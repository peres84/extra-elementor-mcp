<?php
/**
 * Plugin Name:       Extra MCP Tools for Elementor
 * Plugin URI:        https://github.com/peres84/extra-elementor-mcp
 * Description:       Companion plugin providing WordPress core MCP tools (menus, SEO, media, ACF, taxonomies, site settings, revisions) not covered by elementor-mcp.
 * Version:           1.0.0
 * Requires at least: 6.9
 * Tested up to:      6.9
 * Requires PHP:      8.0
 * Author:            peres84
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       extra-elementor-mcp
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'EXTRA_ELEMENTOR_MCP_VERSION', '1.0.0' );
define( 'EXTRA_ELEMENTOR_MCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXTRA_ELEMENTOR_MCP_URL', plugin_dir_url( __FILE__ ) );
define( 'EXTRA_ELEMENTOR_MCP_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Wrapper around wp_register_ability that sanitizes schemas for cross-client compatibility.
 *
 * Strips empty strings from enum arrays (some MCP clients reject them) and
 * ensures empty properties serialize as JSON objects rather than arrays.
 *
 * @since 1.0.0
 *
 * @param string $name The ability name.
 * @param array  $args The ability arguments.
 * @return mixed The result of wp_register_ability().
 */
function extra_elementor_mcp_register_ability( string $name, array $args ) {
	if ( isset( $args['input_schema'] ) && is_array( $args['input_schema'] ) ) {
		$args['input_schema'] = extra_elementor_mcp_sanitize_schema( $args['input_schema'] );
	}
	if ( isset( $args['output_schema'] ) && is_array( $args['output_schema'] ) ) {
		$args['output_schema'] = extra_elementor_mcp_sanitize_schema( $args['output_schema'] );
	}
	return wp_register_ability( $name, $args );
}

/**
 * Recursively removes empty strings from enum arrays in a JSON Schema.
 *
 * @since 1.0.0
 *
 * @param array $schema A JSON Schema array.
 * @return array The sanitized schema.
 */
function extra_elementor_mcp_sanitize_schema( array $schema ): array {
	if ( isset( $schema['enum'] ) && is_array( $schema['enum'] ) ) {
		$schema['enum'] = array_values(
			array_filter(
				$schema['enum'],
				function ( $value ) {
					return '' !== $value;
				}
			)
		);
		if ( empty( $schema['enum'] ) ) {
			unset( $schema['enum'] );
		}
	}

	if ( isset( $schema['properties'] ) && is_array( $schema['properties'] ) ) {
		if ( empty( $schema['properties'] ) ) {
			$schema['properties'] = new \stdClass();
		} else {
			foreach ( $schema['properties'] as $key => $prop ) {
				if ( is_array( $prop ) ) {
					$schema['properties'][ $key ] = extra_elementor_mcp_sanitize_schema( $prop );
				}
			}
		}
	}

	if ( isset( $schema['items'] ) && is_array( $schema['items'] ) ) {
		$schema['items'] = extra_elementor_mcp_sanitize_schema( $schema['items'] );
	}

	foreach ( array( 'allOf', 'oneOf', 'anyOf' ) as $keyword ) {
		if ( isset( $schema[ $keyword ] ) && is_array( $schema[ $keyword ] ) ) {
			foreach ( $schema[ $keyword ] as $i => $sub ) {
				if ( is_array( $sub ) ) {
					$schema[ $keyword ][ $i ] = extra_elementor_mcp_sanitize_schema( $sub );
				}
			}
		}
	}

	return $schema;
}

/**
 * Checks that all required dependencies are available.
 *
 * @since 1.0.0
 *
 * @return bool True if all dependencies are met.
 */
function extra_elementor_mcp_check_dependencies(): bool {
	$missing = array();

	// MCP Adapter must be active.
	if ( ! class_exists( '\WP\MCP\Core\McpAdapter' ) ) {
		$missing[] = 'WordPress MCP Adapter';
	}

	// WordPress Abilities API must be available.
	if ( ! function_exists( 'wp_register_ability' ) ) {
		$missing[] = 'WordPress Abilities API (requires WordPress 6.9+)';
	}

	if ( ! empty( $missing ) ) {
		add_action(
			'admin_notices',
			function () use ( $missing ) {
				$list = implode( ', ', $missing );
				printf(
					'<div class="notice notice-error"><p>%s</p></div>',
					sprintf(
						/* translators: %s: comma-separated list of missing dependencies */
						esc_html__( 'Extra MCP Tools for Elementor requires the following to be installed and active: %s', 'extra-elementor-mcp' ),
						'<strong>' . esc_html( $list ) . '</strong>'
					)
				);
			}
		);

		return false;
	}

	return true;
}

/**
 * Initializes the plugin.
 *
 * Hooked to `plugins_loaded` at priority 20 to ensure dependencies are loaded first.
 *
 * @since 1.0.0
 */
function extra_elementor_mcp_init(): void {
	if ( ! extra_elementor_mcp_check_dependencies() ) {
		return;
	}

	// Load ability group classes.
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-page-status-abilities.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-menu-abilities.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-site-abilities.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-media-abilities.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-taxonomy-abilities.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-revision-abilities.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-seo-abilities.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-acf-abilities.php';

	// Load core classes.
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/class-ability-registrar.php';
	require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/class-plugin.php';

	// Admin settings page.
	if ( is_admin() ) {
		require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/admin/class-admin.php';
	}

	// Boot the plugin.
	Extra_Elementor_MCP_Plugin::instance();
}
add_action( 'plugins_loaded', 'extra_elementor_mcp_init', 20 );
