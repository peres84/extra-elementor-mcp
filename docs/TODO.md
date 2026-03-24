# Extra Elementor MCP — Implementation TODO

## Phase 0 — Project Setup
- [x] Clone sample repos into `samples/` (elementor-mcp, mcp-adapter, angie-acf-mcp)
- [x] Create directory structure per PRD section 4.2
- [x] Create `extra-elementor-mcp.php` bootstrap file with plugin header, constants, dependency checks
- [x] Create `includes/class-plugin.php` singleton orchestrator with 3 WP hooks
- [x] Create `includes/class-ability-registrar.php` coordinating all ability groups
- [x] Create `readme.txt` for WordPress.org

## Phase 1 — Core (v1.0.0) — 10 tools

### Page Status (3 tools)
- [x] Create `includes/abilities/class-page-status-abilities.php`
- [x] Implement `publish-page` tool (publish/draft/pending/private)
- [x] Implement `get-page-info` tool (status, slug, template, parent, menu order, featured image)
- [x] Implement `update-page-meta` tool (slug, parent, menu_order, featured_image_id, template)

### Navigation Menus (4 tools)
- [x] Create `includes/abilities/class-menu-abilities.php`
- [x] Implement `list-menus` tool
- [x] Implement `get-menu` tool (hierarchical items)
- [x] Implement `update-menu` tool (add/remove/reorder items)
- [x] Implement `assign-menu-location` tool

### Site Settings (3 tools)
- [x] Create `includes/abilities/class-site-abilities.php`
- [x] Implement `get-site-info` tool (comprehensive site overview)
- [x] Implement `update-site-settings` tool (title, tagline, homepage, etc.)
- [x] Implement `get-reading-settings` tool

## Phase 2 — Content Management (v1.1.0) — 9 tools

### Media Library (3 tools)
- [x] Create `includes/abilities/class-media-abilities.php`
- [x] Implement `list-media` tool (with filters)
- [x] Implement `upload-media` tool (base64 + URL)
- [x] Implement `update-media-meta` tool

### Taxonomies (4 tools)
- [x] Create `includes/abilities/class-taxonomy-abilities.php`
- [x] Implement `list-categories` tool (hierarchical)
- [x] Implement `create-category` tool
- [x] Implement `list-tags` tool
- [x] Implement `create-tag` tool

### Revisions (2 tools)
- [x] Create `includes/abilities/class-revision-abilities.php`
- [x] Implement `list-revisions` tool
- [x] Implement `restore-revision` tool (destructive — add warning)

## Phase 3 — Integrations (v1.2.0) — 6 tools

### Yoast SEO (3 tools) — conditional: `defined('WPSEO_VERSION')`
- [x] Create `includes/abilities/class-seo-abilities.php`
- [x] Implement `get-seo` tool
- [x] Implement `update-seo` tool
- [x] Implement `get-seo-analysis` tool

### ACF (3 tools) — conditional: `class_exists('ACF')`
- [x] Create `includes/abilities/class-acf-abilities.php`
- [x] Implement `list-acf-field-groups` tool
- [x] Implement `get-acf-fields` tool
- [x] Implement `update-acf-fields` tool

## Phase 4 — Admin UI & Polish
- [x] Create `includes/admin/class-admin.php` settings page
- [x] Create `includes/admin/views/page-settings.php` view
- [x] Add tool group toggles (enable/disable per group)
- [x] Add dependency status panel (ACF, Yoast detected?)
- [x] Add connection info display (MCP endpoint URL)

## Phase 5 — Testing
- [x] Set up PHPUnit with WP_UnitTestCase
- [x] Unit tests for each ability class
- [x] Integration tests for tool registration + MCP server creation
- [x] Compatibility test with elementor-mcp active simultaneously
