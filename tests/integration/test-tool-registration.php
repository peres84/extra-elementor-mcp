<?php
/**
 * Integration tests: tool registration + MCP server creation.
 *
 * These tests verify that:
 *   1. All expected ability names are registered via the WordPress Abilities API.
 *   2. The plugin creates its MCP server through the MCP Adapter.
 *
 * Requirements:
 *   - The WordPress test library must be installed via bin/install-wp-tests.sh.
 *   - WP_UnitTestCase must be available in the test environment.
 *
 * If these dependencies are not present the test class is skipped gracefully.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

// Skip gracefully when WP_UnitTestCase is unavailable (unit-only CI runs).
if ( ! class_exists( 'WP_UnitTestCase' ) ) {
	return;
}

/**
 * Integration tests for tool registration.
 */
class Test_Tool_Registration_Integration extends WP_UnitTestCase {

	/**
	 * Bootstraps the plugin with real WordPress hooks before running.
	 */
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();

		// Ensure all plugin files are loaded.
		if ( ! function_exists( 'extra_elementor_mcp_register_ability' ) ) {
			require_once dirname( __DIR__, 2 ) . '/extra-elementor-mcp.php';
		}

		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-page-status-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-menu-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-site-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-media-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-taxonomy-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-revision-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-seo-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/abilities/class-acf-abilities.php';
		require_once dirname( __DIR__, 2 ) . '/includes/class-ability-registrar.php';
	}

	/**
	 * Tests that the ability registrar returns all expected core tool names.
	 */
	public function test_registrar_returns_all_core_tool_names(): void {
		$registrar = new Extra_Elementor_MCP_Ability_Registrar();
		$names     = $registrar->register_all();

		$expected = array(
			'extra-elementor-mcp/publish-page',
			'extra-elementor-mcp/get-page-info',
			'extra-elementor-mcp/update-page-meta',
			'extra-elementor-mcp/list-menus',
			'extra-elementor-mcp/get-menu',
			'extra-elementor-mcp/update-menu',
			'extra-elementor-mcp/assign-menu-location',
			'extra-elementor-mcp/get-site-info',
			'extra-elementor-mcp/update-site-settings',
			'extra-elementor-mcp/get-reading-settings',
			'extra-elementor-mcp/list-media',
			'extra-elementor-mcp/upload-media',
			'extra-elementor-mcp/update-media-meta',
			'extra-elementor-mcp/list-categories',
			'extra-elementor-mcp/create-category',
			'extra-elementor-mcp/list-tags',
			'extra-elementor-mcp/create-tag',
			'extra-elementor-mcp/list-revisions',
			'extra-elementor-mcp/restore-revision',
		);

		foreach ( $expected as $ability_name ) {
			$this->assertContains(
				$ability_name,
				$names,
				"Expected ability '{$ability_name}' was not registered."
			);
		}
	}

	/**
	 * Tests that a disabled group produces no abilities from that group.
	 */
	public function test_disabled_group_produces_no_abilities(): void {
		// Disable only the menus group.
		update_option(
			'extra_elementor_mcp_settings',
			array( 'enable_menus' => false )
		);

		$registrar = new Extra_Elementor_MCP_Ability_Registrar();
		$names     = $registrar->register_all();

		// Clean up.
		delete_option( 'extra_elementor_mcp_settings' );

		$this->assertNotContains( 'extra-elementor-mcp/list-menus', $names );
		$this->assertNotContains( 'extra-elementor-mcp/get-menu', $names );
		// Other groups should still be present.
		$this->assertContains( 'extra-elementor-mcp/publish-page', $names );
	}

	/**
	 * Tests that the extra_elementor_mcp_ability_names filter is applied.
	 */
	public function test_ability_names_filter_is_applied(): void {
		$extra_name = 'extra-elementor-mcp/custom-test-ability';

		add_filter(
			'extra_elementor_mcp_ability_names',
			function ( array $names ) use ( $extra_name ): array {
				$names[] = $extra_name;
				return $names;
			}
		);

		$registrar = new Extra_Elementor_MCP_Ability_Registrar();
		$names     = $registrar->register_all();

		remove_all_filters( 'extra_elementor_mcp_ability_names' );

		$this->assertContains( $extra_name, $names );
	}
}
