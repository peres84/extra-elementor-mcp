<?php
/**
 * Unit tests for Extra_Elementor_MCP_Page_Status_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-page-status-abilities.php';

/**
 * Tests for the page status ability class.
 */
class Test_Page_Status_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Page_Status_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Page_Status_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns the expected tool names.
	 */
	public function test_get_ability_names_returns_all_three_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 3, $names );
		$this->assertContains( 'extra-elementor-mcp/publish-page', $names );
		$this->assertContains( 'extra-elementor-mcp/get-page-info', $names );
		$this->assertContains( 'extra-elementor-mcp/update-page-meta', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_publish_permission() returns true when user can publish_pages.
	 */
	public function test_check_publish_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'publish_pages' ),
				'return' => true,
			)
		);

		$result = $this->abilities->check_publish_permission( array( 'post_id' => 0 ) );

		$this->assertTrue( $result );
	}

	/**
	 * Tests check_publish_permission() returns false when user lacks publish_pages.
	 */
	public function test_check_publish_permission_blocks_incapable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'publish_pages' ),
				'return' => false,
			)
		);

		$result = $this->abilities->check_publish_permission( array( 'post_id' => 0 ) );

		$this->assertFalse( $result );
	}

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

		$result = $this->abilities->check_read_permission( array( 'post_id' => 0 ) );

		$this->assertTrue( $result );
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

		$result = $this->abilities->check_read_permission( array( 'post_id' => 0 ) );

		$this->assertFalse( $result );
	}

	/**
	 * Tests check_edit_permission() returns true when user can edit_posts.
	 */
	public function test_check_edit_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => true,
			)
		);

		$result = $this->abilities->check_edit_permission( array( 'post_id' => 0 ) );

		$this->assertTrue( $result );
	}

	// -------------------------------------------------------------------------
	// execute_publish_page()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_publish_page() returns WP_Error when post is not found.
	 */
	public function test_execute_publish_page_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return_arg' => 0,
			)
		);

		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 1 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_publish_page(
			array(
				'post_id' => 1,
				'status'  => 'publish',
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}

	/**
	 * Tests execute_publish_page() returns WP_Error for invalid status.
	 */
	public function test_execute_publish_page_returns_error_for_invalid_status(): void {
		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'args'   => array( 'bad_status' ),
				'return' => 'bad_status',
			)
		);

		$result = $this->abilities->execute_publish_page(
			array(
				'post_id' => 1,
				'status'  => 'bad_status',
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'invalid_status', $result->get_error_code() );
	}

	/**
	 * Tests execute_publish_page() returns success data when post exists.
	 */
	public function test_execute_publish_page_returns_success_data(): void {
		$fake_post = (object) array(
			'ID'          => 5,
			'post_status' => 'draft',
			'post_title'  => 'Test Page',
		);

		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return_arg' => 0,
			)
		);

		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 5 ),
				'return' => $fake_post,
			)
		);

		WP_Mock::userFunction(
			'wp_update_post',
			array(
				'return' => 5,
			)
		);

		WP_Mock::userFunction(
			'get_the_title',
			array(
				'args'   => array( 5 ),
				'return' => 'Test Page',
			)
		);

		WP_Mock::userFunction(
			'get_permalink',
			array(
				'args'   => array( 5 ),
				'return' => 'http://example.com/test-page/',
			)
		);

		$result = $this->abilities->execute_publish_page(
			array(
				'post_id' => 5,
				'status'  => 'publish',
			)
		);

		$this->assertIsArray( $result );
		$this->assertSame( 5, $result['post_id'] );
		$this->assertSame( 'publish', $result['status'] );
		$this->assertSame( 'Test Page', $result['title'] );
	}

	// -------------------------------------------------------------------------
	// execute_get_page_info()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_get_page_info() returns WP_Error when post is not found.
	 */
	public function test_execute_get_page_info_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 99 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_get_page_info( array( 'post_id' => 99 ) );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}

	/**
	 * Tests execute_get_page_info() returns expected keys for a valid post.
	 */
	public function test_execute_get_page_info_returns_expected_keys(): void {
		$fake_post = (object) array(
			'ID'          => 10,
			'post_status' => 'publish',
			'post_name'   => 'my-page',
			'post_parent' => 0,
			'menu_order'  => 0,
		);

		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 10 ),
				'return' => $fake_post,
			)
		);

		WP_Mock::userFunction(
			'get_post_meta',
			array(
				'args'   => array( 10, '_wp_page_template', true ),
				'return' => '',
			)
		);

		WP_Mock::userFunction(
			'get_post_thumbnail_id',
			array(
				'args'   => array( 10 ),
				'return' => 0,
			)
		);

		WP_Mock::userFunction(
			'get_the_title',
			array(
				'args'   => array( 10 ),
				'return' => 'My Page',
			)
		);

		WP_Mock::userFunction(
			'get_permalink',
			array(
				'args'   => array( 10 ),
				'return' => 'http://example.com/my-page/',
			)
		);

		$result = $this->abilities->execute_get_page_info( array( 'post_id' => 10 ) );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'post_id', $result );
		$this->assertArrayHasKey( 'status', $result );
		$this->assertArrayHasKey( 'slug', $result );
		$this->assertArrayHasKey( 'template', $result );
		$this->assertSame( 'default', $result['template'] );
	}

	// -------------------------------------------------------------------------
	// execute_update_page_meta()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_update_page_meta() returns WP_Error when post is not found.
	 */
	public function test_execute_update_page_meta_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 99 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_update_page_meta( array( 'post_id' => 99 ) );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}

	/**
	 * Tests execute_update_page_meta() tracks updated fields.
	 */
	public function test_execute_update_page_meta_reports_updated_fields(): void {
		$fake_post = (object) array( 'ID' => 7 );

		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 7 ),
				'return' => $fake_post,
			)
		);

		WP_Mock::userFunction(
			'sanitize_title',
			array(
				'return_arg' => 0,
			)
		);

		WP_Mock::userFunction(
			'wp_update_post',
			array(
				'return' => 7,
			)
		);

		WP_Mock::userFunction(
			'sanitize_text_field',
			array(
				'return_arg' => 0,
			)
		);

		WP_Mock::userFunction(
			'update_post_meta',
			array(
				'return' => true,
			)
		);

		$result = $this->abilities->execute_update_page_meta(
			array(
				'post_id'  => 7,
				'slug'     => 'new-slug',
				'template' => 'full-width.php',
			)
		);

		$this->assertIsArray( $result );
		$this->assertContains( 'slug', $result['updated'] );
		$this->assertContains( 'template', $result['updated'] );
	}
}
