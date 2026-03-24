<?php
/**
 * Unit tests for Extra_Elementor_MCP_Site_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-site-abilities.php';

/**
 * Tests for the site settings ability class.
 */
class Test_Site_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Site_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Site_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns all three site tool names.
	 */
	public function test_get_ability_names_returns_three_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 3, $names );
		$this->assertContains( 'extra-elementor-mcp/get-site-info', $names );
		$this->assertContains( 'extra-elementor-mcp/update-site-settings', $names );
		$this->assertContains( 'extra-elementor-mcp/get-reading-settings', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_manage_options() returns true for users with manage_options.
	 */
	public function test_check_manage_options_allows_admin(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'manage_options' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_manage_options() );
	}

	/**
	 * Tests check_manage_options() returns false for users without manage_options.
	 */
	public function test_check_manage_options_blocks_editor(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'manage_options' ),
				'return' => false,
			)
		);

		$this->assertFalse( $this->abilities->check_manage_options() );
	}

	// -------------------------------------------------------------------------
	// execute_update_site_settings()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_update_site_settings() returns empty updated array for empty input.
	 *
	 * The implementation accepts empty input gracefully and returns an updated list
	 * without error (no fields updated).
	 */
	public function test_execute_update_site_settings_returns_empty_updated_for_no_fields(): void {
		$result = $this->abilities->execute_update_site_settings( array() );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'updated', $result );
		$this->assertEmpty( $result['updated'] );
	}
}
