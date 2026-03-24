# Extra Elementor MCP

[![GitHub Tag](https://img.shields.io/github/v/tag/peres84/extra-elementor-mcp?sort=semver&label=tag)](https://github.com/peres84/extra-elementor-mcp/tags)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Repo Status](https://img.shields.io/badge/status-planning-blue)](extra-elementor-mcp-prd.md)
[![WordPress](https://img.shields.io/badge/WordPress-6.9%2B-21759B?logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net)

Companion WordPress plugin that extends AI website-building workflows with MCP tools for WordPress core features not covered by [Elementor MCP](https://github.com/msrbuilds/elementor-mcp).

> Built as an extender layer for the Elementor MCP ecosystem, using WordPress MCP Adapter and Abilities API patterns.

## Extender Role In The MCP Stack

This project is an extender plugin for the existing tools used in this workflow, not a replacement for them.

- Extends [Elementor MCP](https://github.com/msrbuilds/elementor-mcp) with WordPress-core operational abilities.
- Integrates through [WordPress MCP Adapter](https://github.com/WordPress/mcp-adapter) and WordPress Abilities API patterns.
- Complements optional ecosystem plugins such as ACF and Yoast SEO when they are installed.

## Project Description

Extra Elementor MCP adds a second MCP server focused on site operations around Elementor pages, including menus, media, SEO metadata, page status, taxonomies, ACF fields, and site settings.

The goal is to pair with the existing Elementor MCP plugin and adapter stack:

- [Elementor MCP](https://github.com/msrbuilds/elementor-mcp): layout, widgets, templates, and Elementor-specific editing.
- [WordPress MCP Adapter](https://github.com/WordPress/mcp-adapter): server transport and MCP server lifecycle integration.
- Extra Elementor MCP: WordPress core operations and content-management tooling as the extender layer.

Together, they allow an AI agent to both design pages and manage the surrounding WordPress configuration without jumping back to wp-admin for common tasks.

## Why This Exists

Elementor MCP is strong at visual/page structure automation, but real production workflows also need non-Elementor actions such as:

- Publishing drafts and managing page metadata.
- Managing navigation menus and menu locations.
- Updating SEO fields and reading SEO analysis.
- Handling media metadata and uploads.
- Managing ACF fields, categories, tags, and site-level settings.

This repository defines that missing layer as a dedicated plugin and MCP server.

## Planned Scope

Initial target is a modular set of WordPress-focused MCP abilities grouped into:

- Navigation menus
- Media library
- Page/post status and metadata
- Yoast SEO integration (optional when plugin is active)
- ACF integration (optional when plugin is active)
- Taxonomy management
- Site info and reading/home settings
- Revision history helpers

See the full requirements in [extra-elementor-mcp-prd.md](extra-elementor-mcp-prd.md).

## Architecture Summary

The plugin follows the same architectural patterns used by Elementor MCP and WordPress MCP Adapter:

- Register category on `wp_abilities_api_categories_init`.
- Register abilities on `wp_abilities_api_init`.
- Create MCP server on `mcp_adapter_init`.

Namespace and endpoint are intentionally separate to avoid conflicts:

- Namespace: `extra-elementor-mcp/`
- MCP endpoint: `/wp-json/mcp/extra-elementor-mcp-server`

## Repository Layout

- [extra-elementor-mcp-prd.md](extra-elementor-mcp-prd.md): product requirements and tool definitions.
- [docs/Instructions.md](docs/Instructions.md): setup and connection guidance.
- [docs/tools.md](docs/tools.md): reference for current Elementor MCP tooling and identified gaps.
- [samples](samples): reference repositories used to mirror proven implementation patterns.

## External References

- [Elementor MCP repository](https://github.com/msrbuilds/elementor-mcp)
- [WordPress MCP Adapter repository](https://github.com/WordPress/mcp-adapter)
- [angie-acf-mcp repository](https://github.com/elementor/angie-acf-mcp)
- [Shields.io badges](https://shields.io/badges/git-hub-tag)

## Samples And References

Use the repositories under [samples](samples) as implementation references before introducing new abstractions:

- [samples/elementor-mcp](samples/elementor-mcp)
- [samples/mcp-adapter](samples/mcp-adapter)
- [samples/angie-acf-mcp](samples/angie-acf-mcp)

If they are missing locally:

```bash
mkdir -p samples
cd samples
git clone git@github.com:elementor/angie-acf-mcp.git
git clone git@github.com:msrbuilds/elementor-mcp.git
git clone git@github.com:WordPress/mcp-adapter.git
```

## Status

Planning and specification stage. The PRD is the source of truth for implementation milestones and ability contracts.

## Contributing

Contributions are welcome. This repository is specification-first, so changes should stay aligned with the PRD and upstream patterns.

1. Read [extra-elementor-mcp-prd.md](extra-elementor-mcp-prd.md) before coding.
2. Review matching references in [samples](samples) and follow established hook and server patterns.
3. Keep changes small and focused by ability group.
4. Enforce explicit WordPress capability checks in every ability.
5. Document any behavioral or schema changes in the PR description.
6. Prefer compatibility-preserving changes because this project extends existing MCP tooling used in production workflows.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE).

<!-- push test: verified -->