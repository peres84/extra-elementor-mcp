<?php
/**
 * Admin settings page handler.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles the plugin settings page in wp-admin.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Admin {

	/**
	 * Option name used to store plugin settings.
	 *
	 * @var string
	 */
	const OPTION_NAME = 'extra_elementor_mcp_settings';

	/**
	 * Settings group name (used by Settings API).
	 *
	 * @var string
	 */
	const SETTINGS_GROUP = 'extra_elementor_mcp_settings_group';

	/**
	 * Page slug used in admin_menu.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'extra-elementor-mcp';

	/**
	 * Initializes admin hooks.
	 *
	 * @since 1.0.0
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Adds the settings page to the admin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_menu_page(): void {
		add_options_page(
			__( 'Extra MCP Tools', 'extra-elementor-mcp' ),
			__( 'Extra MCP Tools', 'extra-elementor-mcp' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registers plugin settings, sections, and fields with the Settings API.
	 *
	 * @since 1.0.0
	 */
	public function register_settings(): void {
		register_setting(
			self::SETTINGS_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => $this->get_defaults(),
			)
		);

		// Tool group toggles section.
		add_settings_section(
			'extra_elementor_mcp_tool_groups',
			__( 'Tool Group Toggles', 'extra-elementor-mcp' ),
			array( $this, 'render_tool_groups_section' ),
			self::PAGE_SLUG
		);

		$groups = $this->get_tool_groups();

		foreach ( $groups as $key => $group ) {
			add_settings_field(
				'extra_elementor_mcp_' . $key,
				esc_html( $group['label'] ),
				array( $this, 'render_toggle_field' ),
				self::PAGE_SLUG,
				'extra_elementor_mcp_tool_groups',
				array(
					'key'         => $key,
					'description' => $group['description'],
					'conditional' => $group['conditional'] ?? null,
				)
			);
		}
	}

	/**
	 * Sanitizes settings values on save.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $input Raw input from form submission.
	 * @return array Sanitized settings array.
	 */
	public function sanitize_settings( $input ): array {
		$sanitized = array();
		$defaults  = $this->get_defaults();

		foreach ( $defaults as $key => $default ) {
			// Checkboxes are only present in POST data when checked.
			$sanitized[ $key ] = isset( $input[ $key ] ) ? 1 : 0;
		}

		return $sanitized;
	}

	/**
	 * Returns the default settings values.
	 *
	 * All tool groups are enabled by default.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default settings.
	 */
	public function get_defaults(): array {
		return array(
			'enable_page_status' => 1,
			'enable_menus'       => 1,
			'enable_site'        => 1,
			'enable_media'       => 1,
			'enable_taxonomies'  => 1,
			'enable_revisions'   => 1,
			'enable_seo'         => 1,
			'enable_acf'         => 1,
		);
	}

	/**
	 * Returns tool group definitions.
	 *
	 * @since 1.0.0
	 *
	 * @return array[] Tool group definitions keyed by settings key.
	 */
	private function get_tool_groups(): array {
		return array(
			'enable_page_status' => array(
				'label'       => __( 'Page Status', 'extra-elementor-mcp' ),
				'description' => __( 'publish-page, get-page-info, update-page-meta', 'extra-elementor-mcp' ),
			),
			'enable_menus'       => array(
				'label'       => __( 'Navigation Menus', 'extra-elementor-mcp' ),
				'description' => __( 'list-menus, get-menu, update-menu, assign-menu-location', 'extra-elementor-mcp' ),
			),
			'enable_site'        => array(
				'label'       => __( 'Site Settings', 'extra-elementor-mcp' ),
				'description' => __( 'get-site-info, update-site-settings, get-reading-settings', 'extra-elementor-mcp' ),
			),
			'enable_media'       => array(
				'label'       => __( 'Media Library', 'extra-elementor-mcp' ),
				'description' => __( 'list-media, upload-media, update-media-meta', 'extra-elementor-mcp' ),
			),
			'enable_taxonomies'  => array(
				'label'       => __( 'Taxonomies', 'extra-elementor-mcp' ),
				'description' => __( 'list-categories, create-category, list-tags, create-tag', 'extra-elementor-mcp' ),
			),
			'enable_revisions'   => array(
				'label'       => __( 'Revisions', 'extra-elementor-mcp' ),
				'description' => __( 'list-revisions, restore-revision', 'extra-elementor-mcp' ),
			),
			'enable_seo'         => array(
				'label'       => __( 'Yoast SEO', 'extra-elementor-mcp' ),
				'description' => __( 'get-seo, update-seo, get-seo-analysis', 'extra-elementor-mcp' ),
				'conditional' => 'WPSEO_VERSION',
			),
			'enable_acf'         => array(
				'label'       => __( 'Advanced Custom Fields (ACF)', 'extra-elementor-mcp' ),
				'description' => __( 'list-acf-field-groups, get-acf-fields, update-acf-fields', 'extra-elementor-mcp' ),
				'conditional' => 'ACF',
			),
		);
	}

	/**
	 * Renders the tool groups settings section description.
	 *
	 * @since 1.0.0
	 */
	public function render_tool_groups_section(): void {
		echo '<p>' . esc_html__( 'Enable or disable individual tool groups. Disabled groups will not be registered with the MCP server.', 'extra-elementor-mcp' ) . '</p>';
	}

	/**
	 * Renders a single toggle checkbox field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Field arguments including 'key', 'description', and optional 'conditional'.
	 */
	public function render_toggle_field( array $args ): void {
		$key         = $args['key'];
		$description = $args['description'];
		$conditional = $args['conditional'] ?? null;
		$options     = get_option( self::OPTION_NAME, $this->get_defaults() );
		$checked     = ! empty( $options[ $key ] );
		$field_name  = self::OPTION_NAME . '[' . $key . ']';
		$field_id    = 'extra_elementor_mcp_' . $key;

		// Detect whether the conditional dependency is available.
		$dependency_met    = true;
		$dependency_notice = '';

		if ( null !== $conditional ) {
			if ( 'WPSEO_VERSION' === $conditional ) {
				$dependency_met    = defined( 'WPSEO_VERSION' );
				$dependency_notice = __( 'Requires Yoast SEO to be active.', 'extra-elementor-mcp' );
			} elseif ( 'ACF' === $conditional ) {
				$dependency_met    = class_exists( 'ACF' );
				$dependency_notice = __( 'Requires Advanced Custom Fields (ACF) to be active.', 'extra-elementor-mcp' );
			}
		}

		?>
		<label for="<?php echo esc_attr( $field_id ); ?>">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $field_id ); ?>"
				name="<?php echo esc_attr( $field_name ); ?>"
				value="1"
				<?php checked( $checked ); ?>
				<?php disabled( ! $dependency_met ); ?>
			/>
			<?php echo esc_html( $description ); ?>
		</label>
		<?php if ( ! $dependency_met ) : ?>
			<p class="description" style="color:#888;">
				<?php echo esc_html( $dependency_notice ); ?>
				<?php esc_html_e( 'Install and activate the dependency to enable this group.', 'extra-elementor-mcp' ); ?>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Renders the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/admin/views/page-settings.php';
	}

	/**
	 * Helper to get whether a tool group is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Settings key, e.g. 'enable_page_status'.
	 * @return bool True if the group is enabled.
	 */
	public static function is_group_enabled( string $key ): bool {
		$options  = get_option( self::OPTION_NAME, array() );
		$defaults = array(
			'enable_page_status' => 1,
			'enable_menus'       => 1,
			'enable_site'        => 1,
			'enable_media'       => 1,
			'enable_taxonomies'  => 1,
			'enable_revisions'   => 1,
			'enable_seo'         => 1,
			'enable_acf'         => 1,
		);

		// Fall back to default (enabled) if option not saved yet.
		if ( ! isset( $options[ $key ] ) ) {
			return ! empty( $defaults[ $key ] );
		}

		return ! empty( $options[ $key ] );
	}
}
