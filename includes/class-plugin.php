<?php
/**
 * Main plugin orchestrator.
 *
 * Singleton that initializes all components, registers hooks for the
 * Abilities API and MCP Adapter, and coordinates the plugin lifecycle.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin orchestrator singleton.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * The ability registrar.
	 *
	 * @var Extra_Elementor_MCP_Ability_Registrar
	 */
	private $registrar;

	/**
	 * The admin settings page handler.
	 *
	 * @var Extra_Elementor_MCP_Admin|null
	 */
	private $admin = null;

	/**
	 * Registered ability names (populated after registration).
	 *
	 * @var string[]
	 */
	private $ability_names = array();

	/**
	 * Gets the singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to enforce singleton.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {}

	/**
	 * Initializes the plugin components and hooks.
	 *
	 * @since 1.0.0
	 */
	private function init(): void {
		$this->registrar = new Extra_Elementor_MCP_Ability_Registrar();

		// Admin settings page.
		if ( is_admin() && class_exists( 'Extra_Elementor_MCP_Admin' ) ) {
			$this->admin = new Extra_Elementor_MCP_Admin();
			$this->admin->init();
		}

		// Register the three-hook pattern.
		add_action( 'wp_abilities_api_categories_init', array( $this, 'register_category' ) );
		add_action( 'wp_abilities_api_init', array( $this, 'register_abilities' ) );

		// Hook at priority 20 so the Abilities API is initialized and our
		// abilities are registered before the MCP Adapter creates the server.
		add_action( 'mcp_adapter_init', array( $this, 'register_mcp_server' ), 20 );
	}

	/**
	 * Registers the ability category.
	 *
	 * Called during `wp_abilities_api_categories_init`.
	 *
	 * @since 1.0.0
	 */
	public function register_category(): void {
		wp_register_ability_category(
			'extra-elementor-mcp',
			array(
				'label'       => __( 'Extra MCP Tools for Elementor', 'extra-elementor-mcp' ),
				'description' => __( 'WordPress core MCP tools for menus, SEO, media, ACF, taxonomies, site settings, and revisions.', 'extra-elementor-mcp' ),
			)
		);
	}

	/**
	 * Registers all abilities with the WordPress Abilities API.
	 *
	 * Called during `wp_abilities_api_init`.
	 *
	 * @since 1.0.0
	 */
	public function register_abilities(): void {
		$this->ability_names = $this->registrar->register_all();
	}

	/**
	 * Registers the MCP server with the MCP Adapter.
	 *
	 * Called during `mcp_adapter_init`.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP\MCP\Core\McpAdapter $mcp_adapter The MCP adapter instance.
	 */
	public function register_mcp_server( $mcp_adapter ): void {
		if ( empty( $this->ability_names ) ) {
			return;
		}

		$mcp_adapter->create_server(
			'extra-elementor-mcp-server',                                                       // server_id
			'mcp',                                                                              // route_namespace
			'extra-elementor-mcp-server',                                                       // route
			__( 'Extra MCP Tools for Elementor Server', 'extra-elementor-mcp' ),               // server_name
			__( 'Provides WordPress core MCP tools for menus, SEO, media, ACF, taxonomies, site settings, and revisions.', 'extra-elementor-mcp' ), // description
			'v' . EXTRA_ELEMENTOR_MCP_VERSION,                                                  // version
			array( \WP\MCP\Transport\HttpTransport::class ),                                    // transports
			null,                                                                               // error_handler
			null,                                                                               // observability_handler
			$this->ability_names,                                                               // tools
			array(),                                                                            // resources
			array(),                                                                            // prompts
			null                                                                                // transport_permission_callback
		);
	}

	/**
	 * Prevents cloning.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}
}
