<?php
/**
 * Admin settings page handler.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the plugin settings page in wp-admin.
 *
 * Registers the settings page under Settings → Extra MCP Tools, registers
 * the tool-group toggle options via the WordPress Settings API, and
 * sanitizes all input on save.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Admin {

	/**
	 * Option name used to persist tool group toggles.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'extra_elementor_mcp_enabled_groups';

	/**
	 * All available tool groups and their labels.
	 *
	 * Groups that require an optional dependency (Yoast, ACF) are still listed
	 * here so the admin can see them; the ability registrar also performs the
	 * runtime dependency check before registering.
	 *
	 * @var array<string,string>
	 */
	const TOOL_GROUPS = array(
		'page_status' => 'Page Status (publish-page, get-page-info, update-page-meta)',
		'menus'       => 'Navigation Menus (list-menus, get-menu, update-menu, assign-menu-location)',
		'site'        => 'Site Settings (get-site-info, update-site-settings, get-reading-settings)',
		'media'       => 'Media Library (list-media, upload-media, update-media-meta)',
		'taxonomies'  => 'Taxonomies (list-categories, create-category, list-tags, create-tag)',
		'revisions'   => 'Revisions (list-revisions, restore-revision)',
		'seo'         => 'Yoast SEO (get-seo, update-seo, get-seo-analysis) — requires Yoast SEO plugin',
		'acf'         => 'ACF Custom Fields (list-acf-field-groups, get-acf-fields, update-acf-fields) — requires ACF plugin',
	);

	/**
	 * Initializes admin hooks.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Adds the settings page to the admin menu under Settings.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_page(): void {
		add_options_page(
			__( 'Extra MCP Tools', 'extra-elementor-mcp' ),
			__( 'Extra MCP Tools', 'extra-elementor-mcp' ),
			'manage_options',
			'extra-elementor-mcp',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registers the settings option and its sanitization callback via the
	 * WordPress Settings API.
	 *
	 * @since 1.0.0
	 */
	public function register_settings(): void {
		register_setting(
			'extra_elementor_mcp_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_enabled_groups' ),
				'default'           => $this->get_default_enabled_groups(),
			)
		);
	}

	/**
	 * Sanitizes the enabled groups option.
	 *
	 * Only keys that appear in TOOL_GROUPS are allowed; any unknown key is
	 * silently dropped.  Converts the submitted checkbox values ('1') into a
	 * clean array of enabled group keys.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $input Raw value from the submitted form.
	 * @return string[] Array of enabled group keys.
	 */
	public function sanitize_enabled_groups( $input ): array {
		$enabled = array();

		if ( ! is_array( $input ) ) {
			return $enabled;
		}

		foreach ( array_keys( self::TOOL_GROUPS ) as $group_key ) {
			if ( ! empty( $input[ $group_key ] ) ) {
				$enabled[] = sanitize_key( $group_key );
			}
		}

		return $enabled;
	}

	/**
	 * Returns the default enabled groups (all groups enabled).
	 *
	 * @since 1.0.0
	 *
	 * @return string[] All group keys enabled by default.
	 */
	public function get_default_enabled_groups(): array {
		return array_keys( self::TOOL_GROUPS );
	}

	/**
	 * Returns the currently enabled tool group keys.
	 *
	 * Falls back to all groups enabled when no option has been saved yet.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Array of enabled group keys.
	 */
	public static function get_enabled_groups(): array {
		$saved = get_option( self::OPTION_NAME, null );

		if ( null === $saved ) {
			return array_keys( self::TOOL_GROUPS );
		}

		return is_array( $saved ) ? $saved : array();
	}

	/**
	 * Checks whether a specific tool group is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group_key The group key (e.g. 'page_status', 'menus').
	 * @return bool True if the group is enabled, false otherwise.
	 */
	public static function is_group_enabled( string $group_key ): bool {
		return in_array( $group_key, self::get_enabled_groups(), true );
	}

	/**
	 * Renders the settings page by including the view file.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/admin/views/page-settings.php';
	}
}
