<?php
/**
 * Unit tests for Extra_Elementor_MCP_Menu_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-menu-abilities.php';

/**
 * Tests for the navigation menu ability class.
 */
class Test_Menu_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Menu_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Menu_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns all four menu tool names.
	 */
	public function test_get_ability_names_returns_all_four_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 4, $names );
		$this->assertContains( 'extra-elementor-mcp/list-menus', $names );
		$this->assertContains( 'extra-elementor-mcp/get-menu', $names );
		$this->assertContains( 'extra-elementor-mcp/update-menu', $names );
		$this->assertContains( 'extra-elementor-mcp/assign-menu-location', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_menu_permission() returns true when user has edit_theme_options.
	 */
	public function test_check_menu_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_theme_options' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_menu_permission() );
	}

	/**
	 * Tests check_menu_permission() returns false when user lacks edit_theme_options.
	 */
	public function test_check_menu_permission_blocks_incapable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_theme_options' ),
				'return' => false,
			)
		);

		$this->assertFalse( $this->abilities->check_menu_permission() );
	}

	// -------------------------------------------------------------------------
	// execute_list_menus()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_list_menus() returns a flat array of menu entries.
	 *
	 * The implementation returns the menu list directly (not wrapped in a 'menus' key).
	 */
	public function test_execute_list_menus_returns_array(): void {
		$fake_menu = (object) array(
			'term_id' => 1,
			'name'    => 'Main Menu',
			'slug'    => 'main-menu',
			'count'   => 3,
		);

		WP_Mock::userFunction(
			'wp_get_nav_menus',
			array(
				'return' => array( $fake_menu ),
			)
		);

		WP_Mock::userFunction(
			'get_nav_menu_locations',
			array(
				'return' => array( 'primary' => 1 ),
			)
		);

		$result = $this->abilities->execute_list_menus( array() );

		$this->assertIsArray( $result );
		$this->assertCount( 1, $result );
		$this->assertArrayHasKey( 'id', $result[0] );
		$this->assertArrayHasKey( 'name', $result[0] );
	}
}
