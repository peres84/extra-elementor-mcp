# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added
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
