# MCP Server Installation Fix — Summary

## Problem

The `elementor-mcp` and `extra-elementor-mcp` servers were configured in `~/.claude/mcp.json`, but Claude Code **doesn't read that file**. The `fal-ai` server was installed but scoped to a different project directory.

## Root Cause

Claude Code stores MCP config in **`~/.claude.json`** (not `~/.claude/mcp.json`), with two scopes:

- **Global** (`-s user`): available in all projects
- **Project-scoped** (default): only available in the working directory where it was added

## Solution

Use the `claude mcp add` CLI command with the correct flags:

```bash
# Add HTTP MCP server globally (available everywhere)
claude mcp add --transport http -s user <name> <url> --header "Authorization: Bearer <token>"

# Add HTTP MCP server to current project only (default)
claude mcp add --transport http <name> <url> --header "Authorization: <credentials>"
```

## Commands Used

```bash
# Elementor servers (project-scoped)
claude mcp add --transport http extra-elementor-mcp "https://azulfinancialtx.com/wp-json/mcp/extra-elementor-mcp-server" --header "Authorization: Basic <base64>"

claude mcp add --transport http elementor-mcp "https://azulfinancialtx.com/wp-json/mcp/elementor-mcp-server" --header "Authorization: Basic <base64>"

# fal-ai (global)
claude mcp add --transport http -s user fal-ai "https://mcp.fal.ai/mcp" --header "Authorization: Bearer <key>"
```

## Verify

```bash
claude mcp list
```

## Key Takeaway

Never manually create `~/.claude/mcp.json` — always use `claude mcp add` to ensure the config lands in the right place.
