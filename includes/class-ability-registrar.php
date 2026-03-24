<?php
/**
 * Registers all Extra MCP Tool abilities with the WordPress Abilities API.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central registrar that coordinates registration of all ability groups.
 *
 * @since 1.0.0
 */
class Extra_Elementor_MCP_Ability_Registrar {

	/**
	 * All registered ability names.
	 *
	 * @var string[]
	 */
	private $ability_names = array();

	/**
	 * Registers all abilities across all phases.
	 *
	 * Must be called during the `wp_abilities_api_init` action.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Array of registered ability names.
	 */
	public function register_all(): array {
		// Phase 1 — Page Status (3 tools).
		$page_status = new Extra_Elementor_MCP_Page_Status_Abilities();
		$page_status->register();
		$this->ability_names = array_merge( $this->ability_names, $page_status->get_ability_names() );

		// Phase 1 — Navigation Menus (4 tools).
		$menus = new Extra_Elementor_MCP_Menu_Abilities();
		$menus->register();
		$this->ability_names = array_merge( $this->ability_names, $menus->get_ability_names() );

		// Phase 1 — Site Settings (3 tools).
		$site = new Extra_Elementor_MCP_Site_Abilities();
		$site->register();
		$this->ability_names = array_merge( $this->ability_names, $site->get_ability_names() );

		// Phase 2 — Media Library (3 tools).
		$media = new Extra_Elementor_MCP_Media_Abilities();
		$media->register();
		$this->ability_names = array_merge( $this->ability_names, $media->get_ability_names() );

		// Phase 2 — Taxonomies (4 tools).
		$taxonomies = new Extra_Elementor_MCP_Taxonomy_Abilities();
		$taxonomies->register();
		$this->ability_names = array_merge( $this->ability_names, $taxonomies->get_ability_names() );

		// Phase 2 — Revisions (2 tools).
		$revisions = new Extra_Elementor_MCP_Revision_Abilities();
		$revisions->register();
		$this->ability_names = array_merge( $this->ability_names, $revisions->get_ability_names() );

		// Phase 3 — Yoast SEO (3 tools) — conditional.
		if ( defined( 'WPSEO_VERSION' ) ) {
			$seo = new Extra_Elementor_MCP_Seo_Abilities();
			$seo->register();
			$this->ability_names = array_merge( $this->ability_names, $seo->get_ability_names() );
		}

		// Phase 3 — ACF Custom Fields (3 tools) — conditional.
		if ( class_exists( 'ACF' ) ) {
			$acf = new Extra_Elementor_MCP_Acf_Abilities();
			$acf->register();
			$this->ability_names = array_merge( $this->ability_names, $acf->get_ability_names() );
		}

		/**
		 * Filters the registered ability names.
		 *
		 * Allows other plugins to add or modify ability names.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $ability_names The registered ability names.
		 */
		$this->ability_names = apply_filters( 'extra_elementor_mcp_ability_names', $this->ability_names );

		return $this->ability_names;
	}

	/**
	 * Gets the list of registered ability names.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Array of ability names.
	 */
	public function get_ability_names(): array {
		return $this->ability_names;
	}
}
