<?php
/**
 * Media Library MCP abilities.
 *
 * Registers 3 tools for listing, uploading, and updating media library items.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the media library abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Media_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/list-media',
			'extra-elementor-mcp/upload-media',
			'extra-elementor-mcp/update-media-meta',
		);
	}

	/**
	 * Registers all media abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_list_media();
		$this->register_upload_media();
		$this->register_update_media_meta();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check for upload_files capability.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_upload_permission(): bool {
		return current_user_can( 'upload_files' );
	}

	// -------------------------------------------------------------------------
	// list-media
	// -------------------------------------------------------------------------

	/**
	 * Registers the list-media ability.
	 *
	 * @since 1.0.0
	 */
	private function register_list_media(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/list-media',
			array(
				'label'               => __( 'List Media', 'extra-elementor-mcp' ),
				'description'         => __( 'List media library items with optional filters by type, search term, and pagination.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_list_media' ),
				'permission_callback' => array( $this, 'check_upload_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'type'     => array(
							'type'        => 'string',
							'enum'        => array( 'image', 'video', 'audio', 'application' ),
							'description' => __( 'Filter by MIME type category.', 'extra-elementor-mcp' ),
						),
						'search'   => array(
							'type'        => 'string',
							'description' => __( 'Search term to filter media by title or caption.', 'extra-elementor-mcp' ),
						),
						'per_page' => array(
							'type'        => 'integer',
							'description' => __( 'Number of items per page (default 20, max 100).', 'extra-elementor-mcp' ),
						),
						'page'     => array(
							'type'        => 'integer',
							'description' => __( 'Page number (default 1).', 'extra-elementor-mcp' ),
						),
					),
				),
				'output_schema'       => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'id'            => array( 'type' => 'integer' ),
							'title'         => array( 'type' => 'string' ),
							'url'           => array( 'type' => 'string' ),
							'thumbnail_url' => array( 'type' => 'string' ),
							'alt_text'      => array( 'type' => 'string' ),
							'caption'       => array( 'type' => 'string' ),
							'mime_type'     => array( 'type' => 'string' ),
							'file_size'     => array( 'type' => 'integer' ),
							'width'         => array( 'type' => 'integer' ),
							'height'        => array( 'type' => 'integer' ),
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
	 * Executes the list-media ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_list_media( array $input ): array {
		$per_page = min( absint( $input['per_page'] ?? 20 ), 100 );
		$page     = max( absint( $input['page'] ?? 1 ), 1 );

		$query_args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => $per_page,
			'paged'          => $page,
		);

		if ( ! empty( $input['type'] ) ) {
			$query_args['post_mime_type'] = sanitize_text_field( $input['type'] );
		}

		if ( ! empty( $input['search'] ) ) {
			$query_args['s'] = sanitize_text_field( $input['search'] );
		}

		$query = new WP_Query( $query_args );
		$items = array();

		foreach ( $query->posts as $post ) {
			$metadata      = wp_get_attachment_metadata( $post->ID );
			$thumbnail_url = wp_get_attachment_image_url( $post->ID, 'thumbnail' );

			$items[] = array(
				'id'            => $post->ID,
				'title'         => $post->post_title,
				'url'           => wp_get_attachment_url( $post->ID ),
				'thumbnail_url' => $thumbnail_url ?: wp_get_attachment_url( $post->ID ),
				'alt_text'      => get_post_meta( $post->ID, '_wp_attachment_image_alt', true ),
				'caption'       => wp_get_attachment_caption( $post->ID ),
				'mime_type'     => $post->post_mime_type,
				'file_size'     => (int) ( $metadata['filesize'] ?? 0 ),
				'width'         => (int) ( $metadata['width'] ?? 0 ),
				'height'        => (int) ( $metadata['height'] ?? 0 ),
			);
		}

		return $items;
	}

	// -------------------------------------------------------------------------
	// upload-media
	// -------------------------------------------------------------------------

	/**
	 * Registers the upload-media ability.
	 *
	 * @since 1.0.0
	 */
	private function register_upload_media(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/upload-media',
			array(
				'label'               => __( 'Upload Media', 'extra-elementor-mcp' ),
				'description'         => __( 'Upload a file from base64-encoded data to the WordPress media library.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_upload_media' ),
				'permission_callback' => array( $this, 'check_upload_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'file_data' => array(
							'type'        => 'string',
							'description' => __( 'Base64-encoded file content.', 'extra-elementor-mcp' ),
						),
						'filename'  => array(
							'type'        => 'string',
							'description' => __( 'Filename including extension (e.g., "image.jpg").', 'extra-elementor-mcp' ),
						),
						'alt_text'  => array(
							'type'        => 'string',
							'description' => __( 'Alt text for the uploaded image.', 'extra-elementor-mcp' ),
						),
						'caption'   => array(
							'type'        => 'string',
							'description' => __( 'Caption for the media item.', 'extra-elementor-mcp' ),
						),
						'title'     => array(
							'type'        => 'string',
							'description' => __( 'Title for the media item.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'file_data', 'filename' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'attachment_id' => array( 'type' => 'integer' ),
						'url'           => array( 'type' => 'string' ),
						'mime_type'     => array( 'type' => 'string' ),
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
	 * Executes the upload-media ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_upload_media( array $input ) {
		$file_data = $input['file_data'];
		$filename  = sanitize_file_name( $input['filename'] );

		// Decode base64 data (strip optional data-URL prefix).
		if ( strpos( $file_data, ',' ) !== false ) {
			$file_data = explode( ',', $file_data, 2 )[1];
		}
		$decoded = base64_decode( $file_data, true ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		if ( false === $decoded ) {
			return new \WP_Error( 'invalid_data', __( 'Invalid base64 file data.', 'extra-elementor-mcp' ) );
		}

		// Write to a temp file.
		$tmp_file = wp_tempnam( $filename );
		if ( false === file_put_contents( $tmp_file, $decoded ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			return new \WP_Error( 'write_error', __( 'Could not write temporary file.', 'extra-elementor-mcp' ) );
		}

		// Check MIME type against WordPress allowed types.
		$file_array = array(
			'name'     => $filename,
			'tmp_name' => $tmp_file,
		);

		$check = wp_check_filetype_and_ext( $tmp_file, $filename );
		if ( empty( $check['type'] ) ) {
			@unlink( $tmp_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return new \WP_Error( 'invalid_type', __( 'File type not allowed.', 'extra-elementor-mcp' ) );
		}

		// Use media_handle_sideload to insert into media library.
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$post_data = array();
		if ( isset( $input['title'] ) ) {
			$post_data['post_title'] = sanitize_text_field( $input['title'] );
		}
		if ( isset( $input['caption'] ) ) {
			$post_data['post_excerpt'] = sanitize_text_field( $input['caption'] );
		}

		$attachment_id = media_handle_sideload( $file_array, 0, null, $post_data );

		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			return $attachment_id;
		}

		if ( isset( $input['alt_text'] ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $input['alt_text'] ) );
		}

		return array(
			'attachment_id' => $attachment_id,
			'url'           => wp_get_attachment_url( $attachment_id ),
			'mime_type'     => get_post_mime_type( $attachment_id ),
		);
	}

	// -------------------------------------------------------------------------
	// update-media-meta
	// -------------------------------------------------------------------------

	/**
	 * Registers the update-media-meta ability.
	 *
	 * @since 1.0.0
	 */
	private function register_update_media_meta(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/update-media-meta',
			array(
				'label'               => __( 'Update Media Metadata', 'extra-elementor-mcp' ),
				'description'         => __( 'Update alt text, caption, title, and description on an existing media attachment.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_update_media_meta' ),
				'permission_callback' => array( $this, 'check_upload_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'attachment_id' => array(
							'type'        => 'integer',
							'description' => __( 'The attachment ID to update.', 'extra-elementor-mcp' ),
						),
						'alt_text'      => array(
							'type'        => 'string',
							'description' => __( 'Alt text for the image.', 'extra-elementor-mcp' ),
						),
						'caption'       => array(
							'type'        => 'string',
							'description' => __( 'Caption for the media item.', 'extra-elementor-mcp' ),
						),
						'title'         => array(
							'type'        => 'string',
							'description' => __( 'Title of the media item.', 'extra-elementor-mcp' ),
						),
						'description'   => array(
							'type'        => 'string',
							'description' => __( 'Description/content of the media item.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'attachment_id' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'attachment_id' => array( 'type' => 'integer' ),
						'updated'       => array(
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
	 * Executes the update-media-meta ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_update_media_meta( array $input ) {
		$attachment_id = absint( $input['attachment_id'] );
		$post          = get_post( $attachment_id );

		if ( ! $post || 'attachment' !== $post->post_type ) {
			return new \WP_Error( 'not_found', __( 'Attachment not found.', 'extra-elementor-mcp' ) );
		}

		$post_data = array( 'ID' => $attachment_id );
		$updated   = array();

		if ( isset( $input['title'] ) ) {
			$post_data['post_title'] = sanitize_text_field( $input['title'] );
			$updated[]               = 'title';
		}

		if ( isset( $input['caption'] ) ) {
			$post_data['post_excerpt'] = sanitize_text_field( $input['caption'] );
			$updated[]                 = 'caption';
		}

		if ( isset( $input['description'] ) ) {
			$post_data['post_content'] = wp_kses_post( $input['description'] );
			$updated[]                 = 'description';
		}

		if ( count( $post_data ) > 1 ) {
			wp_update_post( $post_data );
		}

		if ( isset( $input['alt_text'] ) ) {
			update_post_meta( $attachment_id, '_wp_attachment_image_alt', sanitize_text_field( $input['alt_text'] ) );
			$updated[] = 'alt_text';
		}

		return array(
			'attachment_id' => $attachment_id,
			'updated'       => $updated,
		);
	}
}
