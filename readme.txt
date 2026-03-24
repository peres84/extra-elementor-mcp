=== Extra MCP Tools for Elementor ===
Contributors: peres84
Tags: mcp, elementor, ai, wordpress, automation
Requires at least: 6.9
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Companion plugin providing WordPress core MCP tools (menus, SEO, media, ACF, taxonomies, site settings, revisions) not covered by elementor-mcp.

== Description ==

Extra MCP Tools for Elementor is a companion plugin to `elementor-mcp`. While `elementor-mcp` handles Elementor-specific operations, this plugin handles everything else — the WordPress core functionality that AI agents need to build and manage complete websites.

**Tool Groups:**

* **Page Status** — Publish pages, get page info, update page metadata (slug, parent, template, featured image)
* **Navigation Menus** — List menus, get menu items with hierarchy, update menu items, assign menus to theme locations
* **Site Settings** — Get comprehensive site overview, update title/tagline/homepage, get reading settings
* **Media Library** — List media with filters, upload files from base64 data, update alt text and captions
* **Taxonomies** — List/create categories and tags with hierarchy
* **Revisions** — List revision history, restore to a specific revision
* **Yoast SEO** *(optional)* — Get/update SEO metadata and analysis scores (only when Yoast SEO is active)
* **ACF Custom Fields** *(optional)* — List field groups, get/update ACF field values (only when ACF is active)

**Total: up to 25 MCP tools**

**Requires:**

* WordPress 6.9 or later (includes the WordPress Abilities API and MCP Adapter)
* PHP 8.0 or later

**Optional integrations:**

* Advanced Custom Fields 6.0+ (for ACF tools)
* Yoast SEO 20.0+ (for SEO tools)

**MCP Server Endpoint:**

`/wp-json/mcp/extra-elementor-mcp-server`

Add this to your `.mcp.json` alongside `elementor-mcp` for a complete AI website-building toolkit.

== Installation ==

1. Ensure WordPress 6.9 or later is installed (includes the MCP Adapter and Abilities API).
2. Upload the `extra-elementor-mcp` folder to `/wp-content/plugins/`.
3. Activate the plugin through the **Plugins** menu in WordPress.
4. Go to **Settings → Extra MCP Tools** to see the MCP endpoint URL and dependency status.
5. Add the endpoint to your `.mcp.json` configuration.

== Frequently Asked Questions ==

= Does this plugin require elementor-mcp? =

No. This plugin works independently. It complements `elementor-mcp` but does not depend on it.

= Does this plugin require Elementor? =

No. All tools work with standard WordPress. Elementor is not required.

= How do I connect this to Claude Code? =

Add the following to your `.mcp.json`:

```json
{
  "mcpServers": {
    "extra-elementor-mcp": {
      "type": "http",
      "url": "https://your-site.com/wp-json/mcp/extra-elementor-mcp-server",
      "headers": {
        "Authorization": "Basic BASE64_ENCODED_CREDENTIALS"
      }
    }
  }
}
```

Use a WordPress Application Password for the credentials.

= Are the SEO and ACF tools always available? =

No. SEO tools only register if Yoast SEO is active. ACF tools only register if Advanced Custom Fields is active.

== Changelog ==

= 1.0.0 =
* Initial release
* Page Status tools: publish-page, get-page-info, update-page-meta
* Navigation Menu tools: list-menus, get-menu, update-menu, assign-menu-location
* Site Settings tools: get-site-info, update-site-settings, get-reading-settings
* Media Library tools: list-media, upload-media, update-media-meta
* Taxonomy tools: list-categories, create-category, list-tags, create-tag
* Revision tools: list-revisions, restore-revision
* Yoast SEO tools (conditional): get-seo, update-seo, get-seo-analysis
* ACF tools (conditional): list-acf-field-groups, get-acf-fields, update-acf-fields
