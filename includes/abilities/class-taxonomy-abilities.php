<?php
/**
 * Taxonomy MCP abilities.
 *
 * Registers 4 tools for listing and creating categories and tags.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the taxonomy abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Taxonomy_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/list-categories',
			'extra-elementor-mcp/create-category',
			'extra-elementor-mcp/list-tags',
			'extra-elementor-mcp/create-tag',
		);
	}

	/**
	 * Registers all taxonomy abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_list_categories();
		$this->register_create_category();
		$this->register_list_tags();
		$this->register_create_tag();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check for reading taxonomies (edit_posts).
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_read_permission(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Permission check for creating taxonomy terms (manage_categories).
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_create_permission(): bool {
		return current_user_can( 'manage_categories' );
	}

	// -------------------------------------------------------------------------
	// list-categories
	// -------------------------------------------------------------------------

	/**
	 * Registers the list-categories ability.
	 *
	 * @since 1.0.0
	 */
	private function register_list_categories(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/list-categories',
			array(
				'label'               => __( 'List Categories', 'extra-elementor-mcp' ),
				'description'         => __( 'List all categories with their hierarchy (parent/child relationships).', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_list_categories' ),
				'permission_callback' => array( $this, 'check_read_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'hide_empty' => array(
							'type'        => 'boolean',
							'description' => __( 'Whether to hide categories with no posts (default false).', 'extra-elementor-mcp' ),
						),
					),
				),
				'output_schema'       => array(
					'type'  => 'array',
					'items' => array( 'type' => 'object' ),
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
	 * Executes the list-categories ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_list_categories( array $input ): array {
		$hide_empty = (bool) ( $input['hide_empty'] ?? false );

		$categories = get_categories(
			array(
				'hide_empty' => $hide_empty,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		$cats_by_id = array();
		foreach ( $categories as $cat ) {
			$cats_by_id[ $cat->term_id ] = array(
				'id'          => (int) $cat->term_id,
				'name'        => $cat->name,
				'slug'        => $cat->slug,
				'description' => $cat->description,
				'parent_id'   => (int) $cat->parent,
				'count'       => (int) $cat->count,
				'children'    => array(),
			);
		}

		$tree = array();
		foreach ( $cats_by_id as $id => &$cat ) {
			if ( $cat['parent_id'] && isset( $cats_by_id[ $cat['parent_id'] ] ) ) {
				$cats_by_id[ $cat['parent_id'] ]['children'][] = &$cat;
			} else {
				$tree[] = &$cat;
			}
		}
		unset( $cat );

		return $tree;
	}

	// -------------------------------------------------------------------------
	// create-category
	// -------------------------------------------------------------------------

	/**
	 * Registers the create-category ability.
	 *
	 * @since 1.0.0
	 */
	private function register_create_category(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/create-category',
			array(
				'label'               => __( 'Create Category', 'extra-elementor-mcp' ),
				'description'         => __( 'Create a new category with optional slug, parent, and description.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_create_category' ),
				'permission_callback' => array( $this, 'check_create_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'name'        => array(
							'type'        => 'string',
							'description' => __( 'Category name.', 'extra-elementor-mcp' ),
						),
						'slug'        => array(
							'type'        => 'string',
							'description' => __( 'URL slug (auto-generated from name if omitted).', 'extra-elementor-mcp' ),
						),
						'parent_id'   => array(
							'type'        => 'integer',
							'description' => __( 'Parent category ID (0 for top-level).', 'extra-elementor-mcp' ),
						),
						'description' => array(
							'type'        => 'string',
							'description' => __( 'Category description.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'name' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'   => array( 'type' => 'integer' ),
						'name' => array( 'type' => 'string' ),
						'slug' => array( 'type' => 'string' ),
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
	 * Executes the create-category ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_create_category( array $input ) {
		$args = array(
			'cat_name' => sanitize_text_field( $input['name'] ),
		);

		if ( isset( $input['slug'] ) ) {
			$args['category_nicename'] = sanitize_title( $input['slug'] );
		}
		if ( isset( $input['parent_id'] ) ) {
			$args['category_parent'] = absint( $input['parent_id'] );
		}
		if ( isset( $input['description'] ) ) {
			$args['category_description'] = sanitize_textarea_field( $input['description'] );
		}

		$cat_id = wp_insert_category( $args, true );

		if ( is_wp_error( $cat_id ) ) {
			return $cat_id;
		}

		$term = get_term( $cat_id, 'category' );

		return array(
			'id'   => (int) $term->term_id,
			'name' => $term->name,
			'slug' => $term->slug,
		);
	}

	// -------------------------------------------------------------------------
	// list-tags
	// -------------------------------------------------------------------------

	/**
	 * Registers the list-tags ability.
	 *
	 * @since 1.0.0
	 */
	private function register_list_tags(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/list-tags',
			array(
				'label'               => __( 'List Tags', 'extra-elementor-mcp' ),
				'description'         => __( 'List all post tags with optional search filter.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_list_tags' ),
				'permission_callback' => array( $this, 'check_read_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'hide_empty' => array(
							'type'        => 'boolean',
							'description' => __( 'Whether to hide tags with no posts (default false).', 'extra-elementor-mcp' ),
						),
						'search'     => array(
							'type'        => 'string',
							'description' => __( 'Search term to filter tags by name.', 'extra-elementor-mcp' ),
						),
					),
				),
				'output_schema'       => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'id'          => array( 'type' => 'integer' ),
							'name'        => array( 'type' => 'string' ),
							'slug'        => array( 'type' => 'string' ),
							'description' => array( 'type' => 'string' ),
							'count'       => array( 'type' => 'integer' ),
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
	 * Executes the list-tags ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_list_tags( array $input ): array {
		$args = array(
			'hide_empty' => (bool) ( $input['hide_empty'] ?? false ),
			'orderby'    => 'name',
			'order'      => 'ASC',
		);

		if ( ! empty( $input['search'] ) ) {
			$args['search'] = sanitize_text_field( $input['search'] );
		}

		$tags   = get_tags( $args );
		$result = array();

		foreach ( $tags as $tag ) {
			$result[] = array(
				'id'          => (int) $tag->term_id,
				'name'        => $tag->name,
				'slug'        => $tag->slug,
				'description' => $tag->description,
				'count'       => (int) $tag->count,
			);
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	// create-tag
	// -------------------------------------------------------------------------

	/**
	 * Registers the create-tag ability.
	 *
	 * @since 1.0.0
	 */
	private function register_create_tag(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/create-tag',
			array(
				'label'               => __( 'Create Tag', 'extra-elementor-mcp' ),
				'description'         => __( 'Create a new post tag with optional slug and description.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_create_tag' ),
				'permission_callback' => array( $this, 'check_create_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'name'        => array(
							'type'        => 'string',
							'description' => __( 'Tag name.', 'extra-elementor-mcp' ),
						),
						'slug'        => array(
							'type'        => 'string',
							'description' => __( 'URL slug (auto-generated from name if omitted).', 'extra-elementor-mcp' ),
						),
						'description' => array(
							'type'        => 'string',
							'description' => __( 'Tag description.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'name' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'id'   => array( 'type' => 'integer' ),
						'name' => array( 'type' => 'string' ),
						'slug' => array( 'type' => 'string' ),
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
	 * Executes the create-tag ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_create_tag( array $input ) {
		$name = sanitize_text_field( $input['name'] );
		$args = array();

		if ( isset( $input['slug'] ) ) {
			$args['slug'] = sanitize_title( $input['slug'] );
		}
		if ( isset( $input['description'] ) ) {
			$args['description'] = sanitize_textarea_field( $input['description'] );
		}

		$result = wp_insert_term( $name, 'post_tag', $args );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$term = get_term( $result['term_id'], 'post_tag' );

		return array(
			'id'   => (int) $term->term_id,
			'name' => $term->name,
			'slug' => $term->slug,
		);
	}
}
