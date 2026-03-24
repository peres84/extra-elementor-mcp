<?php
/**
 * Unit tests for Extra_Elementor_MCP_Media_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-media-abilities.php';

/**
 * Tests for the media library ability class.
 */
class Test_Media_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Media_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Media_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns all three media tool names.
	 */
	public function test_get_ability_names_returns_three_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 3, $names );
		$this->assertContains( 'extra-elementor-mcp/list-media', $names );
		$this->assertContains( 'extra-elementor-mcp/upload-media', $names );
		$this->assertContains( 'extra-elementor-mcp/update-media-meta', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_upload_permission() returns true for users with upload_files.
	 */
	public function test_check_upload_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'upload_files' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_upload_permission() );
	}

	/**
	 * Tests check_upload_permission() returns false when user lacks upload_files.
	 */
	public function test_check_upload_permission_blocks_incapable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'upload_files' ),
				'return' => false,
			)
		);

		$this->assertFalse( $this->abilities->check_upload_permission() );
	}

	// -------------------------------------------------------------------------
	// execute_update_media_meta()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_update_media_meta() returns WP_Error when attachment is not found.
	 */
	public function test_execute_update_media_meta_returns_error_for_missing_attachment(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 99 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_update_media_meta(
			array(
				'attachment_id' => 99,
				'title'         => 'New Title',
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}

	/**
	 * Tests execute_update_media_meta() returns WP_Error for non-attachment post.
	 *
	 * The implementation uses a single 'not_found' error code for both missing
	 * posts and non-attachment post types.
	 */
	public function test_execute_update_media_meta_returns_error_for_non_attachment(): void {
		$fake_post = (object) array(
			'ID'        => 5,
			'post_type' => 'post',
		);

		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 5 ),
				'return' => $fake_post,
			)
		);

		$result = $this->abilities->execute_update_media_meta(
			array(
				'attachment_id' => 5,
				'title'         => 'New Title',
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		// Both "not found" and "not attachment" use the same 'not_found' error code.
		$this->assertSame( 'not_found', $result->get_error_code() );
	}
}
