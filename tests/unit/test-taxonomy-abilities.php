<?php
/**
 * Unit tests for Extra_Elementor_MCP_Taxonomy_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-taxonomy-abilities.php';

/**
 * Tests for the taxonomy ability class.
 */
class Test_Taxonomy_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Taxonomy_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Taxonomy_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns all four taxonomy tool names.
	 */
	public function test_get_ability_names_returns_four_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 4, $names );
		$this->assertContains( 'extra-elementor-mcp/list-categories', $names );
		$this->assertContains( 'extra-elementor-mcp/create-category', $names );
		$this->assertContains( 'extra-elementor-mcp/list-tags', $names );
		$this->assertContains( 'extra-elementor-mcp/create-tag', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_read_permission() returns true when user can edit_posts.
	 */
	public function test_check_read_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_read_permission() );
	}

	/**
	 * Tests check_read_permission() returns false when user lacks edit_posts.
	 */
	public function test_check_read_permission_blocks_incapable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => false,
			)
		);

		$this->assertFalse( $this->abilities->check_read_permission() );
	}

	/**
	 * Tests check_create_permission() returns true when user can manage_categories.
	 */
	public function test_check_create_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'manage_categories' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_create_permission() );
	}

	/**
	 * Tests check_create_permission() returns false when user lacks manage_categories.
	 */
	public function test_check_create_permission_blocks_incapable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'manage_categories' ),
				'return' => false,
			)
		);

		$this->assertFalse( $this->abilities->check_create_permission() );
	}

	// -------------------------------------------------------------------------
	// execute_create_category()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_create_category() returns WP_Error on insertion failure.
	 */
	public function test_execute_create_category_returns_error_on_failure(): void {
		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return_arg' => 0,
			)
		);

		// The implementation uses wp_insert_category() (not wp_insert_term).
		WP_Mock::userFunction(
			'wp_insert_category',
			array(
				'return' => new WP_Error( 'term_exists', 'A term with the name provided already exists.' ),
			)
		);

		$result = $this->abilities->execute_create_category(
			array( 'name' => 'Duplicate Category' )
		);

		$this->assertInstanceOf( WP_Error::class, $result );
	}

	// -------------------------------------------------------------------------
	// execute_create_tag()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_create_tag() returns WP_Error on insertion failure.
	 */
	public function test_execute_create_tag_returns_error_on_failure(): void {
		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return_arg' => 0,
			)
		);

		WP_Mock::userFunction(
			'wp_insert_term',
			array(
				'return' => new WP_Error( 'term_exists', 'A term with the name provided already exists.' ),
			)
		);

		$result = $this->abilities->execute_create_tag(
			array( 'name' => 'Duplicate Tag' )
		);

		$this->assertInstanceOf( WP_Error::class, $result );
	}
}
