<?php
/**
 * Revision History MCP abilities.
 *
 * Registers 2 tools for listing and restoring post revisions.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the revision history abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Revision_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/list-revisions',
			'extra-elementor-mcp/restore-revision',
		);
	}

	/**
	 * Registers all revision abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_list_revisions();
		$this->register_restore_revision();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check for reading revisions (edit_posts).
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
	 * Permission check for restoring revisions (edit_posts + ownership).
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input The input data.
	 * @return bool
	 */
	public function check_restore_permission( $input = null ): bool {
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
	// list-revisions
	// -------------------------------------------------------------------------

	/**
	 * Registers the list-revisions ability.
	 *
	 * @since 1.0.0
	 */
	private function register_list_revisions(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/list-revisions',
			array(
				'label'               => __( 'List Revisions', 'extra-elementor-mcp' ),
				'description'         => __( 'List revision history for a page/post with dates, authors, and a content excerpt.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_list_revisions' ),
				'permission_callback' => array( $this, 'check_read_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'  => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post.', 'extra-elementor-mcp' ),
						),
						'per_page' => array(
							'type'        => 'integer',
							'description' => __( 'Maximum number of revisions to return (default 10).', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id' ),
				),
				'output_schema'       => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'revision_id' => array( 'type' => 'integer' ),
							'date'        => array( 'type' => 'string' ),
							'author'      => array( 'type' => 'string' ),
							'title'       => array( 'type' => 'string' ),
							'excerpt'     => array( 'type' => 'string' ),
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
	 * Executes the list-revisions ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_list_revisions( array $input ) {
		$post_id  = absint( $input['post_id'] );
		$per_page = absint( $input['per_page'] ?? 10 );
		$post     = get_post( $post_id );

		if ( ! $post ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		$revisions = wp_get_post_revisions(
			$post_id,
			array(
				'numberposts' => $per_page,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);

		$result = array();
		foreach ( $revisions as $revision ) {
			$author  = get_the_author_meta( 'display_name', $revision->post_author );
			$excerpt = substr( wp_strip_all_tags( $revision->post_content ), 0, 200 );

			$result[] = array(
				'revision_id' => (int) $revision->ID,
				'date'        => $revision->post_modified,
				'author'      => $author,
				'title'       => $revision->post_title,
				'excerpt'     => $excerpt,
			);
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	// restore-revision
	// -------------------------------------------------------------------------

	/**
	 * Registers the restore-revision ability.
	 *
	 * @since 1.0.0
	 */
	private function register_restore_revision(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/restore-revision',
			array(
				'label'               => __( 'Restore Revision', 'extra-elementor-mcp' ),
				'description'         => __( 'DESTRUCTIVE: Restore a page/post to a specific revision. This will overwrite the current content with the revision content. Use list-revisions first to find the revision_id.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_restore_revision' ),
				'permission_callback' => array( $this, 'check_restore_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'     => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post to restore.', 'extra-elementor-mcp' ),
						),
						'revision_id' => array(
							'type'        => 'integer',
							'description' => __( 'The revision ID to restore (from list-revisions).', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id', 'revision_id' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'     => array( 'type' => 'integer' ),
						'revision_id' => array( 'type' => 'integer' ),
						'restored'    => array( 'type' => 'boolean' ),
					),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the restore-revision ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_restore_revision( array $input ) {
		$post_id     = absint( $input['post_id'] );
		$revision_id = absint( $input['revision_id'] );

		$post     = get_post( $post_id );
		$revision = get_post( $revision_id );

		if ( ! $post ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		if ( ! $revision || 'revision' !== $revision->post_type ) {
			return new \WP_Error( 'revision_not_found', __( 'Revision not found.', 'extra-elementor-mcp' ) );
		}

		// Verify the revision belongs to the given post.
		if ( (int) $revision->post_parent !== $post_id ) {
			return new \WP_Error( 'revision_mismatch', __( 'Revision does not belong to the given post.', 'extra-elementor-mcp' ) );
		}

		$result = wp_restore_post_revision( $revision_id );

		if ( ! $result ) {
			return new \WP_Error( 'restore_failed', __( 'Failed to restore revision.', 'extra-elementor-mcp' ) );
		}

		return array(
			'post_id'     => $post_id,
			'revision_id' => $revision_id,
			'restored'    => true,
		);
	}
}
