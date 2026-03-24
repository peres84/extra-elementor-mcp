<?php
/**
 * Yoast SEO MCP abilities.
 *
 * Registers 3 tools for reading and updating Yoast SEO metadata.
 * Only loaded when Yoast SEO is active (defined('WPSEO_VERSION')).
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the Yoast SEO abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Seo_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/get-seo',
			'extra-elementor-mcp/update-seo',
			'extra-elementor-mcp/get-seo-analysis',
		);
	}

	/**
	 * Registers all SEO abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_get_seo();
		$this->register_update_seo();
		$this->register_get_seo_analysis();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check for SEO read/write (edit_posts + ownership).
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input The input data.
	 * @return bool
	 */
	public function check_seo_permission( $input = null ): bool {
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
	// get-seo
	// -------------------------------------------------------------------------

	/**
	 * Registers the get-seo ability.
	 *
	 * @since 1.0.0
	 */
	private function register_get_seo(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/get-seo',
			array(
				'label'               => __( 'Get SEO Metadata', 'extra-elementor-mcp' ),
				'description'         => __( 'Get Yoast SEO metadata for a page/post: title, meta description, focus keyphrase, Open Graph data.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_get_seo' ),
				'permission_callback' => array( $this, 'check_seo_permission' ),
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
						'post_id'        => array( 'type' => 'integer' ),
						'title'          => array( 'type' => 'string' ),
						'description'    => array( 'type' => 'string' ),
						'keyphrase'      => array( 'type' => 'string' ),
						'og_title'       => array( 'type' => 'string' ),
						'og_description' => array( 'type' => 'string' ),
						'og_image_id'    => array( 'type' => 'integer' ),
						'canonical'      => array( 'type' => 'string' ),
						'noindex'        => array( 'type' => 'boolean' ),
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
	 * Executes the get-seo ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_get_seo( array $input ) {
		$post_id = absint( $input['post_id'] );

		if ( ! get_post( $post_id ) ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		$og_image_id = (int) get_post_meta( $post_id, '_yoast_wpseo_opengraph-image-id', true );
		$noindex     = get_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', true );

		return array(
			'post_id'        => $post_id,
			'title'          => get_post_meta( $post_id, '_yoast_wpseo_title', true ),
			'description'    => get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ),
			'keyphrase'      => get_post_meta( $post_id, '_yoast_wpseo_focuskw', true ),
			'og_title'       => get_post_meta( $post_id, '_yoast_wpseo_opengraph-title', true ),
			'og_description' => get_post_meta( $post_id, '_yoast_wpseo_opengraph-description', true ),
			'og_image_id'    => $og_image_id,
			'canonical'      => get_post_meta( $post_id, '_yoast_wpseo_canonical', true ),
			'noindex'        => ! empty( $noindex ) && '1' === $noindex,
		);
	}

	// -------------------------------------------------------------------------
	// update-seo
	// -------------------------------------------------------------------------

	/**
	 * Registers the update-seo ability.
	 *
	 * @since 1.0.0
	 */
	private function register_update_seo(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/update-seo',
			array(
				'label'               => __( 'Update SEO Metadata', 'extra-elementor-mcp' ),
				'description'         => __( 'Set Yoast SEO title, meta description, focus keyphrase, and Open Graph data for a page/post.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_update_seo' ),
				'permission_callback' => array( $this, 'check_seo_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'        => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post to update.', 'extra-elementor-mcp' ),
						),
						'title'          => array(
							'type'        => 'string',
							'description' => __( 'SEO title (shown in search results).', 'extra-elementor-mcp' ),
						),
						'description'    => array(
							'type'        => 'string',
							'description' => __( 'Meta description (shown in search results).', 'extra-elementor-mcp' ),
						),
						'keyphrase'      => array(
							'type'        => 'string',
							'description' => __( 'Focus keyphrase for SEO analysis.', 'extra-elementor-mcp' ),
						),
						'og_title'       => array(
							'type'        => 'string',
							'description' => __( 'Open Graph title (for social sharing).', 'extra-elementor-mcp' ),
						),
						'og_description' => array(
							'type'        => 'string',
							'description' => __( 'Open Graph description.', 'extra-elementor-mcp' ),
						),
						'og_image_id'    => array(
							'type'        => 'integer',
							'description' => __( 'Attachment ID for the Open Graph image.', 'extra-elementor-mcp' ),
						),
						'canonical'      => array(
							'type'        => 'string',
							'description' => __( 'Canonical URL override.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array( 'type' => 'integer' ),
						'updated' => array(
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
	 * Executes the update-seo ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_update_seo( array $input ) {
		$post_id = absint( $input['post_id'] );

		if ( ! get_post( $post_id ) ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		$field_map = array(
			'title'          => '_yoast_wpseo_title',
			'description'    => '_yoast_wpseo_metadesc',
			'keyphrase'      => '_yoast_wpseo_focuskw',
			'og_title'       => '_yoast_wpseo_opengraph-title',
			'og_description' => '_yoast_wpseo_opengraph-description',
			'og_image_id'    => '_yoast_wpseo_opengraph-image-id',
			'canonical'      => '_yoast_wpseo_canonical',
		);

		$updated = array();
		foreach ( $field_map as $input_key => $meta_key ) {
			if ( isset( $input[ $input_key ] ) ) {
				$value = 'og_image_id' === $input_key
					? absint( $input[ $input_key ] )
					: sanitize_text_field( $input[ $input_key ] );
				update_post_meta( $post_id, $meta_key, $value );
				$updated[] = $input_key;
			}
		}

		return array(
			'post_id' => $post_id,
			'updated' => $updated,
		);
	}

	// -------------------------------------------------------------------------
	// get-seo-analysis
	// -------------------------------------------------------------------------

	/**
	 * Registers the get-seo-analysis ability.
	 *
	 * @since 1.0.0
	 */
	private function register_get_seo_analysis(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/get-seo-analysis',
			array(
				'label'               => __( 'Get SEO Analysis', 'extra-elementor-mcp' ),
				'description'         => __( 'Get Yoast SEO readability and SEO analysis scores for a page/post.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_get_seo_analysis' ),
				'permission_callback' => array( $this, 'check_seo_permission' ),
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
						'post_id'           => array( 'type' => 'integer' ),
						'seo_score'         => array( 'type' => 'string' ),
						'readability_score' => array( 'type' => 'string' ),
						'seo_grade'         => array( 'type' => 'string' ),
						'readability_grade' => array( 'type' => 'string' ),
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
	 * Executes the get-seo-analysis ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_get_seo_analysis( array $input ) {
		$post_id = absint( $input['post_id'] );

		if ( ! get_post( $post_id ) ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		// Yoast stores scores as integers in post meta.
		$seo_score         = (int) get_post_meta( $post_id, '_yoast_wpseo_linkdex', true );
		$readability_score = (int) get_post_meta( $post_id, '_yoast_wpseo_content_score', true );

		return array(
			'post_id'           => $post_id,
			'seo_score'         => (string) $seo_score,
			'readability_score' => (string) $readability_score,
			'seo_grade'         => $this->score_to_grade( $seo_score ),
			'readability_grade' => $this->score_to_grade( $readability_score ),
		);
	}

	/**
	 * Converts a Yoast numeric score to a letter grade.
	 *
	 * @since 1.0.0
	 *
	 * @param int $score Score 0-100.
	 * @return string Grade: bad, ok, good, or na.
	 */
	private function score_to_grade( int $score ): string {
		if ( 0 === $score ) {
			return 'na';
		}
		if ( $score <= 40 ) {
			return 'bad';
		}
		if ( $score <= 70 ) {
			return 'ok';
		}
		return 'good';
	}
}
