<?php
/**
 * Navigation Menu MCP abilities.
 *
 * Registers 4 tools for listing, reading, updating, and assigning
 * WordPress navigation menus.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the navigation menu abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Menu_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/list-menus',
			'extra-elementor-mcp/get-menu',
			'extra-elementor-mcp/update-menu',
			'extra-elementor-mcp/assign-menu-location',
		);
	}

	/**
	 * Registers all navigation menu abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_list_menus();
		$this->register_get_menu();
		$this->register_update_menu();
		$this->register_assign_menu_location();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check for menu read/write operations.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_menu_permission(): bool {
		return current_user_can( 'edit_theme_options' );
	}

	// -------------------------------------------------------------------------
	// list-menus
	// -------------------------------------------------------------------------

	/**
	 * Registers the list-menus ability.
	 *
	 * @since 1.0.0
	 */
	private function register_list_menus(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/list-menus',
			array(
				'label'               => __( 'List Navigation Menus', 'extra-elementor-mcp' ),
				'description'         => __( 'List all registered navigation menus and their assigned theme locations.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_list_menus' ),
				'permission_callback' => array( $this, 'check_menu_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(),
				),
				'output_schema'       => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'id'        => array( 'type' => 'integer' ),
							'name'      => array( 'type' => 'string' ),
							'slug'      => array( 'type' => 'string' ),
							'count'     => array( 'type' => 'integer' ),
							'locations' => array(
								'type'  => 'array',
								'items' => array( 'type' => 'string' ),
							),
						),
					),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the list-menus ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_list_menus( array $input ): array {
		$menus             = wp_get_nav_menus();
		$assigned_menus    = get_nav_menu_locations();
		$locations_by_menu = array();

		foreach ( $assigned_menus as $location => $menu_id ) {
			if ( ! isset( $locations_by_menu[ $menu_id ] ) ) {
				$locations_by_menu[ $menu_id ] = array();
			}
			$locations_by_menu[ $menu_id ][] = $location;
		}

		$result = array();
		foreach ( $menus as $menu ) {
			$result[] = array(
				'id'        => (int) $menu->term_id,
				'name'      => $menu->name,
				'slug'      => $menu->slug,
				'count'     => (int) $menu->count,
				'locations' => $locations_by_menu[ $menu->term_id ] ?? array(),
			);
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	// get-menu
	// -------------------------------------------------------------------------

	/**
	 * Registers the get-menu ability.
	 *
	 * @since 1.0.0
	 */
	private function register_get_menu(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/get-menu',
			array(
				'label'               => __( 'Get Menu Items', 'extra-elementor-mcp' ),
				'description'         => __( 'Get a menu\'s items with hierarchy (parent/child), URLs, and classes.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_get_menu' ),
				'permission_callback' => array( $this, 'check_menu_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'menu_id' => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the navigation menu.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'menu_id' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'    => array( 'type' => 'integer' ),
						'name'  => array( 'type' => 'string' ),
						'items' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'object' ),
						),
					),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the get-menu ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_get_menu( array $input ) {
		$menu_id = absint( $input['menu_id'] );
		$menu    = wp_get_nav_menu_object( $menu_id );

		if ( ! $menu ) {
			return new \WP_Error( 'not_found', __( 'Menu not found.', 'extra-elementor-mcp' ) );
		}

		$raw_items = wp_get_nav_menu_items( $menu_id );
		if ( ! $raw_items ) {
			$raw_items = array();
		}

		$items = array();
		foreach ( $raw_items as $item ) {
			$items[] = array(
				'id'        => (int) $item->ID,
				'title'     => $item->title,
				'url'       => $item->url,
				'type'      => $item->type,
				'object'    => $item->object,
				'object_id' => (int) $item->object_id,
				'parent_id' => (int) $item->menu_item_parent,
				'position'  => (int) $item->menu_order,
				'classes'   => $item->classes,
				'target'    => $item->target,
			);
		}

		// Build tree structure.
		$items_by_id = array();
		foreach ( $items as &$item ) {
			$items_by_id[ $item['id'] ] = &$item;
			$item['children']           = array();
		}
		unset( $item );

		$tree = array();
		foreach ( $items as &$item ) {
			if ( $item['parent_id'] && isset( $items_by_id[ $item['parent_id'] ] ) ) {
				$items_by_id[ $item['parent_id'] ]['children'][] = &$item;
			} else {
				$tree[] = &$item;
			}
		}
		unset( $item );

		return array(
			'id'    => (int) $menu->term_id,
			'name'  => $menu->name,
			'items' => $tree,
		);
	}

	// -------------------------------------------------------------------------
	// update-menu
	// -------------------------------------------------------------------------

	/**
	 * Registers the update-menu ability.
	 *
	 * @since 1.0.0
	 */
	private function register_update_menu(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/update-menu',
			array(
				'label'               => __( 'Update Menu Items', 'extra-elementor-mcp' ),
				'description'         => __( 'Update menu items: add, remove, or reorder items. Provide the full desired items array; existing items not in the array will be removed.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_update_menu' ),
				'permission_callback' => array( $this, 'check_menu_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'menu_id' => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the navigation menu to update.', 'extra-elementor-mcp' ),
						),
						'items'   => array(
							'type'        => 'array',
							'description' => __( 'Array of menu items. Each item may have: title, url, type (custom/page/category), object_id, parent_id (item ID of parent), position, classes.', 'extra-elementor-mcp' ),
							'items'       => array(
								'type'       => 'object',
								'properties' => array(
									'id'        => array( 'type' => 'integer', 'description' => __( 'Existing item ID to update (omit to create new).', 'extra-elementor-mcp' ) ),
									'title'     => array( 'type' => 'string' ),
									'url'       => array( 'type' => 'string' ),
									'type'      => array( 'type' => 'string', 'enum' => array( 'custom', 'post_type', 'taxonomy' ) ),
									'object_id' => array( 'type' => 'integer' ),
									'parent_id' => array( 'type' => 'integer' ),
									'position'  => array( 'type' => 'integer' ),
									'classes'   => array( 'type' => 'string' ),
								),
							),
						),
					),
					'required'   => array( 'menu_id', 'items' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'menu_id'       => array( 'type' => 'integer' ),
						'items_updated' => array( 'type' => 'integer' ),
					),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the update-menu ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_update_menu( array $input ) {
		$menu_id = absint( $input['menu_id'] );
		$menu    = wp_get_nav_menu_object( $menu_id );

		if ( ! $menu ) {
			return new \WP_Error( 'not_found', __( 'Menu not found.', 'extra-elementor-mcp' ) );
		}

		$items         = $input['items'] ?? array();
		$items_updated = 0;

		foreach ( $items as $position => $item ) {
			$item_id = absint( $item['id'] ?? 0 );
			$args    = array(
				'menu-item-position' => absint( $item['position'] ?? $position + 1 ),
				'menu-item-status'   => 'publish',
			);

			if ( isset( $item['title'] ) ) {
				$args['menu-item-title'] = sanitize_text_field( $item['title'] );
			}
			if ( isset( $item['url'] ) ) {
				$args['menu-item-url'] = esc_url_raw( $item['url'] );
			}
			if ( isset( $item['type'] ) ) {
				$args['menu-item-type'] = sanitize_text_field( $item['type'] );
			}
			if ( isset( $item['object_id'] ) ) {
				$args['menu-item-object-id'] = absint( $item['object_id'] );
			}
			if ( isset( $item['parent_id'] ) ) {
				$args['menu-item-parent-id'] = absint( $item['parent_id'] );
			}
			if ( isset( $item['classes'] ) ) {
				$args['menu-item-classes'] = sanitize_text_field( $item['classes'] );
			}

			$result = wp_update_nav_menu_item( $menu_id, $item_id, $args );
			if ( ! is_wp_error( $result ) ) {
				++$items_updated;
			}
		}

		return array(
			'menu_id'       => $menu_id,
			'items_updated' => $items_updated,
		);
	}

	// -------------------------------------------------------------------------
	// assign-menu-location
	// -------------------------------------------------------------------------

	/**
	 * Registers the assign-menu-location ability.
	 *
	 * @since 1.0.0
	 */
	private function register_assign_menu_location(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/assign-menu-location',
			array(
				'label'               => __( 'Assign Menu to Location', 'extra-elementor-mcp' ),
				'description'         => __( 'Assign a navigation menu to a registered theme location (e.g., primary, footer).', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_assign_menu_location' ),
				'permission_callback' => array( $this, 'check_menu_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'menu_id'  => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the navigation menu.', 'extra-elementor-mcp' ),
						),
						'location' => array(
							'type'        => 'string',
							'description' => __( 'The theme location slug to assign the menu to (e.g., "primary", "footer").', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'menu_id', 'location' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'menu_id'  => array( 'type' => 'integer' ),
						'location' => array( 'type' => 'string' ),
						'success'  => array( 'type' => 'boolean' ),
					),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the assign-menu-location ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_assign_menu_location( array $input ) {
		$menu_id  = absint( $input['menu_id'] );
		$location = sanitize_text_field( $input['location'] );

		$menu = wp_get_nav_menu_object( $menu_id );
		if ( ! $menu ) {
			return new \WP_Error( 'not_found', __( 'Menu not found.', 'extra-elementor-mcp' ) );
		}

		$registered_locations = get_registered_nav_menus();
		if ( ! isset( $registered_locations[ $location ] ) ) {
			return new \WP_Error(
				'invalid_location',
				sprintf(
					/* translators: %s: location slug */
					__( 'Theme location "%s" is not registered.', 'extra-elementor-mcp' ),
					$location
				)
			);
		}

		$locations              = get_nav_menu_locations();
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		return array(
			'menu_id'  => $menu_id,
			'location' => $location,
			'success'  => true,
		);
	}
}
