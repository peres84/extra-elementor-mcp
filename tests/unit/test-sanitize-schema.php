<?php
/**
 * Unit tests for the extra_elementor_mcp_sanitize_schema() helper.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

use WP_Mock\Tools\TestCase;

/**
 * Tests for the schema sanitization helper in the main plugin file.
 */
class Test_Sanitize_Schema extends TestCase {

	/**
	 * Tests that enum arrays with empty strings have them removed.
	 */
	public function test_removes_empty_strings_from_enum(): void {
		$schema = array(
			'type' => 'string',
			'enum' => array( 'publish', '', 'draft', '', 'pending' ),
		);

		$result = extra_elementor_mcp_sanitize_schema( $schema );

		$this->assertSame( array( 'publish', 'draft', 'pending' ), $result['enum'] );
	}

	/**
	 * Tests that an enum array of only empty strings removes the enum key entirely.
	 */
	public function test_removes_enum_key_when_all_values_are_empty(): void {
		$schema = array(
			'type' => 'string',
			'enum' => array( '', '' ),
		);

		$result = extra_elementor_mcp_sanitize_schema( $schema );

		$this->assertArrayNotHasKey( 'enum', $result );
	}

	/**
	 * Tests that an empty properties array is converted to a stdClass.
	 */
	public function test_converts_empty_properties_to_stdclass(): void {
		$schema = array(
			'type'       => 'object',
			'properties' => array(),
		);

		$result = extra_elementor_mcp_sanitize_schema( $schema );

		$this->assertInstanceOf( \stdClass::class, $result['properties'] );
	}

	/**
	 * Tests that non-empty properties arrays are recursively sanitized.
	 */
	public function test_recursively_sanitizes_nested_properties(): void {
		$schema = array(
			'type'       => 'object',
			'properties' => array(
				'status' => array(
					'type' => 'string',
					'enum' => array( 'active', '', 'inactive' ),
				),
			),
		);

		$result = extra_elementor_mcp_sanitize_schema( $schema );

		$this->assertSame( array( 'active', 'inactive' ), $result['properties']['status']['enum'] );
	}

	/**
	 * Tests that items arrays are recursively sanitized.
	 */
	public function test_recursively_sanitizes_items(): void {
		$schema = array(
			'type'  => 'array',
			'items' => array(
				'type' => 'string',
				'enum' => array( 'foo', '', 'bar' ),
			),
		);

		$result = extra_elementor_mcp_sanitize_schema( $schema );

		$this->assertSame( array( 'foo', 'bar' ), $result['items']['enum'] );
	}

	/**
	 * Tests that allOf/oneOf/anyOf sub-schemas are recursively sanitized.
	 */
	public function test_recursively_sanitizes_allof_schemas(): void {
		$schema = array(
			'allOf' => array(
				array(
					'type' => 'string',
					'enum' => array( 'a', '', 'b' ),
				),
			),
		);

		$result = extra_elementor_mcp_sanitize_schema( $schema );

		$this->assertSame( array( 'a', 'b' ), $result['allOf'][0]['enum'] );
	}

	/**
	 * Tests that a schema without enum/properties is passed through unchanged.
	 */
	public function test_passthrough_schema_without_enum_or_properties(): void {
		$schema = array(
			'type'        => 'string',
			'description' => 'A plain string field',
		);

		$result = extra_elementor_mcp_sanitize_schema( $schema );

		$this->assertSame( $schema, $result );
	}
}
