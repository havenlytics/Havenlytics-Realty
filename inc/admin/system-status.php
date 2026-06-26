<?php
/**
 * Realty admin — read-only system diagnostics.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** System Status admin page slug. */
define( 'HVN_REALTY_SYSTEM_STATUS_SLUG', 'hvn-realty-system-status' );

/**
 * URL for the System Status admin page.
 *
 * @return string
 */
function hvn_realty_get_system_status_url() {
	return admin_url( 'admin.php?page=' . HVN_REALTY_SYSTEM_STATUS_SLUG );
}

/**
 * Havenlytics plugin version string (read-only).
 *
 * @return string Empty when plugin inactive or version unknown.
 */
function hvn_realty_get_system_plugin_version() {
	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		return '';
	}

	if ( defined( 'HVNLY_VERSION' ) ) {
		return (string) HVNLY_VERSION;
	}

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$active_plugins = (array) get_option( 'active_plugins', array() );

	foreach ( $active_plugins as $plugin_file ) {
		if ( ! is_string( $plugin_file ) || false === strpos( $plugin_file, 'havenlytics' ) ) {
			continue;
		}

		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;

		if ( ! file_exists( $plugin_path ) ) {
			continue;
		}

		$data = get_plugin_data( $plugin_path, false, false );

		if ( ! empty( $data['Version'] ) ) {
			return (string) $data['Version'];
		}
	}

	return '';
}

/**
 * Property import status for display.
 *
 * @return array{label: string, status: string, badge: string}
 */
function hvn_realty_get_system_demo_status() {
	if ( get_option( 'hvnly_demo_properties_imported', false ) ) {
		return array(
			'label'  => __( 'Imported', 'havenlytics-realty' ),
			'status' => 'success',
			'badge'  => __( 'Complete', 'havenlytics-realty' ),
		);
	}

	return array(
		'label'  => __( 'Not imported', 'havenlytics-realty' ),
		'status' => 'warning',
		'badge'  => '',
	);
}

/**
 * Home page diagnostic row.
 *
 * @return array{label: string, value: string, status: string, detail: string}
 */
function hvn_realty_get_system_home_page_status() {
	$home_id = defined( 'HVN_REALTY_HOME_PAGE_OPTION' )
		? (int) get_option( HVN_REALTY_HOME_PAGE_OPTION, 0 )
		: 0;

	if ( $home_id <= 0 ) {
		return array(
			'label'  => __( 'Home page', 'havenlytics-realty' ),
			'value'  => __( 'Not set', 'havenlytics-realty' ),
			'status' => 'warning',
			'detail' => '',
		);
	}

	$post = get_post( $home_id );

	if ( ! $post || 'page' !== $post->post_type ) {
		return array(
			'label'  => __( 'Home page', 'havenlytics-realty' ),
			'value'  => sprintf(
				/* translators: %d: page ID */
				__( 'Missing (ID %d)', 'havenlytics-realty' ),
				$home_id
			),
			'status' => 'error',
			'detail' => '',
		);
	}

	return array(
		'label'  => __( 'Home page', 'havenlytics-realty' ),
		'value'  => get_the_title( $post ),
		'status' => 'success',
		'detail' => sprintf( 'ID %d', $home_id ),
	);
}

/**
 * Front page reading settings diagnostic row.
 *
 * @return array{label: string, value: string, status: string, detail: string}
 */
function hvn_realty_get_system_front_page_status() {
	$show_on_front = (string) get_option( 'show_on_front', 'posts' );
	$page_on_front = (int) get_option( 'page_on_front', 0 );

	if ( 'page' !== $show_on_front ) {
		return array(
			'label'  => __( 'Front page', 'havenlytics-realty' ),
			'value'  => __( 'Blog posts', 'havenlytics-realty' ),
			'status' => 'warning',
			'detail' => '',
		);
	}

	if ( $page_on_front <= 0 || ! get_post( $page_on_front ) ) {
		return array(
			'label'  => __( 'Front page', 'havenlytics-realty' ),
			'value'  => __( 'Static page (missing)', 'havenlytics-realty' ),
			'status' => 'error',
			'detail' => '',
		);
	}

	return array(
		'label'  => __( 'Front page', 'havenlytics-realty' ),
		'value'  => __( 'Static page', 'havenlytics-realty' ),
		'status' => 'success',
		'detail' => get_the_title( $page_on_front ),
	);
}

/**
 * Primary menu diagnostic row.
 *
 * @return array{label: string, value: string, status: string, detail: string}
 */
function hvn_realty_get_system_primary_menu_status() {
	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['primary'] ) ? (int) $locations['primary'] : 0;

	if ( $menu_id <= 0 ) {
		return array(
			'label'  => __( 'Primary menu', 'havenlytics-realty' ),
			'value'  => __( 'Not assigned', 'havenlytics-realty' ),
			'status' => 'warning',
			'detail' => '',
		);
	}

	$menu = wp_get_nav_menu_object( $menu_id );

	if ( ! $menu || is_wp_error( $menu ) ) {
		return array(
			'label'  => __( 'Primary menu', 'havenlytics-realty' ),
			'value'  => __( 'Missing menu', 'havenlytics-realty' ),
			'status' => 'error',
			'detail' => sprintf( 'ID %d', $menu_id ),
		);
	}

	return array(
		'label'  => __( 'Primary menu', 'havenlytics-realty' ),
		'value'  => $menu->name,
		'status' => 'success',
		'detail' => sprintf( 'ID %d', $menu_id ),
	);
}

/**
 * All System Status diagnostic checks (read-only).
 *
 * @return array<int, array<string, string>>
 */
function hvn_realty_get_system_status_checks() {
	$plugin_active   = function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) && hvn_realty_is_havenlytics_plugin_active();
	$plugin_version  = hvn_realty_get_system_plugin_version();
	$demo            = hvn_realty_get_system_demo_status();
	$home            = hvn_realty_get_system_home_page_status();
	$front           = hvn_realty_get_system_front_page_status();
	$menu            = hvn_realty_get_system_primary_menu_status();
	$launch_complete = defined( 'HVN_REALTY_LAUNCH_COMPLETE_OPTION' )
		? (bool) get_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, false )
		: false;
	$php_version     = PHP_VERSION;
	$php_ok          = version_compare( $php_version, '7.4', '>=' );
	$integrity       = class_exists( 'HVN_Realty_Theme_Integrity', false )
		? HVN_Realty_Theme_Integrity::get_status_check_row()
		: array(
			'key'    => 'theme_integrity',
			'label'  => __( 'Theme integrity', 'havenlytics-realty' ),
			'value'  => __( 'Scanner unavailable', 'havenlytics-realty' ),
			'status' => 'warning',
			'detail' => '',
		);

	$checks = array(
		array(
			'key'    => 'theme_version',
			'label'  => __( 'Theme version', 'havenlytics-realty' ),
			'value'  => defined( 'HVN_REALTY_VERSION' ) ? HVN_REALTY_VERSION : '',
			'status' => 'neutral',
			'detail' => '',
		),
		array(
			'key'    => 'plugin_version',
			'label'  => __( 'Plugin version', 'havenlytics-realty' ),
			'value'  => $plugin_version ? $plugin_version : '—',
			'status' => $plugin_active && $plugin_version ? 'success' : 'warning',
			'detail' => '',
		),
		array(
			'key'    => 'wordpress_version',
			'label'  => __( 'WordPress version', 'havenlytics-realty' ),
			'value'  => get_bloginfo( 'version' ),
			'status' => 'neutral',
			'detail' => '',
		),
		array(
			'key'    => 'php_version',
			'label'  => __( 'PHP version', 'havenlytics-realty' ),
			'value'  => $php_version,
			'status' => $php_ok ? 'success' : 'warning',
			'detail' => $php_ok ? '' : __( 'PHP 7.4+ recommended', 'havenlytics-realty' ),
		),
		array(
			'key'    => 'plugin_active',
			'label'  => __( 'Plugin active', 'havenlytics-realty' ),
			'value'  => $plugin_active ? __( 'Yes', 'havenlytics-realty' ) : __( 'No', 'havenlytics-realty' ),
			'status' => $plugin_active ? 'success' : 'warning',
			'detail' => '',
		),
		array(
			'key'    => 'launch_complete',
			'label'  => __( 'Launch complete', 'havenlytics-realty' ),
			'value'  => $launch_complete ? __( 'Yes', 'havenlytics-realty' ) : __( 'No', 'havenlytics-realty' ),
			'status' => $launch_complete ? 'success' : 'warning',
			'detail' => '',
		),
		array(
			'key'    => 'demo_status',
			'label'  => __( 'Property import', 'havenlytics-realty' ),
			'value'  => $demo['label'],
			'status' => $demo['status'],
			'detail' => $demo['badge'],
		),
		array(
			'key'    => 'home_page',
			'label'  => $home['label'],
			'value'  => $home['value'],
			'status' => $home['status'],
			'detail' => $home['detail'],
		),
		array(
			'key'    => 'front_page',
			'label'  => $front['label'],
			'value'  => $front['value'],
			'status' => $front['status'],
			'detail' => $front['detail'],
		),
		array(
			'key'    => 'primary_menu',
			'label'  => $menu['label'],
			'value'  => $menu['value'],
			'status' => $menu['status'],
			'detail' => $menu['detail'],
		),
		$integrity,
	);

	return apply_filters( 'hvn_realty_system_status_checks', $checks );
}

/**
 * Overall health summary from diagnostic checks.
 *
 * @return array{state: string, label: string, message: string, warnings: int, errors: int}
 */
function hvn_realty_get_system_status_health() {
	$warnings = 0;
	$errors   = 0;

	foreach ( hvn_realty_get_system_status_checks() as $check ) {
		if ( 'warning' === ( $check['status'] ?? '' ) ) {
			++$warnings;
		}
		if ( 'error' === ( $check['status'] ?? '' ) ) {
			++$errors;
		}
	}

	if ( $errors > 0 ) {
		return array(
			'state'    => 'error',
			'label'    => __( 'Issues detected', 'havenlytics-realty' ),
			'message'  => sprintf(
				/* translators: %d: number of issues */
				_n( '%d item needs attention.', '%d items need attention.', $errors + $warnings, 'havenlytics-realty' ),
				$errors + $warnings
			),
			'warnings' => $warnings,
			'errors'   => $errors,
		);
	}

	if ( $warnings > 0 ) {
		return array(
			'state'    => 'warning',
			'label'    => __( 'Mostly healthy', 'havenlytics-realty' ),
			'message'  => sprintf(
				/* translators: %d: number of warnings */
				_n( '%d optional item could be improved.', '%d optional items could be improved.', $warnings, 'havenlytics-realty' ),
				$warnings
			),
			'warnings' => $warnings,
			'errors'   => 0,
		);
	}

	return array(
		'state'    => 'success',
		'label'    => __( 'All checks passed', 'havenlytics-realty' ),
		'message'  => __( 'Theme, plugin, and site setup diagnostics look good.', 'havenlytics-realty' ),
		'warnings' => 0,
		'errors'   => 0,
	);
}

/**
 * Render a status indicator badge.
 *
 * @param string $status success|warning|error|neutral.
 * @return void
 */
function hvn_realty_render_system_status_indicator( $status ) {
	$status = sanitize_key( $status );
	$allowed = array( 'success', 'warning', 'error', 'neutral' );

	if ( ! in_array( $status, $allowed, true ) ) {
		$status = 'neutral';
	}

	printf(
		'<span class="hvn-realty-admin__status hvn-realty-admin__status--%1$s" aria-hidden="true"></span>',
		esc_attr( $status )
	);
}

/**
 * Render a status pill badge.
 *
 * @param string $status success|warning|error|neutral.
 * @param string $text   Badge label.
 * @return void
 */
function hvn_realty_render_system_status_badge( $status, $text ) {
	$status = sanitize_key( $status );
	$allowed = array( 'success', 'warning', 'error', 'neutral' );

	if ( ! in_array( $status, $allowed, true ) ) {
		$status = 'neutral';
	}

	printf(
		'<span class="hvn-realty-admin__badge hvn-realty-admin__badge--%1$s">%2$s</span>',
		esc_attr( $status ),
		esc_html( $text )
	);
}

/**
 * Render a single diagnostic row.
 *
 * @param array<string, string> $check Check row.
 * @return void
 */
function hvn_realty_render_system_status_row( $check ) {
	?>
	<div class="hvn-realty-admin__status-row">
		<div class="hvn-realty-admin__status-key">
			<?php hvn_realty_render_system_status_indicator( $check['status'] ?? 'neutral' ); ?>
			<span><?php echo esc_html( $check['label'] ?? '' ); ?></span>
		</div>
		<div class="hvn-realty-admin__status-value">
			<strong><?php echo esc_html( $check['value'] ?? '' ); ?></strong>
			<?php if ( ! empty( $check['detail'] ) ) : ?>
				<span class="hvn-realty-admin__status-detail"><?php echo esc_html( $check['detail'] ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

/**
 * Render the Theme Integrity detail panel.
 *
 * @return void
 */
function hvn_realty_render_system_integrity_panel() {
	if ( ! class_exists( 'HVN_Realty_Theme_Integrity', false ) ) {
		return;
	}

	$report = HVN_Realty_Theme_Integrity::get_report();
	?>
	<section class="hvn-realty-admin__status-panel hvn-realty-admin__status-panel--integrity" aria-label="<?php esc_attr_e( 'Theme integrity', 'havenlytics-realty' ); ?>">
		<h2><?php esc_html_e( 'Theme integrity', 'havenlytics-realty' ); ?></h2>
		<?php if ( 'success' === $report['status'] ) : ?>
			<p class="hvn-realty-admin__integrity-summary hvn-realty-admin__integrity-summary--success">
				<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
				<?php esc_html_e( 'All required theme files and classes are present.', 'havenlytics-realty' ); ?>
			</p>
		<?php elseif ( ! empty( $report['missing_critical'] ) || ! empty( $report['missing_classes'] ) ) : ?>
			<p class="hvn-realty-admin__integrity-summary hvn-realty-admin__integrity-summary--error">
				<span class="dashicons dashicons-warning" aria-hidden="true"></span>
				<?php esc_html_e( 'Some theme files or classes are missing. Reinstall the complete theme package from WordPress.org or your release ZIP.', 'havenlytics-realty' ); ?>
			</p>
		<?php elseif ( ! empty( $report['missing_optional_customizer'] ) ) : ?>
			<p class="hvn-realty-admin__integrity-summary hvn-realty-admin__integrity-summary--warning">
				<span class="dashicons dashicons-info" aria-hidden="true"></span>
				<?php esc_html_e( 'Optional Customizer control files are missing from this install. Drag-and-drop section order and testimonials controls are unavailable.', 'havenlytics-realty' ); ?>
			</p>
		<?php else : ?>
			<p class="hvn-realty-admin__integrity-summary hvn-realty-admin__integrity-summary--warning">
				<span class="dashicons dashicons-info" aria-hidden="true"></span>
				<?php esc_html_e( 'Some optional theme files are missing. Core theme features should still work.', 'havenlytics-realty' ); ?>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $report['missing_critical'] ) ) : ?>
			<h3><?php esc_html_e( 'Missing files', 'havenlytics-realty' ); ?></h3>
			<ul class="hvn-realty-admin__integrity-list">
				<?php foreach ( array_keys( $report['missing_critical'] ) as $path ) : ?>
					<li><code><?php echo esc_html( $path ); ?></code></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( ! empty( $report['missing_classes'] ) ) : ?>
			<h3><?php esc_html_e( 'Missing classes', 'havenlytics-realty' ); ?></h3>
			<ul class="hvn-realty-admin__integrity-list">
				<?php foreach ( $report['missing_classes'] as $class => $file ) : ?>
					<li>
						<code><?php echo esc_html( $class ); ?></code>
						<span class="hvn-realty-admin__status-detail"><?php echo esc_html( $file ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( ! empty( $report['missing_optional_customizer'] ) ) : ?>
			<h3><?php esc_html_e( 'Optional Customizer controls', 'havenlytics-realty' ); ?></h3>
			<ul class="hvn-realty-admin__integrity-list hvn-realty-admin__integrity-list--optional">
				<?php foreach ( $report['missing_optional_customizer'] as $class => $file ) : ?>
					<li>
						<code><?php echo esc_html( $class ); ?></code>
						<span class="hvn-realty-admin__status-detail"><?php echo esc_html( $file ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php if ( ! empty( $report['missing_optional'] ) ) : ?>
			<h3><?php esc_html_e( 'Optional files', 'havenlytics-realty' ); ?></h3>
			<ul class="hvn-realty-admin__integrity-list hvn-realty-admin__integrity-list--optional">
				<?php foreach ( array_keys( $report['missing_optional'] ) as $path ) : ?>
					<li><code><?php echo esc_html( $path ); ?></code></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Render the System Status admin page.
 *
 * @return void
 */
function hvn_realty_render_system_status_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$health = hvn_realty_get_system_status_health();
	$checks = hvn_realty_get_system_status_checks();

	$environment_keys = array( 'theme_version', 'plugin_version', 'wordpress_version', 'php_version', 'plugin_active', 'theme_integrity' );
	$setup_keys       = array( 'launch_complete', 'demo_status', 'home_page', 'front_page', 'primary_menu' );
	?>
	<div class="wrap hvn-realty-admin hvn-realty-admin--system-status">
		<header class="hvn-realty-admin__hero">
			<div class="hvn-realty-admin__hero-copy">
				<p class="hvn-realty-admin__eyebrow"><?php esc_html_e( 'Havenlytics Realty', 'havenlytics-realty' ); ?></p>
				<h1 class="hvn-realty-admin__title"><?php esc_html_e( 'System Status', 'havenlytics-realty' ); ?></h1>
				<p class="hvn-realty-admin__lead">
					<?php esc_html_e( 'Read-only environment and setup diagnostics. No settings are changed on this page.', 'havenlytics-realty' ); ?>
				</p>
			</div>
		</header>

		<div class="hvn-realty-admin__health-banner hvn-realty-admin__health-banner--<?php echo esc_attr( $health['state'] ); ?>">
			<span class="dashicons dashicons-<?php echo 'success' === $health['state'] ? 'yes-alt' : ( 'error' === $health['state'] ? 'warning' : 'info' ); ?>" aria-hidden="true"></span>
			<div>
				<strong><?php echo esc_html( $health['label'] ); ?></strong>
				<p><?php echo esc_html( $health['message'] ); ?></p>
			</div>
		</div>

		<div class="hvn-realty-admin__status-grid">
			<section class="hvn-realty-admin__status-panel" aria-label="<?php esc_attr_e( 'Environment', 'havenlytics-realty' ); ?>">
				<h2><?php esc_html_e( 'Environment', 'havenlytics-realty' ); ?></h2>
				<?php
				foreach ( $checks as $check ) {
					if ( in_array( $check['key'] ?? '', $environment_keys, true ) ) {
						hvn_realty_render_system_status_row( $check );
					}
				}
				?>
			</section>

			<section class="hvn-realty-admin__status-panel" aria-label="<?php esc_attr_e( 'Site setup', 'havenlytics-realty' ); ?>">
				<h2><?php esc_html_e( 'Site setup', 'havenlytics-realty' ); ?></h2>
				<?php
				foreach ( $checks as $check ) {
					if ( in_array( $check['key'] ?? '', $setup_keys, true ) ) {
						hvn_realty_render_system_status_row( $check );
					}
				}
				?>
			</section>
		</div>

		<?php hvn_realty_render_system_integrity_panel(); ?>

		<footer class="hvn-realty-admin__footer hvn-realty-admin__footer--actions">
			<p>
				<a class="button button-secondary" href="<?php echo esc_url( hvn_realty_get_realty_admin_url() ); ?>">
					<?php esc_html_e( 'Back to Dashboard', 'havenlytics-realty' ); ?>
				</a>
			</p>
		</footer>
	</div>
	<?php
}
