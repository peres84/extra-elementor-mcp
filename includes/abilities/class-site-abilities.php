<?php
/**
 * Site Info & Settings MCP abilities.
 *
 * Registers 3 tools for reading and updating WordPress site settings.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the site info & settings abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Site_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/get-site-info',
			'extra-elementor-mcp/update-site-settings',
			'extra-elementor-mcp/get-reading-settings',
		);
	}

	/**
	 * Registers all site abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_get_site_info();
		$this->register_update_site_settings();
		$this->register_get_reading_settings();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check requiring manage_options capability.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_manage_options(): bool {
		return current_user_can( 'manage_options' );
	}

	// -------------------------------------------------------------------------
	// get-site-info
	// -------------------------------------------------------------------------

	/**
	 * Registers the get-site-info ability.
	 *
	 * @since 1.0.0
	 */
	private function register_get_site_info(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/get-site-info',
			array(
				'label'               => __( 'Get Site Info', 'extra-elementor-mcp' ),
				'description'         => __( 'Get a comprehensive site overview: title, tagline, URL, admin email, timezone, language, active theme, active plugins, WordPress version, and PHP version.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_get_site_info' ),
				'permission_callback' => array( $this, 'check_manage_options' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'title'          => array( 'type' => 'string' ),
						'tagline'        => array( 'type' => 'string' ),
						'url'            => array( 'type' => 'string' ),
						'admin_email'    => array( 'type' => 'string' ),
						'timezone'       => array( 'type' => 'string' ),
						'language'       => array( 'type' => 'string' ),
						'wp_version'     => array( 'type' => 'string' ),
						'php_version'    => array( 'type' => 'string' ),
						'active_theme'   => array( 'type' => 'string' ),
						'active_plugins' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
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
	 * Executes the get-site-info ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_get_site_info( array $input ): array {
		$theme          = wp_get_theme();
		$active_plugins = get_option( 'active_plugins', array() );

		// Humanize plugin slugs to names.
		$plugin_names = array();
		foreach ( $active_plugins as $plugin_file ) {
			$plugin_data    = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, false, false );
			$plugin_names[] = $plugin_data['Name'] ?: $plugin_file;
		}

		return array(
			'title'          => get_bloginfo( 'name' ),
			'tagline'        => get_bloginfo( 'description' ),
			'url'            => get_bloginfo( 'url' ),
			'admin_email'    => get_bloginfo( 'admin_email' ),
			'timezone'       => wp_timezone_string(),
			'language'       => get_bloginfo( 'language' ),
			'wp_version'     => get_bloginfo( 'version' ),
			'php_version'    => PHP_VERSION,
			'active_theme'   => $theme->get( 'Name' ),
			'active_plugins' => $plugin_names,
		);
	}

	// -------------------------------------------------------------------------
	// update-site-settings
	// -------------------------------------------------------------------------

	/**
	 * Registers the update-site-settings ability.
	 *
	 * @since 1.0.0
	 */
	private function register_update_site_settings(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/update-site-settings',
			array(
				'label'               => __( 'Update Site Settings', 'extra-elementor-mcp' ),
				'description'         => __( 'Update site title, tagline, homepage (static page), posts page, and front page display setting.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_update_site_settings' ),
				'permission_callback' => array( $this, 'check_manage_options' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'title'         => array(
							'type'        => 'string',
							'description' => __( 'Site title.', 'extra-elementor-mcp' ),
						),
						'tagline'       => array(
							'type'        => 'string',
							'description' => __( 'Site tagline/description.', 'extra-elementor-mcp' ),
						),
						'show_on_front' => array(
							'type'        => 'string',
							'enum'        => array( 'posts', 'page' ),
							'description' => __( 'What to show on the front page: "posts" (latest posts) or "page" (static page).', 'extra-elementor-mcp' ),
						),
						'page_on_front' => array(
							'type'        => 'integer',
							'description' => __( 'ID of the page to use as the static front page (requires show_on_front = "page").', 'extra-elementor-mcp' ),
						),
						'page_for_posts' => array(
							'type'        => 'integer',
							'description' => __( 'ID of the page to use as the posts archive page.', 'extra-elementor-mcp' ),
						),
					),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
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
	 * Executes the update-site-settings ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_update_site_settings( array $input ): array {
		$updated = array();

		if ( isset( $input['title'] ) ) {
			update_option( 'blogname', sanitize_text_field( $input['title'] ) );
			$updated[] = 'title';
		}

		if ( isset( $input['tagline'] ) ) {
			update_option( 'blogdescription', sanitize_text_field( $input['tagline'] ) );
			$updated[] = 'tagline';
		}

		if ( isset( $input['show_on_front'] ) ) {
			$show = sanitize_text_field( $input['show_on_front'] );
			if ( in_array( $show, array( 'posts', 'page' ), true ) ) {
				update_option( 'show_on_front', $show );
				$updated[] = 'show_on_front';
			}
		}

		if ( isset( $input['page_on_front'] ) ) {
			update_option( 'page_on_front', absint( $input['page_on_front'] ) );
			$updated[] = 'page_on_front';
		}

		if ( isset( $input['page_for_posts'] ) ) {
			update_option( 'page_for_posts', absint( $input['page_for_posts'] ) );
			$updated[] = 'page_for_posts';
		}

		return array( 'updated' => $updated );
	}

	// -------------------------------------------------------------------------
	// get-reading-settings
	// -------------------------------------------------------------------------

	/**
	 * Registers the get-reading-settings ability.
	 *
	 * @since 1.0.0
	 */
	private function register_get_reading_settings(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/get-reading-settings',
			array(
				'label'               => __( 'Get Reading Settings', 'extra-elementor-mcp' ),
				'description'         => __( 'Get homepage display settings (latest posts vs static page) and posts per page.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_get_reading_settings' ),
				'permission_callback' => array( $this, 'check_manage_options' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'show_on_front'       => array( 'type' => 'string' ),
						'page_on_front'       => array( 'type' => 'integer' ),
						'page_on_front_title' => array( 'type' => 'string' ),
						'page_for_posts'      => array( 'type' => 'integer' ),
						'page_for_posts_title' => array( 'type' => 'string' ),
						'posts_per_page'      => array( 'type' => 'integer' ),
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
	 * Executes the get-reading-settings ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_get_reading_settings( array $input ): array {
		$show_on_front  = get_option( 'show_on_front', 'posts' );
		$page_on_front  = (int) get_option( 'page_on_front', 0 );
		$page_for_posts = (int) get_option( 'page_for_posts', 0 );
		$posts_per_page = (int) get_option( 'posts_per_page', 10 );

		return array(
			'show_on_front'        => $show_on_front,
			'page_on_front'        => $page_on_front,
			'page_on_front_title'  => $page_on_front ? get_the_title( $page_on_front ) : '',
			'page_for_posts'       => $page_for_posts,
			'page_for_posts_title' => $page_for_posts ? get_the_title( $page_for_posts ) : '',
			'posts_per_page'       => $posts_per_page,
		);
	}
}
