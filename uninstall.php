<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Removes all options stored by Extra MCP Tools for Elementor so the database
 * is left clean after removal.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

// Exit if not called by WordPress during plugin uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin settings option.
delete_option( 'extra_elementor_mcp_settings' );
