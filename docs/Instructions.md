# Elementor MCP Plugin Setup for Claude Code

## Repository Note

If you are building the new plugin in this workspace, review the `samples/` folder first. It contains reference repositories with implementation patterns you can reuse:

- `samples/elementor-mcp/` for core plugin architecture and abilities patterns.
- `samples/mcp-adapter/` for MCP adapter/server integration patterns.
- `samples/angie-acf-mcp/` for ACF-focused MCP patterns.

If `samples/` or any of these folders is missing, fetch them with Git:

```bash
mkdir -p samples
cd samples
git clone git@github.com:elementor/angie-acf-mcp.git
git clone git@github.com:msrbuilds/elementor-mcp.git
git clone git@github.com:WordPress/mcp-adapter.git
```

## Prerequisites

- The **Elementor MCP** plugin must be installed and activated on the WordPress site.
- A WordPress **Application Password** must be generated (Users > Profile > Application Passwords).
- Claude Code installed on your local machine.

## Step 1: Get Your Credentials from WordPress

1. Go to the WordPress admin panel of your site.
2. Navigate to the Elementor MCP settings page (usually under the MCP menu).
3. Look for the **"Connect Your AI Client"** section.
4. Enter your **Username** and **Application Password**.
5. Click **Generate Configs** to get the Base64-encoded authorization string.
6. Copy the `Basic <base64string>` value — you'll need it for the config.

## Step 2: Create the `.mcp.json` File

In your project's root directory, create a `.mcp.json` file with the **Direct HTTP** config:

```json
{
    "mcpServers": {
        "elementor-mcp": {
            "type": "http",
            "url": "https://YOUR-DOMAIN.com/wp-json/mcp/elementor-mcp-server",
            "headers": {
                "Authorization": "Basic YOUR_BASE64_ENCODED_CREDENTIALS"
            }
        }
    }
}
```

Replace:
- `YOUR-DOMAIN.com` with your actual WordPress site domain.
- `YOUR_BASE64_ENCODED_CREDENTIALS` with the Base64 string from Step 1.

## Step 3: Verify the Connection

1. Open Claude Code in the project directory.
2. Run `/mcp` to load the MCP servers.
3. Run `/doctor` to check for errors.
4. If successful, you should see `elementor-mcp` tools available (e.g., `elementor-mcp-list-pages`).

## Common Mistakes to Avoid

### Do NOT use the `stdio` proxy config

The plugin's settings page shows a "Node.js Proxy" config as the recommended option. **This does not work for remote sites** because the `args` path points to the plugin file on the server filesystem (e.g., `/home/customer/www/.../bin/mcp-proxy.mjs`), which does not exist on your local machine.

```json
// DO NOT USE THIS for remote WordPress sites:
{
    "type": "stdio",
    "command": "node",
    "args": ["/home/customer/www/.../bin/mcp-proxy.mjs"]
}
```

### Use `url`, not `serverUrl`

Claude Code expects the field name `url` for HTTP-type MCP servers. Using `serverUrl` will cause a schema validation error in `/doctor`:

```
Does not adhere to MCP server configuration schema
```

### Do not include `env` fields with the HTTP config

The `WP_URL`, `WP_USERNAME`, `WP_APP_PASSWORD` environment variables are only for the Node.js proxy. The HTTP config uses the `Authorization` header instead.

## Generating the Base64 Credentials Manually

If you need to generate the Base64 string yourself:

```bash
echo -n 'your_username:your_application_password' | base64
```

Then use the output as: `"Authorization": "Basic <output>"`
