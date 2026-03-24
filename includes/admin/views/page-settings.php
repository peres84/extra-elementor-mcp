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
	<table class="form-table">
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
	<table class="form-table">
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
			<th scope="row"><?php esc_html_e( 'Yoast SEO', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
					<span style="color:green;">&#10003; <?php echo esc_html( sprintf( __( 'Active (v%s) — SEO tools enabled', 'extra-elementor-mcp' ), WPSEO_VERSION ) ); ?></span>
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

	<h2><?php esc_html_e( 'Available Tools', 'extra-elementor-mcp' ); ?></h2>
	<p><?php esc_html_e( 'The following tool groups are registered:', 'extra-elementor-mcp' ); ?></p>
	<ul>
		<li><?php esc_html_e( 'Page Status: publish-page, get-page-info, update-page-meta', 'extra-elementor-mcp' ); ?></li>
		<li><?php esc_html_e( 'Navigation Menus: list-menus, get-menu, update-menu, assign-menu-location', 'extra-elementor-mcp' ); ?></li>
		<li><?php esc_html_e( 'Site Settings: get-site-info, update-site-settings, get-reading-settings', 'extra-elementor-mcp' ); ?></li>
		<li><?php esc_html_e( 'Media Library: list-media, upload-media, update-media-meta', 'extra-elementor-mcp' ); ?></li>
		<li><?php esc_html_e( 'Taxonomies: list-categories, create-category, list-tags, create-tag', 'extra-elementor-mcp' ); ?></li>
		<li><?php esc_html_e( 'Revisions: list-revisions, restore-revision', 'extra-elementor-mcp' ); ?></li>
		<?php if ( defined( 'WPSEO_VERSION' ) ) : ?>
		<li><?php esc_html_e( 'Yoast SEO: get-seo, update-seo, get-seo-analysis', 'extra-elementor-mcp' ); ?></li>
		<?php endif; ?>
		<?php if ( class_exists( 'ACF' ) ) : ?>
		<li><?php esc_html_e( 'ACF: list-acf-field-groups, get-acf-fields, update-acf-fields', 'extra-elementor-mcp' ); ?></li>
		<?php endif; ?>
	</ul>
</div>
