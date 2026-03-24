# CLAUDE.md — Extra Elementor MCP

## Project Overview

WordPress plugin that provides MCP tools for WordPress core functionality not covered by `elementor-mcp`. Extends the AI website-building workflow with tools for menus, SEO, media, page status, ACF, taxonomies, site settings, and revisions.

## Key Architecture

- **Three-hook pattern**: `wp_abilities_api_categories_init` → `wp_abilities_api_init` → `mcp_adapter_init`
- **Namespace**: `extra-elementor-mcp/`
- **MCP Endpoint**: `/wp-json/mcp/extra-elementor-mcp-server`
- **Singleton orchestrator** in `includes/class-plugin.php`
- **Ability registrar** in `includes/class-ability-registrar.php` coordinates all ability groups

## Implementation Rules

1. **Always check samples first** — Before implementing any module, read the matching pattern in `samples/elementor-mcp/`, `samples/mcp-adapter/`, or `samples/angie-acf-mcp/`
2. **Mirror proven patterns** — Follow the exact hook flow, ability registration style, and capability checks from samples
3. **WordPress API only** — No direct DB queries. Use `wp_update_post()`, `get_option()`, etc.
4. **Capability checks on every tool** — Always verify `current_user_can()` before executing
5. **Sanitize all input** — Use `sanitize_text_field()`, `wp_kses_post()`, `absint()`, etc.
6. **Escape all output** — Use `esc_html()`, `esc_attr()`, `esc_url()` in admin views
7. **Conditional registration** — SEO tools only if `defined('WPSEO_VERSION')`, ACF tools only if `class_exists('ACF')`
8. **One ability group per file** — Each class-*-abilities.php handles one group

## TODO Tracking

Implementation progress is tracked in `TODO.md`. When working on tasks:
- Pick the next unchecked item in order (Phase 0 → 1 → 2 → 3 → 4 → 5)
- Mark items `[x]` as you complete them
- Commit after completing each logical group (e.g., all Page Status tools)
- Update TODO.md in the same commit

## Git Workflow (Remote Agents)

Remote agents (scheduled triggers) MUST follow this git workflow:

1. **Pull first** — `git pull --rebase origin main` before starting any work
2. **Do the work** — implement the task group
3. **Commit** — commit all changes with a descriptive message
4. **Pull again** — `git pull --rebase origin main` to catch any changes pushed while working
5. **Resolve conflicts** — if rebase conflicts occur, resolve them cleanly
6. **Push** — `git push origin main`
7. **If push fails** — pull rebase again and retry. If unresolvable, document the issue in `KNOWN-ISSUES.md`

## Changelog

All changes must be logged in `CHANGELOG.md` using [Keep a Changelog](https://keepachangelog.com/) format. When committing work:
- Add entries under `[Unreleased]` with categories: Added, Changed, Fixed, Removed
- Move entries to a versioned section when releasing

## Known Issues

Unresolved problems (failed pushes, merge conflicts, bugs found during self-review) must be documented in `KNOWN-ISSUES.md` with:
- Date discovered
- Description of the issue
- Steps to reproduce (if applicable)
- Workaround (if any)

## File Structure

```
extra-elementor-mcp/
├── CLAUDE.md                              # This file
├── TODO.md                                # Implementation tracker
├── extra-elementor-mcp-prd.md             # Full PRD (source of truth)
├── extra-elementor-mcp.php                # Bootstrap file
├── includes/
│   ├── class-plugin.php                   # Singleton orchestrator
│   ├── class-ability-registrar.php        # Coordinates all groups
│   ├── abilities/
│   │   ├── class-menu-abilities.php
│   │   ├── class-media-abilities.php
│   │   ├── class-page-status-abilities.php
│   │   ├── class-seo-abilities.php
│   │   ├── class-acf-abilities.php
│   │   ├── class-taxonomy-abilities.php
│   │   ├── class-site-abilities.php
│   │   └── class-revision-abilities.php
│   └── admin/
│       ├── class-admin.php
│       └── views/
│           └── page-settings.php
├── CHANGELOG.md                           # Keep a Changelog format
├── KNOWN-ISSUES.md                        # Unresolved issues tracker
├── docs/
├── samples/                               # Reference repos (gitignored)
└── readme.txt
```

## Commands

- Clone samples: `cd samples && git clone git@github.com:elementor/angie-acf-mcp.git && git clone git@github.com:msrbuilds/elementor-mcp.git && git clone git@github.com:WordPress/mcp-adapter.git`

## PRD Reference

Full specification in `extra-elementor-mcp-prd.md`. The PRD defines:
- All 25 tools with inputs/outputs (Section 5)
- Permission model (Section 4.4)
- Implementation phases (Section 8)
- Security requirements (Section 10)
