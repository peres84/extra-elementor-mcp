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
 * Registers the Settings API option for tool group toggles and renders
 * the settings page view. The static helper {@see Extra_Elementor_MCP_Admin::is_group_enabled()}
 * is intentionally kept separate from the ability registrar so the option
 * can be read without instantiating the Admin class.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Admin {

	/**
	 * WordPress option name for plugin settings.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'extra_elementor_mcp_settings';

	/**
	 * Settings API option group name.
	 *
	 * @var string
	 */
	const SETTINGS_GROUP = 'extra_elementor_mcp_settings_group';

	/**
	 * All tool group keys available for toggling.
	 *
	 * @var string[]
	 */
	const GROUPS = array(
		'page_status',
		'menus',
		'site',
		'media',
		'taxonomies',
		'revisions',
		'seo',
		'acf',
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
	 * Registers the plugin option and settings section via the Settings API.
	 *
	 * @since 1.0.0
	 */
	public function register_settings(): void {
		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => self::get_defaults(),
			)
		);
	}

	/**
	 * Sanitizes and validates the settings before saving.
	 *
	 * Ensures only known group keys are stored and values are cast to integers.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $input Raw input from the settings form.
	 * @return array Sanitized settings array.
	 */
	public function sanitize_settings( $input ): array {
		$sanitized = self::get_defaults();

		if ( ! is_array( $input ) ) {
			return $sanitized;
		}

		$submitted_groups = isset( $input['enabled_groups'] ) && is_array( $input['enabled_groups'] )
			? $input['enabled_groups']
			: array();

		foreach ( self::GROUPS as $group ) {
			// Checkboxes only appear in POST when checked, so absence means disabled.
			$sanitized['enabled_groups'][ $group ] = isset( $submitted_groups[ $group ] ) ? 1 : 0;
		}

		return $sanitized;
	}

	/**
	 * Returns the default settings (all groups enabled).
	 *
	 * @since 1.0.0
	 *
	 * @return array Default settings array.
	 */
	public static function get_defaults(): array {
		$defaults = array( 'enabled_groups' => array() );

		foreach ( self::GROUPS as $group ) {
			$defaults['enabled_groups'][ $group ] = 1;
		}

		return $defaults;
	}

	/**
	 * Checks whether a specific tool group is enabled.
	 *
	 * Defaults to true (enabled) when the option is missing or the group key
	 * does not exist, so a fresh install exposes all tools.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group Tool group key (e.g. 'page_status', 'menus').
	 * @return bool True if the group is enabled.
	 */
	public static function is_group_enabled( string $group ): bool {
		$settings = get_option( self::OPTION_NAME, array() );

		if ( ! isset( $settings['enabled_groups'][ $group ] ) ) {
			return true; // Default: enabled.
		}

		return (bool) $settings['enabled_groups'][ $group ];
	}

	/**
	 * Renders the settings page by loading the view template.
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
