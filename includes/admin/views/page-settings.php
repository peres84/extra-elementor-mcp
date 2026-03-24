<?php
/**
 * Settings page view.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mcp_endpoint = home_url( '/wp-json/mcp/extra-elementor-mcp-server' );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Extra MCP Tools for Elementor', 'extra-elementor-mcp' ); ?></h1>

	<h2><?php esc_html_e( 'Connection Info', 'extra-elementor-mcp' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'MCP Server Endpoint', 'extra-elementor-mcp' ); ?></th>
			<td>
				<code><?php echo esc_url( $mcp_endpoint ); ?></code>
				<p class="description">
					<?php esc_html_e( 'Use this URL in your .mcp.json to connect AI tools to this server.', 'extra-elementor-mcp' ); ?>
				</p>
			</td>
		</tr>
	</table>

	<h2><?php esc_html_e( 'Dependency Status', 'extra-elementor-mcp' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'WordPress MCP Adapter', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( class_exists( '\WP\MCP\Core\McpAdapter' ) ) : ?>
					<span style="color:green;">&#10003; <?php esc_html_e( 'Active', 'extra-elementor-mcp' ); ?></span>
				<?php else : ?>
					<span style="color:red;">&#10007; <?php esc_html_e( 'Not detected (required)', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'WordPress Abilities API', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( function_exists( 'wp_register_ability' ) ) : ?>
					<span style="color:green;">&#10003; <?php esc_html_e( 'Active', 'extra-elementor-mcp' ); ?></span>
				<?php else : ?>
					<span style="color:red;">&#10007; <?php esc_html_e( 'Not detected — requires WordPress 6.9+', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Yoast SEO', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
					<span style="color:green;">&#10003; <?php echo esc_html( sprintf( /* translators: %s: Yoast SEO version number */ __( 'Active (v%s) — SEO tools enabled', 'extra-elementor-mcp' ), WPSEO_VERSION ) ); ?></span>
				<?php else : ?>
					<span style="color:#888;">&#8212; <?php esc_html_e( 'Not detected — SEO tools disabled', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Advanced Custom Fields (ACF)', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( class_exists( 'ACF' ) ) : ?>
					<span style="color:green;">&#10003; <?php esc_html_e( 'Active — ACF tools enabled', 'extra-elementor-mcp' ); ?></span>
				<?php else : ?>
					<span style="color:#888;">&#8212; <?php esc_html_e( 'Not detected — ACF tools disabled', 'extra-elementor-mcp' ); ?></span>
				<?php endif; ?>
			</td>
		</tr>
	</table>

	<form action="options.php" method="post">
		<?php
		settings_fields( Extra_Elementor_MCP_Admin::SETTINGS_GROUP );
		do_settings_sections( Extra_Elementor_MCP_Admin::PAGE_SLUG );
		submit_button( __( 'Save Settings', 'extra-elementor-mcp' ) );
		?>
	</form>
</div>
