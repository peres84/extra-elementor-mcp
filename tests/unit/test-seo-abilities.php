<?php
/**
 * Unit tests for Extra_Elementor_MCP_Seo_Abilities.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

// Define WPSEO_VERSION before loading the class so it can be included.
if ( ! defined( 'WPSEO_VERSION' ) ) {
	define( 'WPSEO_VERSION', '21.0' );
}

// Load the class under test.
require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/abilities/class-seo-abilities.php';

/**
 * Tests for the Yoast SEO ability class.
 */
class Test_Seo_Abilities extends TestCase {

	/**
	 * @var Extra_Elementor_MCP_Seo_Abilities
	 */
	private $abilities;

	public function setUp(): void {
		parent::setUp();
		$this->abilities = new Extra_Elementor_MCP_Seo_Abilities();
	}

	// -------------------------------------------------------------------------
	// get_ability_names()
	// -------------------------------------------------------------------------

	/**
	 * Tests that get_ability_names() returns all three SEO tool names.
	 */
	public function test_get_ability_names_returns_three_names(): void {
		$names = $this->abilities->get_ability_names();

		$this->assertCount( 3, $names );
		$this->assertContains( 'extra-elementor-mcp/get-seo', $names );
		$this->assertContains( 'extra-elementor-mcp/update-seo', $names );
		$this->assertContains( 'extra-elementor-mcp/get-seo-analysis', $names );
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Tests check_seo_permission() returns true for capable user without post_id.
	 */
	public function test_check_seo_permission_allows_capable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => true,
			)
		);

		$this->assertTrue( $this->abilities->check_seo_permission( array( 'post_id' => 0 ) ) );
	}

	/**
	 * Tests check_seo_permission() returns false when user lacks edit_posts.
	 */
	public function test_check_seo_permission_blocks_incapable_user(): void {
		WP_Mock::userFunction(
			'current_user_can',
			array(
				'args'   => array( 'edit_posts' ),
				'return' => false,
			)
		);

		$this->assertFalse( $this->abilities->check_seo_permission( array( 'post_id' => 0 ) ) );
	}

	// -------------------------------------------------------------------------
	// execute_get_seo()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_get_seo() returns WP_Error when post is not found.
	 */
	public function test_execute_get_seo_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 99 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_get_seo( array( 'post_id' => 99 ) );

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}

	/**
	 * Tests execute_get_seo() returns expected SEO meta keys for a valid post.
	 */
	public function test_execute_get_seo_returns_expected_keys(): void {
		$fake_post = (object) array( 'ID' => 10 );

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
				'return' => '',
			)
		);

		$result = $this->abilities->execute_get_seo( array( 'post_id' => 10 ) );

		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'post_id', $result );
		$this->assertArrayHasKey( 'title', $result );
		$this->assertArrayHasKey( 'description', $result );
	}

	// -------------------------------------------------------------------------
	// execute_update_seo()
	// -------------------------------------------------------------------------

	/**
	 * Tests execute_update_seo() returns WP_Error when post is not found.
	 */
	public function test_execute_update_seo_returns_error_for_missing_post(): void {
		WP_Mock::userFunction(
			'get_post',
			array(
				'args'   => array( 99 ),
				'return' => null,
			)
		);

		$result = $this->abilities->execute_update_seo(
			array(
				'post_id' => 99,
				'title'   => 'SEO Title',
			)
		);

		$this->assertInstanceOf( WP_Error::class, $result );
		$this->assertSame( 'not_found', $result->get_error_code() );
	}
}
