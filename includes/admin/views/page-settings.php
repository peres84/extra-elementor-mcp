<?php
/**
 * Settings page view with tabbed interface.
 *
 * @package Extra_Elementor_MCP
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$extra_mcp_current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'connection'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$extra_mcp_tabs        = array(
	'connection' => __( 'Connection', 'extra-elementor-mcp' ),
	'tools'      => __( 'Tools', 'extra-elementor-mcp' ),
);
?>
<div class="wrap extra-mcp-admin">
	<h1><?php esc_html_e( 'Extra MCP Tools for Elementor', 'extra-elementor-mcp' ); ?></h1>

	<nav class="extra-mcp-tabs">
		<?php foreach ( $extra_mcp_tabs as $extra_mcp_tab_key => $extra_mcp_tab_label ) : ?>
			<a href="<?php echo esc_url( admin_url( 'options-general.php?page=' . Extra_Elementor_MCP_Admin::PAGE_SLUG . '&tab=' . $extra_mcp_tab_key ) ); ?>"
			   class="extra-mcp-tab <?php echo $extra_mcp_current_tab === $extra_mcp_tab_key ? 'is-active' : ''; ?>">
				<?php echo esc_html( $extra_mcp_tab_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<div class="extra-mcp-tab-content">
		<?php if ( 'connection' === $extra_mcp_current_tab ) : ?>
			<?php require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/admin/views/page-connection.php'; ?>
		<?php elseif ( 'tools' === $extra_mcp_current_tab ) : ?>
			<?php require_once EXTRA_ELEMENTOR_MCP_DIR . 'includes/admin/views/page-tools.php'; ?>
		<?php endif; ?>
	</div>
</div>
