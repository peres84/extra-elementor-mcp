# Extra Elementor MCP — Product Requirements Document

## 1. Overview

**Plugin Name:** Extra MCP Tools for Elementor
**Slug:** `extra-elementor-mcp`
**Version:** 1.0.0
**Namespace:** `extra-elementor-mcp/`
**MCP Server Endpoint:** `/wp-json/mcp/extra-elementor-mcp-server`

A companion WordPress plugin that extends the AI-powered website building workflow by providing MCP tools for WordPress core functionality **not covered** by the `elementor-mcp` plugin. While `elementor-mcp` handles Elementor-specific operations (widgets, containers, templates, page structure), this plugin handles everything else: menus, SEO, media management, page status, site settings, ACF fields, taxonomies, and revisions.

### Relationship to `elementor-mcp`

```
elementor-mcp (3rd party)          extra-elementor-mcp (ours)
├── 97 Elementor tools             ├── ~25 WordPress tools
├── Widgets, containers, layouts   ├── Menus, SEO, media, ACF
├── Templates, global styles       ├── Page status, site settings
├── elementor-mcp/ namespace       ├── extra-elementor-mcp/ namespace
└── /wp-json/mcp/elementor-mcp-server  └── /wp-json/mcp/extra-elementor-mcp-server
```

Both plugins:
- Use the **WordPress Abilities API** (`wp_register_ability()`)
- Use the **WordPress MCP Adapter** (`$mcp_adapter->create_server()`)
- Share the same authentication mechanism (WordPress Application Passwords via MCP Adapter)
- Can run simultaneously on the same WordPress site
- Are accessed via separate MCP server endpoints in `.mcp.json`

---

## 2. Goals

1. **Fill the gaps** — Provide AI agents with tools for WordPress operations that `elementor-mcp` doesn't cover
2. **Zero conflicts** — Separate namespace, separate MCP server endpoint, no shared code
3. **Same patterns** — Follow identical architecture to `elementor-mcp` so both plugins feel like one unified toolkit
4. **Modular** — Each tool group can be enabled/disabled independently
5. **Secure** — WordPress capability checks on every tool, admin role required

---

## 3. Dependencies & Requirements

| Dependency | Version | Required? |
|------------|---------|-----------|
| WordPress | 6.9+ | Yes |
| PHP | 8.0+ | Yes |
| WordPress MCP Adapter | Bundled in WP 6.9 | Yes |
| WordPress Abilities API | Bundled in WP 6.9 | Yes |
| Elementor | 3.20+ | No (some tools work without it) |
| ACF (Advanced Custom Fields) | 6.0+ | No (ACF tools only register if ACF active) |
| Yoast SEO | 20.0+ | No (SEO tools only register if Yoast active) |

---

## 4. Architecture

### 4.0 Reference Implementations (Use `samples/`)

This repository includes a `samples/` folder with example repositories that should be used as pattern references while implementing the new plugin.

- `samples/elementor-mcp/` is the primary architecture reference for hook flow, ability registration style, capability checks, server bootstrapping, and project organization.
- `samples/mcp-adapter/` is the reference for adapter/server integration patterns and transport/server layering.
- `samples/angie-acf-mcp/` is the reference for ACF-specific MCP patterns, schema definitions, REST controller structure, and TypeScript MCP server wiring.

When implementing a new module or ability, scan the relevant sample first and mirror proven patterns before introducing new abstractions.

If `samples/` (or any sample repository folder) does not exist locally, recreate it and fetch the references:

```bash
mkdir -p samples
cd samples
git clone git@github.com:elementor/angie-acf-mcp.git
git clone git@github.com:msrbuilds/elementor-mcp.git
git clone git@github.com:WordPress/mcp-adapter.git
```

### 4.1 Hook Registration Flow

Same three-hook pattern as `elementor-mcp`:

```
1. wp_abilities_api_categories_init  → Register "extra-elementor-mcp" category
2. wp_abilities_api_init             → Register all abilities via wp_register_ability()
3. mcp_adapter_init                  → Create MCP server "extra-elementor-mcp-server"
```

### 4.2 Directory Structure

```
extra-elementor-mcp/
├── extra-elementor-mcp.php              # Bootstrap: plugin header, constants, dependency checks, init
├── includes/
│   ├── class-plugin.php                 # Singleton orchestrator — hooks into 3 WP hooks
│   ├── class-ability-registrar.php      # Coordinates registration of all ability groups
│   ├── abilities/
│   │   ├── class-menu-abilities.php     # Navigation menu tools (4 tools)
│   │   ├── class-media-abilities.php    # Media library tools (3 tools)
│   │   ├── class-page-status-abilities.php  # Page/post status tools (3 tools)
│   │   ├── class-seo-abilities.php      # Yoast SEO tools (3 tools)
│   │   ├── class-acf-abilities.php      # ACF custom fields tools (3 tools)
│   │   ├── class-taxonomy-abilities.php # Categories & tags tools (4 tools)
│   │   ├── class-site-abilities.php     # Site info & settings tools (3 tools)
│   │   └── class-revision-abilities.php # Revision history tools (2 tools)
│   └── admin/
│       ├── class-admin.php              # Admin settings page with tool toggles
│       └── views/
│           └── page-settings.php        # Settings page view
└── readme.txt                           # WordPress.org readme
```

### 4.3 Plugin Bootstrap

```php
<?php
/**
 * Plugin Name: Extra MCP Tools for Elementor
 * Description: Companion plugin providing WordPress core MCP tools (menus, SEO, media, ACF, etc.)
 * Version: 1.0.0
 */

// Same pattern as elementor-mcp:
// 1. Define constants
// 2. Check dependencies (MCP Adapter, Abilities API)
// 3. require_once all class files
// 4. Boot singleton via Extra_Elementor_MCP_Plugin::instance()
// 5. Hook to plugins_loaded at priority 20
```

### 4.4 Permission Model

| Ability Group | Required WordPress Capability |
|---------------|-------------------------------|
| Menu read | `edit_theme_options` |
| Menu write | `edit_theme_options` |
| Media list | `upload_files` |
| Media upload | `upload_files` |
| Page status change | `publish_pages` |
| SEO read/write | `edit_posts` + ownership check |
| ACF read/write | `edit_posts` + ownership check |
| Taxonomy read | `edit_posts` |
| Taxonomy create | `manage_categories` |
| Site info read | `manage_options` |
| Site settings write | `manage_options` |
| Revisions read | `edit_posts` |

---

## 5. Tools Specification (25 tools)

### 5.1 Navigation Menus (4 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `list-menus` | List all registered navigation menus and their assigned theme locations | None |
| `get-menu` | Get a menu's items with hierarchy (parent/child), URLs, classes | `menu_id` |
| `update-menu` | Update menu items: add, remove, reorder, set parent/child | `menu_id`, `items[]` |
| `assign-menu-location` | Assign a menu to a theme location (primary, footer, etc.) | `menu_id`, `location` |

**Implementation Notes:**
- Use `wp_get_nav_menus()`, `wp_get_nav_menu_items()`, `wp_update_nav_menu_item()`
- `items[]` array supports: `title`, `url`, `type` (page/custom/category), `object_id`, `parent`, `position`, `classes[]`
- Return menu items as a nested tree (children inside parent objects)

### 5.2 Media Library (3 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `list-media` | List media library items with filters | `type` (image/video/document), `search`, `per_page`, `page` |
| `upload-media` | Upload a file from base64 data or URL to the media library | `file_data` (base64), `filename`, `alt_text`, `caption` |
| `update-media-meta` | Update alt text, caption, title, description on existing media | `attachment_id`, `alt_text`, `caption`, `title`, `description` |

**Implementation Notes:**
- `upload-media` accepts base64-encoded file data (solves the limitation that `sideload-image` only works with URLs)
- Use `wp_insert_attachment()`, `wp_generate_attachment_metadata()`
- `list-media` returns: `id`, `url`, `thumbnail_url`, `alt_text`, `caption`, `mime_type`, `width`, `height`, `file_size`

### 5.3 Page/Post Status Management (3 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `publish-page` | Publish a draft page/post or change status | `post_id`, `status` (publish/draft/pending/private) |
| `get-page-info` | Get page metadata: status, slug, template, parent, menu order, featured image | `post_id` |
| `update-page-meta` | Update page slug, parent, menu order, featured image, page template | `post_id`, `slug`, `parent`, `menu_order`, `featured_image_id`, `template` |

**Implementation Notes:**
- `publish-page` is the most-requested missing tool — currently requires going to wp-admin
- Use `wp_update_post()` for status changes
- `update-page-meta` handles WordPress page settings (not Elementor settings)

### 5.4 Yoast SEO (3 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `get-seo` | Get SEO metadata for a page/post | `post_id` |
| `update-seo` | Set SEO title, meta description, focus keyphrase, Open Graph data | `post_id`, `title`, `description`, `keyphrase`, `og_title`, `og_description`, `og_image_id` |
| `get-seo-analysis` | Get Yoast's readability and SEO analysis scores | `post_id` |

**Implementation Notes:**
- Only register these tools if Yoast SEO plugin is active (`defined('WPSEO_VERSION')`)
- Use Yoast's meta functions: `get_post_meta($id, '_yoast_wpseo_title')`, etc.
- For `update-seo`, use `update_post_meta()` with Yoast's meta keys: `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw`, `_yoast_wpseo_opengraph-title`, etc.
- `get-seo-analysis` returns both the SEO score and readability score

### 5.5 ACF Custom Fields (3 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `list-acf-field-groups` | List all ACF field groups with their fields and location rules | None |
| `get-acf-fields` | Get ACF field values for a specific post/page | `post_id` |
| `update-acf-fields` | Update ACF field values for a post/page | `post_id`, `fields` (key-value object) |

**Implementation Notes:**
- Only register if ACF is active (`class_exists('ACF')`)
- Use `acf_get_field_groups()`, `acf_get_fields()`, `get_fields()`, `update_field()`
- `list-acf-field-groups` returns field definitions: `name`, `type`, `choices`, `default_value`, `required`
- Handle ACF field types: text, textarea, number, select, image, gallery, repeater, group, relationship

### 5.6 Taxonomies (4 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `list-categories` | List all categories with hierarchy | `hide_empty` (bool) |
| `create-category` | Create a new category | `name`, `slug`, `parent_id`, `description` |
| `list-tags` | List all tags | `hide_empty` (bool), `search` |
| `create-tag` | Create a new tag | `name`, `slug`, `description` |

**Implementation Notes:**
- Use `get_categories()`, `wp_insert_category()`, `get_tags()`, `wp_insert_term()`
- Return category tree with parent/child relationships

### 5.7 Site Info & Settings (3 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `get-site-info` | Get site title, tagline, URL, admin email, timezone, language, active theme, active plugins list, WordPress version, PHP version | None |
| `update-site-settings` | Update site title, tagline, homepage (static page), posts page, date/time format | `title`, `tagline`, `page_on_front`, `page_for_posts`, `show_on_front` |
| `get-reading-settings` | Get homepage display settings (latest posts vs static page), posts per page | None |

**Implementation Notes:**
- `get-site-info` is a comprehensive overview tool — helps AI agents understand the WordPress environment
- `update-site-settings` can set a page as the homepage: `update_option('page_on_front', $id)` + `update_option('show_on_front', 'page')`
- Use `get_option()`, `update_option()` for WordPress settings

### 5.8 Revisions (2 tools)

| Tool Name | Description | Input |
|-----------|-------------|-------|
| `list-revisions` | List revision history for a page/post with dates, authors, and change summary | `post_id`, `per_page` |
| `restore-revision` | Restore a page/post to a specific revision | `post_id`, `revision_id` |

**Implementation Notes:**
- Use `wp_get_post_revisions()`, `wp_restore_post_revision()`
- `list-revisions` returns: `revision_id`, `date`, `author`, `title`, `excerpt` (first 200 chars of content diff)
- `restore-revision` is destructive — requires confirmation pattern in the tool description

---

## 6. Client Configuration

### `.mcp.json` (alongside existing `elementor-mcp`)

```json
{
    "mcpServers": {
        "elementor-mcp": {
            "type": "http",
            "url": "https://azulfinancialtx.com/wp-json/mcp/elementor-mcp-server",
            "headers": {
                "Authorization": "Basic <base64>"
            }
        },
        "extra-elementor-mcp": {
            "type": "http",
            "url": "https://azulfinancialtx.com/wp-json/mcp/extra-elementor-mcp-server",
            "headers": {
                "Authorization": "Basic <base64>"
            }
        }
    }
}
```

Both servers share the same WordPress Application Password credentials. The MCP Adapter handles authentication for both.

---

## 7. Conditional Tool Registration

Tools should only register when their dependencies are available:

```php
// Always register (WordPress core)
Menu abilities        → always
Media abilities       → always
Page status abilities → always
Taxonomy abilities    → always
Site abilities        → always
Revision abilities    → always

// Conditional registration
SEO abilities         → only if defined('WPSEO_VERSION')
ACF abilities         → only if class_exists('ACF')
```

---

## 8. Implementation Phases

### Phase 1 — Core (v1.0.0)

**Priority: Highest — these unblock the most common workflows**

| Group | Tools | Why |
|-------|-------|-----|
| Page Status | `publish-page`, `get-page-info`, `update-page-meta` | Can't publish pages without going to wp-admin |
| Menus | `list-menus`, `get-menu`, `update-menu`, `assign-menu-location` | Can't set up site navigation programmatically |
| Site Settings | `get-site-info`, `update-site-settings`, `get-reading-settings` | Can't set homepage or get environment info |

**Total: 10 tools**

### Phase 2 — Content Management (v1.1.0)

| Group | Tools | Why |
|-------|-------|-----|
| Media | `list-media`, `upload-media`, `update-media-meta` | Can't upload local files or manage media metadata |
| Taxonomies | `list-categories`, `create-category`, `list-tags`, `create-tag` | Can't organize content programmatically |
| Revisions | `list-revisions`, `restore-revision` | Can't undo mistakes without wp-admin |

**Total: 9 tools**

### Phase 3 — Integrations (v1.2.0)

| Group | Tools | Why |
|-------|-------|-----|
| Yoast SEO | `get-seo`, `update-seo`, `get-seo-analysis` | Can't set SEO metadata programmatically |
| ACF | `list-acf-field-groups`, `get-acf-fields`, `update-acf-fields` | Can't manage custom fields programmatically |

**Total: 6 tools**

---

## 9. Admin UI

A simple settings page at **Settings → Extra MCP Tools**:

- **Tool Toggles**: Enable/disable individual tool groups
- **Status Panel**: Show which optional dependencies are detected (ACF, Yoast)
- **Connection Info**: Display the MCP server endpoint URL for easy copy/paste

---

## 10. Security Considerations

1. **Capability checks** on every tool — never trust the request, always verify `current_user_can()`
2. **Nonce verification** handled by MCP Adapter (no custom nonce logic needed)
3. **Input sanitization** using WordPress sanitization functions (`sanitize_text_field()`, `wp_kses_post()`, etc.)
4. **Output escaping** in admin views (`esc_html()`, `esc_attr()`, `esc_url()`)
5. **No direct DB queries** — use WordPress API functions exclusively
6. **`restore-revision`** marked as destructive in tool description with clear warning
7. **File upload validation** in `upload-media` — check MIME types against WordPress allowed list

---

## 11. Testing Strategy

- **Unit tests** for each ability class using PHPUnit + WP_UnitTestCase
- **Integration tests** verifying tool registration and MCP server creation
- **Manual QA** via Claude Code: test each tool end-to-end on a staging site
- **Compatibility tests** with elementor-mcp active simultaneously

---

## 12. Future Considerations (v2.0+)

| Feature | Description |
|---------|-------------|
| WooCommerce tools | Products, orders, coupons management |
| User management | Create/update WordPress users and roles |
| Custom Post Types | Register and manage CPTs |
| Multisite support | Switch between sites in a multisite network |
| RankMath SEO | Alternative to Yoast SEO tools |
| WPML/Polylang | Multilingual content management |
| Custom fonts | Upload and register web fonts |
| Backup/restore | Full site backup and restore tools |
