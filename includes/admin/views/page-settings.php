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
$settings     = get_option( Extra_Elementor_MCP_Admin::OPTION_NAME, Extra_Elementor_MCP_Admin::get_defaults() );

/**
 * Human-readable labels for each tool group toggle.
 *
 * Keys must match the constants in Extra_Elementor_MCP_Admin::GROUPS.
 */
$group_labels = array(
	'page_status' => array(
		'label'       => __( 'Page Status', 'extra-elementor-mcp' ),
		'description' => __( 'publish-page, get-page-info, update-page-meta', 'extra-elementor-mcp' ),
		'required'    => false,
	),
	'menus'       => array(
		'label'       => __( 'Navigation Menus', 'extra-elementor-mcp' ),
		'description' => __( 'list-menus, get-menu, update-menu, assign-menu-location', 'extra-elementor-mcp' ),
		'required'    => false,
	),
	'site'        => array(
		'label'       => __( 'Site Settings', 'extra-elementor-mcp' ),
		'description' => __( 'get-site-info, update-site-settings, get-reading-settings', 'extra-elementor-mcp' ),
		'required'    => false,
	),
	'media'       => array(
		'label'       => __( 'Media Library', 'extra-elementor-mcp' ),
		'description' => __( 'list-media, upload-media, update-media-meta', 'extra-elementor-mcp' ),
		'required'    => false,
	),
	'taxonomies'  => array(
		'label'       => __( 'Taxonomies', 'extra-elementor-mcp' ),
		'description' => __( 'list-categories, create-category, list-tags, create-tag', 'extra-elementor-mcp' ),
		'required'    => false,
	),
	'revisions'   => array(
		'label'       => __( 'Revisions', 'extra-elementor-mcp' ),
		'description' => __( 'list-revisions, restore-revision', 'extra-elementor-mcp' ),
		'required'    => false,
	),
	'seo'         => array(
		'label'       => __( 'Yoast SEO', 'extra-elementor-mcp' ),
		/* translators: %s: Yoast SEO plugin version */
		'description' => defined( 'WPSEO_VERSION' )
			? sprintf( __( 'get-seo, update-seo, get-seo-analysis — Yoast SEO v%s detected', 'extra-elementor-mcp' ), WPSEO_VERSION )
			: __( 'get-seo, update-seo, get-seo-analysis — requires Yoast SEO plugin', 'extra-elementor-mcp' ),
		'required'    => false,
		'unavailable' => ! defined( 'WPSEO_VERSION' ),
	),
	'acf'         => array(
		'label'       => __( 'ACF Custom Fields', 'extra-elementor-mcp' ),
		'description' => class_exists( 'ACF' )
			? __( 'list-acf-field-groups, get-acf-fields, update-acf-fields — ACF detected', 'extra-elementor-mcp' )
			: __( 'list-acf-field-groups, get-acf-fields, update-acf-fields — requires Advanced Custom Fields plugin', 'extra-elementor-mcp' ),
		'required'    => false,
		'unavailable' => ! class_exists( 'ACF' ),
	),
);
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors( Extra_Elementor_MCP_Admin::OPTION_NAME ); ?>

	<!-- ==================== Connection Info ==================== -->
	<h2><?php esc_html_e( 'Connection Info', 'extra-elementor-mcp' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'MCP Server Endpoint', 'extra-elementor-mcp' ); ?></th>
			<td>
				<code><?php echo esc_url( $mcp_endpoint ); ?></code>
				<p class="description">
					<?php esc_html_e( 'Add this URL to your .mcp.json file to connect AI tools to this server.', 'extra-elementor-mcp' ); ?>
				</p>
			</td>
		</tr>
	</table>

	<!-- ==================== Dependency Status ==================== -->
	<h2><?php esc_html_e( 'Dependency Status', 'extra-elementor-mcp' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'WordPress MCP Adapter', 'extra-elementor-mcp' ); ?></th>
			<td>
				<?php if ( class_exists( '\WP\MCP\Core\McpAdapter' ) ) : ?>
					<span style="color:#46b450;">&#10003; <?php esc_html_e( 'Active', 'extra-elementor-mcp' ); ?></span>
				<?php else : ?>
					<span style="color:#dc3232;">&#10007; <?php esc_html_e( 'Not detected (required)', 'extra-elementor-mcp' ); ?></span>
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

	<!-- ==================== Tool Group Toggles ==================== -->
	<h2><?php esc_html_e( 'Tool Group Toggles', 'extra-elementor-mcp' ); ?></h2>
	<p><?php esc_html_e( 'Enable or disable individual tool groups. Disabled groups are not registered with the MCP server.', 'extra-elementor-mcp' ); ?></p>

	<form method="post" action="options.php">
		<?php settings_fields( Extra_Elementor_MCP_Admin::SETTINGS_GROUP ); ?>

		<table class="form-table" role="presentation">
			<?php foreach ( $group_labels as $group_key => $group ) : ?>
				<?php
				$is_checked     = ! empty( $settings['enabled_groups'][ $group_key ] );
				$is_unavailable = ! empty( $group['unavailable'] );
				$field_name     = esc_attr( Extra_Elementor_MCP_Admin::OPTION_NAME . '[enabled_groups][' . $group_key . ']' );
				$field_id       = esc_attr( 'extra-elementor-mcp-group-' . $group_key );
				?>
				<tr>
					<th scope="row">
						<label for="<?php echo $field_id; // Already escaped above. ?>">
							<?php echo esc_html( $group['label'] ); ?>
						</label>
					</th>
					<td>
						<fieldset>
							<label for="<?php echo $field_id; // Already escaped above. ?>">
								<input
									type="checkbox"
									id="<?php echo $field_id; // Already escaped above. ?>"
									name="<?php echo $field_name; // Already escaped above. ?>"
									value="1"
									<?php checked( $is_checked ); ?>
									<?php if ( $is_unavailable ) : ?>
										aria-describedby="<?php echo esc_attr( $group_key . '-unavailable' ); ?>"
									<?php endif; ?>
								/>
								<?php echo esc_html( $group['description'] ); ?>
							</label>
							<?php if ( $is_unavailable ) : ?>
								<p id="<?php echo esc_attr( $group_key . '-unavailable' ); ?>" class="description" style="color:#dc3232;">
									<?php esc_html_e( 'Required plugin is not active. This group will not register even if enabled.', 'extra-elementor-mcp' ); ?>
								</p>
							<?php endif; ?>
						</fieldset>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<?php submit_button( __( 'Save Settings', 'extra-elementor-mcp' ) ); ?>
	</form>
</div>
