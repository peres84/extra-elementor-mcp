<?php
/**
 * Unit tests for Extra_Elementor_MCP_Revision_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-revision-abilities.php';

/**
 * Tests for the revision history ability class.
 */
class Test_Revision_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Revision_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Revision_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns both revision tool names.
	 */
	public function test_get_ability_names_returns_two_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 2, $names );
		$this->assertContains( 'extra-elementor-mcp/list-revisions', $names );
		$this->assertContains( 'extra-elementor-mcp/restore-revision', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_read_permission() returns true with no post_id when user can edit_posts.
	 */
	public function test_check_read_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_read_permission( array( 'post_id' => 0 ) ) );
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

		$this->assertFalse( $this->abilities->check_read_permission( array( 'post_id' => 0 ) ) );
	}

	/**
	 * Tests check_restore_permission() returns true for capable user with no post_id.
	 */
	public function test_check_restore_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => true,
			)
		);

		$this->assertTrue(
			$this->abilities->check_restore_permission( array( 'revision_id' => 0, 'post_id' => 0 ) )
		);
	}

	// -------------------------------------------------------------------------
	// execute_list_revisions()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_list_revisions() returns WP_Error when post is not found.
	 */
	public function test_execute_list_revisions_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 99 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_list_revisions( array( 'post_id' => 99 ) );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}

	// -------------------------------------------------------------------------
	// execute_restore_revision()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_restore_revision() returns WP_Error when the parent post is not found.
	 */
	public function test_execute_restore_revision_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'return' => null,
			)
		);

		$result = $this->abilities->execute_restore_revision(
			array(
				'post_id'     => 10,
				'revision_id' => 55,
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}
}
