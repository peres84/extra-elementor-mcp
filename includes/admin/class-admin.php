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
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Admin {

	/**
	 * Initializes admin hooks.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
	}

	/**
	 * Adds the settings page to the admin menu.
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
	 * Renders the settings page.
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
