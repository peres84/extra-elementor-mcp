<?php
/**
 * Settings page view — Extra MCP Tools for Elementor.
 *
 * Rendered by Extra_Elementor_MCP_Admin::render_settings_page().
 * Capability check is performed by the caller; do not repeat it here.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mcp_endpoint    = home_url( '/wp-json/mcp/extra-elementor-mcp-server' );
$enabled_groups  = Extra_Elementor_MCP_Admin::get_enabled_groups();
$tool_groups     = Extra_Elementor_MCP_Admin::TOOL_GROUPS;
$option_name     = Extra_Elementor_MCP_Admin::OPTION_NAME;
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Extra MCP Tools for Elementor', 'extra-elementor-mcp' ); ?></h1>

	<?php settings_errors( 'extra_elementor_mcp_settings_group' ); ?>

	<!-- =========================================================
	     Connection Info
	     ========================================================= -->
	<h2><?php esc_html_e( 'Connection Info', 'extra-elementor-mcp' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'MCP Server Endpoint', 'extra-elementor-mcp' ); ?></th>
			<td>
				<code><?php echo esc_url( $mcp_endpoint ); ?></code>
				<p class="description">
					<?php esc_html_e( 'Use this URL in your .mcp.json to connect AI tools to this server.', 'extra-elementor-mcp' ); ?>
				</p>
				<p class="description">
					<?php
					/* translators: %s: code snippet example */
					echo wp_kses(
						sprintf(
							__( 'Example: <code>"url": "%s"</code>', 'extra-elementor-mcp' ),
							esc_url( $mcp_endpoint )
						),
						array( 'code' => array() )
					);
					?>
				</p>
			</td>
		</tr>
	</table>

	<!-- =========================================================
	     Dependency Status
	     ========================================================= -->
	<h2><?php esc_html_e( 'Dependency Status', 'extra-elementor-mcp' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'WordPress MCP Adapter', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( class_exists( '\WP\MCP\Core\McpAdapter' ) ) : ?>
					<span style="color:#46b450;">&#10003; <?php esc_html_e( 'Active (required)', 'extra-elementor-mcp' ); ?></span>
				<?php else : ?>
					<span style="color:#dc3232;">&#10007; <?php esc_html_e( 'Not detected — required for MCP tools to work', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'WordPress Abilities API', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( function_exists( 'wp_register_ability' ) ) : ?>
					<span style="color:#46b450;">&#10003; <?php esc_html_e( 'Available (WordPress 6.9+)', 'extra-elementor-mcp' ); ?></span>
				<?php else : ?>
					<span style="color:#dc3232;">&#10007; <?php esc_html_e( 'Not available — upgrade to WordPress 6.9+', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Yoast SEO', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
					<span style="color:#46b450;">&#10003;
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: Yoast SEO version number */
							__( 'Active (v%s) — SEO tools enabled', 'extra-elementor-mcp' ),
							WPSEO_VERSION
						)
					);
					?>
					</span>
				<?php else : ?>
					<span style="color:#888;">&#8212; <?php esc_html_e( 'Not detected — SEO tools will not register', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Advanced Custom Fields (ACF)', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( class_exists( 'ACF' ) ) : ?>
					<span style="color:#46b450;">&#10003; <?php esc_html_e( 'Active — ACF tools enabled', 'extra-elementor-mcp' ); ?></span>
				<?php else : ?>
					<span style="color:#888;">&#8212; <?php esc_html_e( 'Not detected — ACF tools will not register', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
	</table>

	<!-- =========================================================
	     Tool Group Toggles
	     ========================================================= -->
	<h2><?php esc_html_e( 'Tool Group Toggles', 'extra-elementor-mcp' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Enable or disable individual tool groups. Disabled groups are not registered with the MCP server. Changes take effect immediately after saving.', 'extra-elementor-mcp' ); ?>
	</p>

	<form method="post" action="options.php">
		<?php settings_fields( 'extra_elementor_mcp_settings_group' ); ?>

		<table class="form-table" role="presentation">
			<?php foreach ( $tool_groups as $group_key => $group_label ) : ?>
				<?php
				$is_enabled = in_array( $group_key, $enabled_groups, true );

				// Determine whether this group has an unmet dependency.
				$dependency_unmet = false;
				if ( 'seo' === $group_key && ! defined( 'WPSEO_VERSION' ) ) {
					$dependency_unmet = true;
				}
				if ( 'acf' === $group_key && ! class_exists( 'ACF' ) ) {
					$dependency_unmet = true;
				}
				?>
				<tr>
					<th scope="row">
						<label for="emcp-group-<?php echo esc_attr( $group_key ); ?>">
							<?php echo esc_html( ucwords( str_replace( '_', ' ', $group_key ) ) ); ?>
						</label>
					</th>
					<td>
						<label>
							<input
								type="checkbox"
								id="emcp-group-<?php echo esc_attr( $group_key ); ?>"
								name="<?php echo esc_attr( $option_name ); ?>[<?php echo esc_attr( $group_key ); ?>]"
								value="1"
								<?php checked( $is_enabled ); ?>
								<?php disabled( $dependency_unmet ); ?>
							>
							<?php echo esc_html( $group_label ); ?>
						</label>
						<?php if ( $dependency_unmet ) : ?>
							<p class="description" style="color:#888;">
								<?php esc_html_e( 'Unavailable — required plugin is not active.', 'extra-elementor-mcp' ); ?>
							</p>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<?php submit_button( __( 'Save Settings', 'extra-elementor-mcp' ) ); ?>
	</form>
</div>
