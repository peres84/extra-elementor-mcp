<?php
/**
 * Connection info tab view.
 *
 * Displays MCP connection configurations for various AI clients.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$extra_mcp_endpoint    = rest_url( 'mcp/extra-elementor-mcp-server' );
$extra_mcp_has_adapter = class_exists( '\WP\MCP\Core\McpAdapter' );
?>

<div class="extra-mcp-connection">

	<!-- Server Status -->
	<div class="extra-mcp-section">
		<h2><?php esc_html_e( 'Server Status', 'extra-elementor-mcp' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Current status of your MCP server and connected components.', 'extra-elementor-mcp' ); ?></p>

		<div class="extra-mcp-status-grid">
			<div class="extra-mcp-status-card">
				<span class="extra-mcp-status-card-icon extra-mcp-status-card-icon--ok">
					<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
				</span>
				<span class="extra-mcp-status-card-info">
					<span class="extra-mcp-status-card-label"><?php esc_html_e( 'Extra MCP Tools', 'extra-elementor-mcp' ); ?></span>
					<span class="extra-mcp-status-card-value"><?php esc_html_e( 'Active', 'extra-elementor-mcp' ); ?></span>
				</span>
			</div>

			<div class="extra-mcp-status-card">
				<span class="extra-mcp-status-card-icon <?php echo esc_attr( $extra_mcp_has_adapter ? 'extra-mcp-status-card-icon--ok' : 'extra-mcp-status-card-icon--warn' ); ?>">
					<?php if ( $extra_mcp_has_adapter ) : ?>
						<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
					<?php else : ?>
						<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
					<?php endif; ?>
				</span>
				<span class="extra-mcp-status-card-info">
					<span class="extra-mcp-status-card-label"><?php esc_html_e( 'MCP Adapter', 'extra-elementor-mcp' ); ?></span>
					<span class="extra-mcp-status-card-value"><?php echo esc_html( $extra_mcp_has_adapter ? __( 'Active', 'extra-elementor-mcp' ) : __( 'Not Active', 'extra-elementor-mcp' ) ); ?></span>
				</span>
			</div>

			<div class="extra-mcp-status-card">
				<span class="extra-mcp-status-card-icon extra-mcp-status-card-icon--ok">
					<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
				</span>
				<span class="extra-mcp-status-card-info">
					<span class="extra-mcp-status-card-label"><?php esc_html_e( 'Tool Groups', 'extra-elementor-mcp' ); ?></span>
					<span class="extra-mcp-status-card-value"><?php esc_html_e( '8 groups / 25 tools', 'extra-elementor-mcp' ); ?></span>
				</span>
			</div>
		</div>

		<div class="extra-mcp-endpoint">
			<code><?php echo esc_html( $extra_mcp_endpoint ); ?></code>
			<button type="button" class="button extra-mcp-copy-btn" data-target="extra-mcp-endpoint-copy"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
			<textarea id="extra-mcp-endpoint-copy" class="extra-mcp-copy-source"><?php echo esc_html( $extra_mcp_endpoint ); ?></textarea>
		</div>
	</div>

	<!-- Connect Your AI Client -->
	<div class="extra-mcp-section">
		<h2><?php esc_html_e( 'Connect Your AI Client', 'extra-elementor-mcp' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Connect to this site from any AI client using HTTP. No proxy or Node.js needed — just an Application Password.', 'extra-elementor-mcp' ); ?>
		</p>

		<h3><?php esc_html_e( 'Step 1: Generate Your Credentials', 'extra-elementor-mcp' ); ?></h3>
		<p class="description">
			<?php
			printf(
				/* translators: %s: link to application passwords */
				esc_html__( 'Enter your username and Application Password (create one at %s).', 'extra-elementor-mcp' ),
				'<a href="' . esc_url( admin_url( 'profile.php#application-passwords-section' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Users &rsaquo; Profile', 'extra-elementor-mcp' ) . '</a>'
			);
			?>
		</p>

		<div class="extra-mcp-cred-form">
			<div class="extra-mcp-cred-field">
				<label for="extra-mcp-b64-username"><?php esc_html_e( 'Username', 'extra-elementor-mcp' ); ?></label>
				<input type="text" id="extra-mcp-b64-username" value="<?php echo esc_attr( wp_get_current_user()->user_login ); ?>" />
			</div>
			<div class="extra-mcp-cred-field">
				<label for="extra-mcp-b64-app-password"><?php esc_html_e( 'Application Password', 'extra-elementor-mcp' ); ?></label>
				<input type="text" id="extra-mcp-b64-app-password" placeholder="xxxx xxxx xxxx xxxx xxxx xxxx" />
				<p class="description">
					<?php
					printf(
						/* translators: %s: link */
						esc_html__( 'Create one at %s', 'extra-elementor-mcp' ),
						'<a href="' . esc_url( admin_url( 'profile.php#application-passwords-section' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Application Passwords', 'extra-elementor-mcp' ) . '</a>'
					);
					?>
				</p>
			</div>
			<button type="button" class="button extra-mcp-generate-btn" id="extra-mcp-generate-b64"><?php esc_html_e( 'Generate Configs', 'extra-elementor-mcp' ); ?></button>

			<div id="extra-mcp-b64-result-row" style="display: none;">
				<div class="extra-mcp-auth-result">
					<code id="extra-mcp-b64-result"></code>
					<button type="button" class="button extra-mcp-copy-btn" data-target="extra-mcp-b64-result-copy"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
					<textarea id="extra-mcp-b64-result-copy" class="extra-mcp-copy-source"></textarea>
				</div>
			</div>
		</div>

		<div id="extra-mcp-http-configs" style="display: none;">

			<h3><?php esc_html_e( 'Step 2: Copy the config for your AI client', 'extra-elementor-mcp' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Paste the configuration into the appropriate file for your AI client.', 'extra-elementor-mcp' ); ?>
			</p>

			<!-- Claude Code -->
			<div class="extra-mcp-config-card">
				<div class="extra-mcp-config-card-header">
					<span class="extra-mcp-config-card-title"><?php esc_html_e( 'Claude Code', 'extra-elementor-mcp' ); ?> <span style="font-weight: 400; color: #9ca3af;">&mdash; .mcp.json</span></span>
					<button type="button" class="button extra-mcp-copy-btn" data-target="claude-code-http"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
				</div>
				<pre><code id="extra-mcp-claude-code-http-code"></code></pre>
				<textarea id="claude-code-http" class="extra-mcp-copy-source"></textarea>
			</div>

			<!-- Claude Desktop -->
			<div class="extra-mcp-config-card">
				<div class="extra-mcp-config-card-header">
					<span class="extra-mcp-config-card-title"><?php esc_html_e( 'Claude Desktop', 'extra-elementor-mcp' ); ?> <span style="font-weight: 400; color: #9ca3af;">&mdash; claude_desktop_config.json</span></span>
					<button type="button" class="button extra-mcp-copy-btn" data-target="claude-desktop-http"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
				</div>
				<pre><code id="extra-mcp-claude-desktop-http-code"></code></pre>
				<textarea id="claude-desktop-http" class="extra-mcp-copy-source"></textarea>
			</div>

			<!-- Cursor -->
			<div class="extra-mcp-config-card">
				<div class="extra-mcp-config-card-header">
					<span class="extra-mcp-config-card-title"><?php esc_html_e( 'Cursor', 'extra-elementor-mcp' ); ?> <span style="font-weight: 400; color: #9ca3af;">&mdash; .cursor/mcp.json</span></span>
					<button type="button" class="button extra-mcp-copy-btn" data-target="cursor-config"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
				</div>
				<pre><code id="extra-mcp-cursor-code"></code></pre>
				<textarea id="cursor-config" class="extra-mcp-copy-source"></textarea>
			</div>

			<!-- Windsurf -->
			<div class="extra-mcp-config-card">
				<div class="extra-mcp-config-card-header">
					<span class="extra-mcp-config-card-title"><?php esc_html_e( 'Windsurf', 'extra-elementor-mcp' ); ?> <span style="font-weight: 400; color: #9ca3af;">&mdash; mcp_config.json</span></span>
					<button type="button" class="button extra-mcp-copy-btn" data-target="windsurf-config"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
				</div>
				<pre><code id="extra-mcp-windsurf-code"></code></pre>
				<textarea id="windsurf-config" class="extra-mcp-copy-source"></textarea>
			</div>

			<!-- Codex -->
			<div class="extra-mcp-config-card">
				<div class="extra-mcp-config-card-header">
					<span class="extra-mcp-config-card-title"><?php esc_html_e( 'Codex', 'extra-elementor-mcp' ); ?> <span style="font-weight: 400; color: #9ca3af;">&mdash; config.toml</span></span>
					<button type="button" class="button extra-mcp-copy-btn" data-target="codex-config"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
				</div>
				<pre><code id="extra-mcp-codex-code"></code></pre>
				<textarea id="codex-config" class="extra-mcp-copy-source"></textarea>
			</div>

			<!-- npx mcp-remote -->
			<div class="extra-mcp-config-card">
				<div class="extra-mcp-config-card-header">
					<span class="extra-mcp-config-card-title"><?php esc_html_e( 'npx mcp-remote', 'extra-elementor-mcp' ); ?> <span style="font-weight: 400; color: #9ca3af;">&mdash; <?php esc_html_e( 'any stdio client', 'extra-elementor-mcp' ); ?></span></span>
					<button type="button" class="button extra-mcp-copy-btn" data-target="mcp-remote-config"><?php esc_html_e( 'Copy', 'extra-elementor-mcp' ); ?></button>
				</div>
				<pre><code id="extra-mcp-mcp-remote-code"></code></pre>
				<textarea id="mcp-remote-config" class="extra-mcp-copy-source"></textarea>
			</div>

		</div>
	</div>

</div>
