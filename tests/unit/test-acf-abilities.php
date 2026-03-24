<?php
/**
 * Unit tests for Extra_Elementor_MCP_Acf_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Define a stub ACF class before loading the ability class so it can be
// included without a real ACF installation present.
if ( ! class_exists( 'ACF' ) ) {
	class ACF {} // phpcs:ignore
}

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-acf-abilities.php';

/**
 * Tests for the ACF custom field ability class.
 */
class Test_Acf_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Acf_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Acf_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns all three ACF tool names.
	 */
	public function test_get_ability_names_returns_three_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 3, $names );
		$this->assertContains( 'extra-elementor-mcp/list-acf-field-groups', $names );
		$this->assertContains( 'extra-elementor-mcp/get-acf-fields', $names );
		$this->assertContains( 'extra-elementor-mcp/update-acf-fields', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_acf_permission() returns true for capable user.
	 */
	public function test_check_acf_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_acf_permission( array( 'post_id' => 0 ) ) );
	}

	/**
	 * Tests check_acf_permission() returns false for incapable user.
	 */
	public function test_check_acf_permission_blocks_incapable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => false,
			)
		);

		$this->assertFalse( $this->abilities->check_acf_permission( array( 'post_id' => 0 ) ) );
	}

	// -------------------------------------------------------------------------
	// execute_get_acf_fields()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_get_acf_fields() returns WP_Error when post is not found.
	 */
	public function test_execute_get_acf_fields_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 99 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_get_acf_fields( array( 'post_id' => 99 ) );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}
}
