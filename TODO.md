# Extra Elementor MCP — Implementation TODO

## Phase 0 — Project Setup
- [ ] Clone sample repos into `samples/` (elementor-mcp, mcp-adapter, angie-acf-mcp)
- [ ] Create directory structure per PRD section 4.2
- [ ] Create `extra-elementor-mcp.php` bootstrap file with plugin header, constants, dependency checks
- [ ] Create `includes/class-plugin.php` singleton orchestrator with 3 WP hooks
- [ ] Create `includes/class-ability-registrar.php` coordinating all ability groups
- [ ] Create `readme.txt` for WordPress.org

## Phase 1 — Core (v1.0.0) — 10 tools

### Page Status (3 tools)
- [ ] Create `includes/abilities/class-page-status-abilities.php`
- [ ] Implement `publish-page` tool (publish/draft/pending/private)
- [ ] Implement `get-page-info` tool (status, slug, template, parent, menu order, featured image)
- [ ] Implement `update-page-meta` tool (slug, parent, menu_order, featured_image_id, template)

### Navigation Menus (4 tools)
- [ ] Create `includes/abilities/class-menu-abilities.php`
- [ ] Implement `list-menus` tool
- [ ] Implement `get-menu` tool (hierarchical items)
- [ ] Implement `update-menu` tool (add/remove/reorder items)
- [ ] Implement `assign-menu-location` tool

### Site Settings (3 tools)
- [ ] Create `includes/abilities/class-site-abilities.php`
- [ ] Implement `get-site-info` tool (comprehensive site overview)
- [ ] Implement `update-site-settings` tool (title, tagline, homepage, etc.)
- [ ] Implement `get-reading-settings` tool

## Phase 2 — Content Management (v1.1.0) — 9 tools

### Media Library (3 tools)
- [ ] Create `includes/abilities/class-media-abilities.php`
- [ ] Implement `list-media` tool (with filters)
- [ ] Implement `upload-media` tool (base64 + URL)
- [ ] Implement `update-media-meta` tool

### Taxonomies (4 tools)
- [ ] Create `includes/abilities/class-taxonomy-abilities.php`
- [ ] Implement `list-categories` tool (hierarchical)
- [ ] Implement `create-category` tool
- [ ] Implement `list-tags` tool
- [ ] Implement `create-tag` tool

### Revisions (2 tools)
- [ ] Create `includes/abilities/class-revision-abilities.php`
- [ ] Implement `list-revisions` tool
- [ ] Implement `restore-revision` tool (destructive — add warning)

## Phase 3 — Integrations (v1.2.0) — 6 tools

### Yoast SEO (3 tools) — conditional: `defined('WPSEO_VERSION')`
- [ ] Create `includes/abilities/class-seo-abilities.php`
- [ ] Implement `get-seo` tool
- [ ] Implement `update-seo` tool
- [ ] Implement `get-seo-analysis` tool

### ACF (3 tools) — conditional: `class_exists('ACF')`
- [ ] Create `includes/abilities/class-acf-abilities.php`
- [ ] Implement `list-acf-field-groups` tool
- [ ] Implement `get-acf-fields` tool
- [ ] Implement `update-acf-fields` tool

## Phase 4 — Admin UI & Polish
- [ ] Create `includes/admin/class-admin.php` settings page
- [ ] Create `includes/admin/views/page-settings.php` view
- [ ] Add tool group toggles (enable/disable per group)
- [ ] Add dependency status panel (ACF, Yoast detected?)
- [ ] Add connection info display (MCP endpoint URL)

## Phase 5 — Testing
- [ ] Set up PHPUnit with WP_UnitTestCase
- [ ] Unit tests for each ability class
- [ ] Integration tests for tool registration + MCP server creation
- [ ] Compatibility test with elementor-mcp active simultaneously
