<?php
/**
 * ACF Custom Fields MCP abilities.
 *
 * Registers 3 tools for listing, reading, and updating ACF field data.
 * Only loaded when ACF is active (class_exists('ACF')).
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and implements the ACF custom field abilities.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Acf_Abilities {

	/**
	 * Returns the ability names registered by this class.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	public function get_ability_names(): array {
		return array(
			'extra-elementor-mcp/list-acf-field-groups',
			'extra-elementor-mcp/get-acf-fields',
			'extra-elementor-mcp/update-acf-fields',
		);
	}

	/**
	 * Registers all ACF abilities.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$this->register_list_acf_field_groups();
		$this->register_get_acf_fields();
		$this->register_update_acf_fields();
	}

	// -------------------------------------------------------------------------
	// Permission callbacks
	// -------------------------------------------------------------------------

	/**
	 * Permission check for ACF read/write (edit_posts + ownership).
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input The input data.
	 * @return bool
	 */
	public function check_acf_permission( $input = null ): bool {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		$post_id = absint( $input['post_id'] ?? 0 );
		if ( $post_id && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Permission check for listing field groups (edit_posts).
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function check_list_permission(): bool {
		return current_user_can( 'edit_posts' );
	}

	// -------------------------------------------------------------------------
	// list-acf-field-groups
	// -------------------------------------------------------------------------

	/**
	 * Registers the list-acf-field-groups ability.
	 *
	 * @since 1.0.0
	 */
	private function register_list_acf_field_groups(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/list-acf-field-groups',
			array(
				'label'               => __( 'List ACF Field Groups', 'extra-elementor-mcp' ),
				'description'         => __( 'List all ACF field groups with their fields and location rules.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_list_acf_field_groups' ),
				'permission_callback' => array( $this, 'check_list_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(),
				),
				'output_schema'       => array(
					'type'  => 'array',
					'items' => array( 'type' => 'object' ),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the list-acf-field-groups ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array
	 */
	public function execute_list_acf_field_groups( array $input ): array {
		$groups = acf_get_field_groups();
		$result = array();

		foreach ( $groups as $group ) {
			$fields     = acf_get_fields( $group['key'] );
			$field_list = array();

			if ( $fields ) {
				foreach ( $fields as $field ) {
					$field_data = array(
						'key'           => $field['key'],
						'name'          => $field['name'],
						'label'         => $field['label'],
						'type'          => $field['type'],
						'required'      => (bool) ( $field['required'] ?? false ),
						'default_value' => $field['default_value'] ?? null,
					);

					if ( isset( $field['choices'] ) ) {
						$field_data['choices'] = $field['choices'];
					}

					$field_list[] = $field_data;
				}
			}

			$result[] = array(
				'key'       => $group['key'],
				'title'     => $group['title'],
				'fields'    => $field_list,
				'locations' => $group['location'] ?? array(),
				'active'    => (bool) ( $group['active'] ?? true ),
			);
		}

		return $result;
	}

	// -------------------------------------------------------------------------
	// get-acf-fields
	// -------------------------------------------------------------------------

	/**
	 * Registers the get-acf-fields ability.
	 *
	 * @since 1.0.0
	 */
	private function register_get_acf_fields(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/get-acf-fields',
			array(
				'label'               => __( 'Get ACF Fields', 'extra-elementor-mcp' ),
				'description'         => __( 'Get ACF field values for a specific post/page.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_get_acf_fields' ),
				'permission_callback' => array( $this, 'check_acf_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id' ),
				),
				'output_schema'       => array(
					'type'        => 'object',
					'description' => __( 'Object mapping field names to their current values.', 'extra-elementor-mcp' ),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the get-acf-fields ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_get_acf_fields( array $input ) {
		$post_id = absint( $input['post_id'] );

		if ( ! get_post( $post_id ) ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		$fields = get_fields( $post_id );
		return $fields ?: array();
	}

	// -------------------------------------------------------------------------
	// update-acf-fields
	// -------------------------------------------------------------------------

	/**
	 * Registers the update-acf-fields ability.
	 *
	 * @since 1.0.0
	 */
	private function register_update_acf_fields(): void {
		extra_elementor_mcp_register_ability(
			'extra-elementor-mcp/update-acf-fields',
			array(
				'label'               => __( 'Update ACF Fields', 'extra-elementor-mcp' ),
				'description'         => __( 'Update ACF field values for a post/page. Provide a fields object with field names as keys and new values as values.', 'extra-elementor-mcp' ),
				'category'            => 'extra-elementor-mcp',
				'execute_callback'    => array( $this, 'execute_update_acf_fields' ),
				'permission_callback' => array( $this, 'check_acf_permission' ),
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array(
							'type'        => 'integer',
							'description' => __( 'The ID of the page or post to update.', 'extra-elementor-mcp' ),
						),
						'fields'  => array(
							'type'        => 'object',
							'description' => __( 'Key-value pairs of field names and their new values.', 'extra-elementor-mcp' ),
						),
					),
					'required'   => array( 'post_id', 'fields' ),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array( 'type' => 'integer' ),
						'updated' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
					),
				),
				'meta'                => array(
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					),
					'show_in_rest' => true,
				),
			)
		);
	}

	/**
	 * Executes the update-acf-fields ability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input The input data.
	 * @return array|\WP_Error
	 */
	public function execute_update_acf_fields( array $input ) {
		$post_id = absint( $input['post_id'] );
		$fields  = $input['fields'] ?? array();

		if ( ! get_post( $post_id ) ) {
			return new \WP_Error( 'not_found', __( 'Post not found.', 'extra-elementor-mcp' ) );
		}

		if ( ! is_array( $fields ) || empty( $fields ) ) {
			return new \WP_Error( 'invalid_fields', __( 'Fields must be a non-empty object.', 'extra-elementor-mcp' ) );
		}

		$updated = array();
		foreach ( $fields as $field_name => $value ) {
			$field_name = sanitize_key( $field_name );
			if ( update_field( $field_name, $value, $post_id ) !== false ) {
				$updated[] = $field_name;
			}
		}

		return array(
			'post_id' => $post_id,
			'updated' => $updated,
		);
	}
}
