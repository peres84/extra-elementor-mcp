<?php
/**
 * Unit tests for Extra_Elementor_MCP_Ability_Registrar.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Load all ability classes and core classes needed for the registrar.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-page-status-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-menu-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-site-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-media-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-taxonomy-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-revision-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-seo-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-acf-abilities.php';
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/class-ability-registrar.php';

/**
 * Tests for the ability registrar.
 */
class Test_Ability_Registrar extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Ability_Registrar
	 */
	private $registrar;

	public function setUp(): void {
		parent::setUp();
		$this->registrar = new Extra_Elementor_MCP_Ability_Registrar();
	}

	// -------------------------------------------------------------------------
	// register_all()
	// -------------------------------------------------------------------------

	/**
	 * Tests that register_all() returns at least the Phase 1 core tool names
	 * when all groups are enabled (default state: option not saved).
	 */
	public function test_register_all_returns_core_tool_names_when_all_enabled(): void {
		// Stub get_option to return empty array (all groups default to enabled).
		WP_Mock::userFunction(
			'get_option',
			array(
				'return' => array(),
			)
		);

		// Stub apply_filters to return its second argument unchanged.
		WP_Mock::userFunction(
			'apply_filters',
			array(
				'return_arg' => 1,
			)
		);

		$names = $this->registrar->register_all();

		$this->assertIsArray( $names );
		$this->assertContains( 'extra-elementor-mcp/publish-page', $names );
		$this->assertContains( 'extra-elementor-mcp/get-page-info', $names );
		$this->assertContains( 'extra-elementor-mcp/list-menus', $names );
		$this->assertContains( 'extra-elementor-mcp/get-site-info', $names );
		$this->assertContains( 'extra-elementor-mcp/list-media', $names );
		$this->assertContains( 'extra-elementor-mcp/list-categories', $names );
		$this->assertContains( 'extra-elementor-mcp/list-revisions', $names );
	}

	/**
	 * Tests that register_all() skips a group when its toggle is disabled.
	 */
	public function test_register_all_skips_disabled_group(): void {
		WP_Mock::userFunction(
			'get_option',
			array(
				'return' => array( 'enable_page_status' => false ),
			)
		);

		WP_Mock::userFunction(
			'apply_filters',
			array(
				'return_arg' => 1,
			)
		);

		$names = $this->registrar->register_all();

		$this->assertNotContains( 'extra-elementor-mcp/publish-page', $names );
		// Other groups should still be present.
		$this->assertContains( 'extra-elementor-mcp/list-menus', $names );
	}

	/**
	 * Tests that register_all() does not throw with all groups enabled.
	 */
	public function test_register_all_does_not_throw(): void {
		WP_Mock::userFunction(
			'get_option',
			array(
				'return' => array(),
			)
		);

		WP_Mock::userFunction(
			'apply_filters',
			array(
				'return_arg' => 1,
			)
		);

		$names = $this->registrar->register_all();

		$this->assertIsArray( $names );
	}

	/**
	 * Tests that get_ability_names() returns the same list produced by register_all().
	 */
	public function test_get_ability_names_matches_register_all(): void {
		WP_Mock::userFunction(
			'get_option',
			array(
				'return' => array(),
			)
		);

		WP_Mock::userFunction(
			'apply_filters',
			array(
				'return_arg' => 1,
			)
		);

		$registered = $this->registrar->register_all();
		$retrieved   = $this->registrar->get_ability_names();

		$this->assertSame( $registered, $retrieved );
	}
}
