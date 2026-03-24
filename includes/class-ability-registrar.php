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
 * Before registering each group it checks the admin toggle stored in the
 * `extra_elementor_mcp_settings` option. Groups that are disabled by the
 * site admin (or whose required plugin is absent) are skipped entirely.
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
		if ( $this->is_group_enabled( 'page_status' ) ) {
			$page_status = new Extra_Elementor_MCP_Page_Status_Abilities();
			$page_status->register();
			$this->ability_names = array_merge( $this->ability_names, $page_status->get_ability_names() );
		}

		// Phase 1 — Navigation Menus (4 tools).
		if ( $this->is_group_enabled( 'menus' ) ) {
			$menus = new Extra_Elementor_MCP_Menu_Abilities();
			$menus->register();
			$this->ability_names = array_merge( $this->ability_names, $menus->get_ability_names() );
		}

		// Phase 1 — Site Settings (3 tools).
		if ( $this->is_group_enabled( 'site' ) ) {
			$site = new Extra_Elementor_MCP_Site_Abilities();
			$site->register();
			$this->ability_names = array_merge( $this->ability_names, $site->get_ability_names() );
		}

		// Phase 2 — Media Library (3 tools).
		if ( $this->is_group_enabled( 'media' ) ) {
			$media = new Extra_Elementor_MCP_Media_Abilities();
			$media->register();
			$this->ability_names = array_merge( $this->ability_names, $media->get_ability_names() );
		}

		// Phase 2 — Taxonomies (4 tools).
		if ( $this->is_group_enabled( 'taxonomies' ) ) {
			$taxonomies = new Extra_Elementor_MCP_Taxonomy_Abilities();
			$taxonomies->register();
			$this->ability_names = array_merge( $this->ability_names, $taxonomies->get_ability_names() );
		}

		// Phase 2 — Revisions (2 tools).
		if ( $this->is_group_enabled( 'revisions' ) ) {
			$revisions = new Extra_Elementor_MCP_Revision_Abilities();
			$revisions->register();
			$this->ability_names = array_merge( $this->ability_names, $revisions->get_ability_names() );
		}

		// Phase 3 — Yoast SEO (3 tools) — conditional on plugin and admin toggle.
		if ( defined( 'WPSEO_VERSION' ) && $this->is_group_enabled( 'seo' ) ) {
			$seo = new Extra_Elementor_MCP_Seo_Abilities();
			$seo->register();
			$this->ability_names = array_merge( $this->ability_names, $seo->get_ability_names() );
		}

		// Phase 3 — ACF Custom Fields (3 tools) — conditional on plugin and admin toggle.
		if ( class_exists( 'ACF' ) && $this->is_group_enabled( 'acf' ) ) {
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

	/**
	 * Checks whether a specific tool group is enabled in the admin settings.
	 *
	 * Reads directly from the WordPress options table so this works on both
	 * the admin and front-end request paths (the Admin class may not be loaded
	 * outside of wp-admin).
	 *
	 * Defaults to true (enabled) when the option or key is absent, ensuring
	 * all tools are exposed on a fresh installation.
	 *
	 * @since 1.0.0
	 *
	 * @param string $group Tool group key (e.g. 'page_status', 'menus').
	 * @return bool True if the group is enabled.
	 */
	private function is_group_enabled( string $group ): bool {
		$settings = get_option( 'extra_elementor_mcp_settings', array() );

		if ( ! isset( $settings['enabled_groups'][ $group ] ) ) {
			return true; // Default: enabled.
		}

		return (bool) $settings['enabled_groups'][ $group ];
	}
}
