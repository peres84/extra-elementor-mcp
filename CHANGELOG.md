# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added
- PHPUnit test suite with WP_Mock for unit testing without a WordPress installation (`composer.json`, `phpunit.xml.dist`, `tests/bootstrap.php`)
- Unit tests for all 8 ability classes: page status, menus, site settings, media, taxonomies, revisions, SEO, and ACF (60 tests, 122 assertions)
- Unit tests for `extra_elementor_mcp_sanitize_schema()` helper covering enum cleanup, empty properties, and recursive schemas
- Unit tests for `Extra_Elementor_MCP_Ability_Registrar` covering group toggling and the `extra_elementor_mcp_ability_names` filter
- Integration test scaffold for tool registration and MCP server creation (`tests/integration/test-tool-registration.php`) — requires WordPress test library
- Compatibility test scaffold verifying namespace, class prefix, and option name isolation vs. elementor-mcp (`tests/integration/test-compatibility.php`)
- `bin/install-wp-tests.sh` script for setting up the WordPress test environment for integration tests
- Updated `.gitignore` to exclude `vendor/`, `composer.lock`, `.phpunit.cache`, and `tests/coverage/`

### Changed
- Plugin bootstrap with dependency checks and schema sanitization (`extra-elementor-mcp.php`)
- Singleton orchestrator (`class-plugin.php`) with three-hook pattern
- Ability registrar (`class-ability-registrar.php`) coordinating all ability groups
- Page Status tools: `publish-page`, `get-page-info`, `update-page-meta`
- Navigation Menu tools: `list-menus`, `get-menu`, `update-menu`, `assign-menu-location`
- Site Settings tools: `get-site-info`, `update-site-settings`, `get-reading-settings`
- Media Library tools: `list-media`, `upload-media`, `update-media-meta`
- Taxonomy tools: `list-categories`, `create-category`, `list-tags`, `create-tag`
- Revision tools: `list-revisions`, `restore-revision`
- Yoast SEO tools (conditional): `get-seo`, `update-seo`, `get-seo-analysis`
- ACF tools (conditional): `list-acf-field-groups`, `get-acf-fields`, `update-acf-fields`
- Admin settings page scaffold (`class-admin.php`, `page-settings.php`)
- WordPress.org `readme.txt`
