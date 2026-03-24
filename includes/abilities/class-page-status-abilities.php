<?php
/**
 * Page/Post Status MCP abilities.
 *
 * Registers 3 tools for managing WordPress page and post status,
 * metadata, slugs, templates, and featured images.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the page status abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Page_Status_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/publish-page',
			'extra-elementor-mcp/get-page-info',
			'extra-elementor-mcp/update-page-meta',
		);
	}

	/**
	 * Registers all page status abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_publish_page();
		$this->register_get_page_info();
		$this->register_update_page_meta();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check for publish-page (requires publish_pages capability).
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input The input data.
	 * @return bool
	 */
	public function check_publish_permission( $input = null ): bool {
		if ( ! current_user_can( 'publish_pages' ) ) {
			return false;
		}

		$post_id = absint( $input['post_id'] ?? 0 );
		if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Permission check for get-page-info (requires edit_posts capability).
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input The input data.
	 * @return bool
	 */
	public function check_read_permission( $input = null ): bool {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		$post_id = absint( $input['post_id'] ?? 0 );
		if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Permission check for update-page-meta (requires edit_posts capability).
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input The input data.
	 * @return bool
	 */
	public function check_edit_permission( $input = null ): bool {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		$post_id = absint( $input['post_id'] ?? 0 );
		if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}

	// -------------------------------------------------------------------------
	// publish-page
	// -------------------------------------------------------------------------

	/**
	 * Registers the publish-page ability.
	 *
	 * @since 1.0.0
	 */
	private function register_publish_page(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/publish-page',
			array(
				'label'               => __( 'Publish / Change Page Status', 'extra-elementor-mcp' ),
				'description'         => __( 'Publish a draft page/post or change its status (publish, draft, pending, private).', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_publish_page' ),
				'permission_callback' => array( $this, 'check_publish_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post to update.', 'extra-elementor-mcp' ),
						),
						'status'  => array(
							'type'        => 'string',
							'enum'        => array( 'publish', 'draft', 'pending', 'private' ),
							'description' => __( 'The new post status.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id', 'status' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array( 'type' => 'integer' ),
						'status'  => array( 'type' => 'string' ),
						'title'   => array( 'type' => 'string' ),
						'url'     => array( 'type' => 'string' ),
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
	 * Executes the publish-page ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_publish_page( array $input ) {
		$post_id = absint( $input['post_id'] );
		$status  = sanitize_text_field( $input['status'] );

		$allowed_statuses = array( 'publish', 'draft', 'pending', 'private' );
		if ( ! in_array( $status, $allowed_statuses, true ) ) {
			return new \WP_Error( 'invalid_status', __( 'Invalid post status.', 'extra-elementor-mcp' ) );
		}

		$post = get_post( $post_id );
		if ( ! $post ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		$result = wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => $status,
			),
			true
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return array(
			'post_id' => $post_id,
			'status'  => $status,
			'title'   => get_the_title( $post_id ),
			'url'     => get_permalink( $post_id ),
		);
	}

	// -------------------------------------------------------------------------
	// get-page-info
	// -------------------------------------------------------------------------

	/**
	 * Registers the get-page-info ability.
	 *
	 * @since 1.0.0
	 */
	private function register_get_page_info(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/get-page-info',
			array(
				'label'               => __( 'Get Page Info', 'extra-elementor-mcp' ),
				'description'         => __( 'Get page metadata: status, slug, template, parent, menu order, and featured image.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_get_page_info' ),
				'permission_callback' => array( $this, 'check_read_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'            => array( 'type' => 'integer' ),
						'title'              => array( 'type' => 'string' ),
						'status'             => array( 'type' => 'string' ),
						'slug'               => array( 'type' => 'string' ),
						'url'                => array( 'type' => 'string' ),
						'template'           => array( 'type' => 'string' ),
						'parent_id'          => array( 'type' => 'integer' ),
						'menu_order'         => array( 'type' => 'integer' ),
						'featured_image_id'  => array( 'type' => 'integer' ),
						'featured_image_url' => array( 'type' => 'string' ),
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
	 * Executes the get-page-info ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_get_page_info( array $input ) {
		$post_id = absint( $input['post_id'] );
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		$template           = get_post_meta( $post_id, '_wp_page_template', true );
		$featured_image_id  = (int) get_post_thumbnail_id( $post_id );
		$featured_image_url = $featured_image_id ? wp_get_attachment_url( $featured_image_id ) : '';

		return array(
			'post_id'            => $post_id,
			'title'              => get_the_title( $post_id ),
			'status'             => $post->post_status,
			'slug'               => $post->post_name,
			'url'                => get_permalink( $post_id ),
			'template'           => $template ?: 'default',
			'parent_id'          => (int) $post->post_parent,
			'menu_order'         => (int) $post->menu_order,
			'featured_image_id'  => $featured_image_id,
			'featured_image_url' => $featured_image_url ?: '',
		);
	}

	// -------------------------------------------------------------------------
	// update-page-meta
	// -------------------------------------------------------------------------

	/**
	 * Registers the update-page-meta ability.
	 *
	 * @since 1.0.0
	 */
	private function register_update_page_meta(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/update-page-meta',
			array(
				'label'               => __( 'Update Page Meta', 'extra-elementor-mcp' ),
				'description'         => __( 'Update page settings: slug, parent, menu order, featured image, and page template.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_update_page_meta' ),
				'permission_callback' => array( $this, 'check_edit_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'           => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post to update.', 'extra-elementor-mcp' ),
						),
						'slug'              => array(
							'type'        => 'string',
							'description' => __( 'The post slug (URL path segment).', 'extra-elementor-mcp' ),
						),
						'parent_id'         => array(
							'type'        => 'integer',
							'description' => __( 'ID of the parent page (0 for top-level).', 'extra-elementor-mcp' ),
						),
						'menu_order'        => array(
							'type'        => 'integer',
							'description' => __( 'Menu order for ordering pages.', 'extra-elementor-mcp' ),
						),
						'featured_image_id' => array(
							'type'        => 'integer',
							'description' => __( 'Attachment ID for the featured image (0 to remove).', 'extra-elementor-mcp' ),
						),
						'template'          => array(
							'type'        => 'string',
							'description' => __( 'Page template filename (e.g. "full-width.php") or "default".', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'    => array( 'type' => 'integer' ),
						'updated'    => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
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
	 * Executes the update-page-meta ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_update_page_meta( array $input ) {
		$post_id = absint( $input['post_id'] );
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		$post_data = array( 'ID' => $post_id );
		$updated   = array();

		if ( isset( $input['slug'] ) ) {
			$post_data['post_name'] = sanitize_title( $input['slug'] );
			$updated[]              = 'slug';
		}

		if ( isset( $input['parent_id'] ) ) {
			$post_data['post_parent'] = absint( $input['parent_id'] );
			$updated[]                = 'parent_id';
		}

		if ( isset( $input['menu_order'] ) ) {
			$post_data['menu_order'] = (int) $input['menu_order'];
			$updated[]               = 'menu_order';
		}

		if ( count( $post_data ) > 1 ) {
			$result = wp_update_post( $post_data, true );
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		if ( isset( $input['featured_image_id'] ) ) {
			$image_id = absint( $input['featured_image_id'] );
			if ( 0 === $image_id ) {
				delete_post_thumbnail( $post_id );
			} else {
				set_post_thumbnail( $post_id, $image_id );
			}
			$updated[] = 'featured_image_id';
		}

		if ( isset( $input['template'] ) ) {
			$template = sanitize_text_field( $input['template'] );
			update_post_meta( $post_id, '_wp_page_template', $template );
			$updated[] = 'template';
		}

		return array(
			'post_id' => $post_id,
			'updated' => $updated,
		);
	}
}
