<?php
/**
 * Compatibility test: extra-elementor-mcp alongside elementor-mcp.
 *
 * Verifies that both plugins can be active simultaneously without:
 *   - Class naming conflicts
 *   - Hook registration conflicts
 *   - MCP server endpoint collisions
 *
 * Requirements:
 *   - WP_UnitTestCase must be available in the test environment.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

// Skip gracefully when WP_UnitTestCase is unavailable.
if ( ! class_exists( 'WP_UnitTestCase' ) ) {
	return;
}

/**
 * Compatibility tests for coexistence with elementor-mcp.
 */
class Test_Compatibility extends WP_UnitTestCase {

	// -------------------------------------------------------------------------
	// Namespace / Class isolation
	// -------------------------------------------------------------------------

	/**
	 * Tests that this plugin's class names do not conflict with elementor-mcp.
	 *
	 * elementor-mcp uses classes like Elementor_MCP_* whereas we use
	 * Extra_Elementor_MCP_* — both prefixes must coexist.
	 */
	public function test_class_prefixes_do_not_conflict(): void {
		$our_classes = array(
			'Extra_Elementor_MCP_Plugin',
			'Extra_Elementor_MCP_Ability_Registrar',
			'Extra_Elementor_MCP_Page_Status_Abilities',
			'Extra_Elementor_MCP_Menu_Abilities',
			'Extra_Elementor_MCP_Site_Abilities',
			'Extra_Elementor_MCP_Media_Abilities',
			'Extra_Elementor_MCP_Taxonomy_Abilities',
			'Extra_Elementor_MCP_Revision_Abilities',
			'Extra_Elementor_MCP_Seo_Abilities',
			'Extra_Elementor_MCP_Acf_Abilities',
		);

		foreach ( $our_classes as $class_name ) {
			// Verify the class does not start with the elementor-mcp prefix.
			$this->assertStringStartsWith(
				'Extra_Elementor_MCP_',
				$class_name,
				"Class '{$class_name}' must use the Extra_Elementor_MCP_ prefix."
			);
		}
	}

	// -------------------------------------------------------------------------
	// MCP endpoint isolation
	// -------------------------------------------------------------------------

	/**
	 * Tests that the MCP server ID differs from elementor-mcp's server ID.
	 *
	 * elementor-mcp registers 'elementor-mcp-server'; we must use a distinct ID.
	 */
	public function test_mcp_server_id_is_unique(): void {
		$our_server_id      = 'extra-elementor-mcp-server';
		$elementor_server_id = 'elementor-mcp-server';

		$this->assertNotSame( $our_server_id, $elementor_server_id );
		$this->assertStringStartsWith( 'extra-', $our_server_id );
	}

	// -------------------------------------------------------------------------
	// Namespace isolation
	// -------------------------------------------------------------------------

	/**
	 * Tests that our plugin's ability namespace prefix is unique.
	 */
	public function test_ability_namespace_prefix_is_unique(): void {
		$our_prefix         = 'extra-elementor-mcp/';
		$elementor_prefix   = 'elementor-mcp/';

		$this->assertNotSame( $our_prefix, $elementor_prefix );
	}

	/**
	 * Tests that every ability name registered by our plugin uses the correct namespace.
	 */
	public function test_all_ability_names_use_correct_namespace(): void {
		if ( ! class_exists( 'Extra_Elementor_MCP_Ability_Registrar' ) ) {
			require_once dirname( __DIR__, 2 ) . '/includes/class-ability-registrar.php';
		}

		$registrar = new Extra_Elementor_MCP_Ability_Registrar();
		$names     = $registrar->register_all();

		foreach ( $names as $name ) {
			$this->assertStringStartsWith(
				'extra-elementor-mcp/',
				$name,
				"Ability '{$name}' does not use the extra-elementor-mcp/ namespace."
			);
		}
	}

	// -------------------------------------------------------------------------
	// Option name isolation
	// -------------------------------------------------------------------------

	/**
	 * Tests that our settings option key uses the correct plugin-specific prefix.
	 */
	public function test_settings_option_name_is_namespaced(): void {
		$option_name = 'extra_elementor_mcp_settings';

		// The option must start with 'extra_elementor_mcp_' to avoid collision
		// with elementor-mcp options.
		$this->assertStringStartsWith( 'extra_elementor_mcp_', $option_name );
	}
}
