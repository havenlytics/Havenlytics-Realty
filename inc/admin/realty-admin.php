<?php
/**
 * Havenlytics Realty admin hub (top-level Realty menu).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Admin page slug. */
define( 'HVN_REALTY_ADMIN_SLUG', 'hvn-realty' );

/** Admin menu position — directly below Agents (plugin position 3). */
define( 'HVN_REALTY_ADMIN_MENU_POSITION', 4 );

/** @deprecated Use HVN_REALTY_ADMIN_SLUG. */
define( 'HVN_REALTY_GETTING_STARTED_SLUG', 'hvn-realty-getting-started' );

/** Internal Customize submenu placeholder (rewritten to customize.php). */
define( 'HVN_REALTY_ADMIN_CUSTOMIZE_SLUG', 'hvn-realty-open-customize' );

/** Transient key for one-time activation redirect. */
define( 'HVN_REALTY_ACTIVATION_REDIRECT_TRANSIENT', 'hvn_realty_activation_redirect' );

/**
 * Flag redirect to Realty admin after theme activation.
 *
 * @return void
 */
function hvn_realty_set_activation_redirect() {
	if ( 'havenlytics-realty' !== get_template() ) {
		return;
	}

	set_transient( HVN_REALTY_ACTIVATION_REDIRECT_TRANSIENT, 1, 30 );
}
add_action( 'after_switch_theme', 'hvn_realty_set_activation_redirect', 5 );

/**
 * Redirect to Realty admin once after theme activation.
 *
 * @return void
 */
function hvn_realty_activation_redirect() {
	if ( ! is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	if ( ! get_transient( HVN_REALTY_ACTIVATION_REDIRECT_TRANSIENT ) ) {
		return;
	}

	delete_transient( HVN_REALTY_ACTIVATION_REDIRECT_TRANSIENT );

	if ( isset( $_GET['activate-multi'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	wp_safe_redirect( hvn_realty_get_realty_admin_url() );
	exit;
}
add_action( 'admin_init', 'hvn_realty_activation_redirect' );

/**
 * URL for the Realty admin page.
 *
 * @return string
 */
function hvn_realty_get_realty_admin_url() {
	return admin_url( 'admin.php?page=' . HVN_REALTY_ADMIN_SLUG );
}

/**
 * Backward-compatible alias.
 *
 * @return string
 */
function hvn_realty_get_getting_started_url() {
	return hvn_realty_get_realty_admin_url();
}

/**
 * Whether Havenlytics plugin, demo import, and homepage setup are complete.
 *
 * @return bool
 */
function hvn_realty_is_site_setup_complete() {
	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return false;
	}

	if ( ! get_option( 'hvnly_demo_properties_imported', false ) ) {
		return false;
	}

	$launch_complete = (bool) get_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, false );
	$homepage_ready  = $launch_complete || ( function_exists( 'hvn_realty_is_realty_homepage' ) && hvn_realty_is_realty_homepage() && 'page' === get_option( 'show_on_front', 'posts' ) );

	return $homepage_ready;
}

/**
 * Setup checklist steps.
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_setup_checklist() {
	$plugin_active   = hvn_realty_is_havenlytics_plugin_active();
	$demo_imported   = (bool) get_option( 'hvnly_demo_properties_imported', false );
	$launch_complete = (bool) get_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, false );
	$homepage_ready  = $launch_complete || ( function_exists( 'hvn_realty_is_realty_homepage' ) && hvn_realty_is_realty_homepage() && 'page' === get_option( 'show_on_front', 'posts' ) );

	$import_url = $plugin_active
		? admin_url( 'edit.php?post_type=hvnly_property&page=hvnly-property-import' )
		: hvn_realty_get_plugin_install_url();

	$home_id   = (int) get_option( HVN_REALTY_HOME_PAGE_OPTION, 0 );
	$home_edit = $home_id > 0 ? get_edit_post_link( $home_id, 'raw' ) : '';

	$steps = array(
		array(
			'id'             => 'plugin',
			'icon'           => 'dashicons-admin-plugins',
			'title'          => __( 'Install Havenlytics', 'havenlytics-realty' ),
			'description'    => __( 'Activate the free plugin to unlock listings, search, agents, and agencies.', 'havenlytics-realty' ),
			'complete'       => $plugin_active,
			'action_label'   => $plugin_active ? __( 'Completed', 'havenlytics-realty' ) : __( 'Install plugin', 'havenlytics-realty' ),
			'action_url'     => hvn_realty_get_plugin_install_url(),
			'action_primary' => ! $plugin_active,
			'disabled'       => $plugin_active,
		),
		array(
			'id'             => 'import',
			'icon'           => 'dashicons-database-import',
			'title'          => __( 'Import demo properties', 'havenlytics-realty' ),
			'description'    => __( 'Run the Setup Wizard to import sample listings, agents, and plugin pages.', 'havenlytics-realty' ),
			'complete'       => $demo_imported,
			'action_label'   => $demo_imported ? __( 'Completed', 'havenlytics-realty' ) : __( 'Run Setup Wizard', 'havenlytics-realty' ),
			'action_url'     => $import_url,
			'action_primary' => $plugin_active && ! $demo_imported,
			'disabled'       => ! $plugin_active || $demo_imported,
		),
		array(
			'id'             => 'homepage',
			'icon'           => 'dashicons-admin-home',
			'title'          => __( 'Configure homepage', 'havenlytics-realty' ),
			'description'    => __( 'Creates your Home page, primary menu, and static front page after demo import.', 'havenlytics-realty' ),
			'complete'       => $homepage_ready,
			'action_label'   => $homepage_ready
				? __( 'Completed', 'havenlytics-realty' )
				: ( $demo_imported ? __( 'Run theme setup', 'havenlytics-realty' ) : __( 'Waiting for import', 'havenlytics-realty' ) ),
			'action_url'     => $homepage_ready && $home_edit ? $home_edit : hvn_realty_get_realty_admin_url(),
			'action_primary' => $demo_imported && ! $homepage_ready,
			'disabled'       => ! $demo_imported || $homepage_ready,
			'is_setup'       => $demo_imported && ! $homepage_ready,
		),
		array(
			'id'             => 'launch',
			'icon'           => 'dashicons-visibility',
			'title'          => __( 'View your website', 'havenlytics-realty' ),
			'description'    => __( 'Preview your live real estate homepage with search, listings, and navigation.', 'havenlytics-realty' ),
			'complete'       => $homepage_ready && $plugin_active,
			'action_label'   => __( 'View website', 'havenlytics-realty' ),
			'action_url'     => home_url( '/' ),
			'action_primary' => $homepage_ready && $plugin_active,
			'external'       => true,
			'disabled'       => ! $homepage_ready,
		),
	);

	return apply_filters( 'hvn_realty_setup_checklist', $steps );
}

/**
 * Setup progress stats.
 *
 * @return array{complete: int, total: int, percent: int}
 */
function hvn_realty_get_setup_progress() {
	$steps    = hvn_realty_get_setup_checklist();
	$total    = count( $steps );
	$complete = 0;

	foreach ( $steps as $step ) {
		if ( ! empty( $step['complete'] ) ) {
			++$complete;
		}
	}

	return array(
		'complete' => $complete,
		'total'    => $total,
		'percent'  => $total > 0 ? (int) round( ( $complete / $total ) * 100 ) : 0,
	);
}

/**
 * Redirect legacy Getting Started slug.
 *
 * @return void
 */
function hvn_realty_admin_legacy_redirect() {
	if ( ! is_admin() || ! isset( $_GET['page'] ) ) {
		return;
	}

	$page = sanitize_key( wp_unslash( $_GET['page'] ) );
	if ( 'hvn-realty-getting-started' !== $page ) {
		return;
	}

	wp_safe_redirect( hvn_realty_get_realty_admin_url() );
	exit;
}
add_action( 'admin_init', 'hvn_realty_admin_legacy_redirect', 1 );

/**
 * Redirect legacy Appearance → Realty URL to the top-level menu page.
 *
 * @return void
 */
function hvn_realty_admin_appearance_legacy_redirect() {
	global $pagenow;

	if ( 'themes.php' !== $pagenow || ! isset( $_GET['page'] ) ) {
		return;
	}

	$page = sanitize_key( wp_unslash( $_GET['page'] ) );
	if ( HVN_REALTY_ADMIN_SLUG !== $page ) {
		return;
	}

	wp_safe_redirect( hvn_realty_get_realty_admin_url() );
	exit;
}
add_action( 'admin_init', 'hvn_realty_admin_appearance_legacy_redirect', 2 );

/**
 * Theme-owned Realty admin menu icon URL for add_menu_page().
 *
 * @return string Image URL or dashicon slug fallback.
 */
function hvn_realty_get_admin_menu_icon() {
	$candidates = array(
		'assets/admin/img/realty-icon.svg',
		'assets/admin/img/realty-icon.png',
	);

	foreach ( $candidates as $relative_path ) {
		$file = get_template_directory() . '/' . $relative_path;
		if ( file_exists( $file ) ) {
			return HVN_REALTY_TEMPLATE_URL . '/' . ltrim( $relative_path, '/' );
		}
	}

	return 'dashicons-admin-home';
}

/**
 * Register top-level Realty menu (below Agents).
 *
 * @return void
 */
function hvn_realty_register_realty_admin_page() {
	add_menu_page(
		__( 'Havenlytics Realty', 'havenlytics-realty' ),
		__( 'Realty', 'havenlytics-realty' ),
		'manage_options',
		HVN_REALTY_ADMIN_SLUG,
		'hvn_realty_render_realty_admin_page',
		hvn_realty_get_admin_menu_icon(),
		HVN_REALTY_ADMIN_MENU_POSITION
	);

	add_submenu_page(
		HVN_REALTY_ADMIN_SLUG,
		__( 'Dashboard', 'havenlytics-realty' ),
		__( 'Dashboard', 'havenlytics-realty' ),
		'manage_options',
		HVN_REALTY_ADMIN_SLUG,
		'hvn_realty_render_realty_admin_page'
	);

	add_submenu_page(
		HVN_REALTY_ADMIN_SLUG,
		__( 'Customize', 'havenlytics-realty' ),
		__( 'Customize', 'havenlytics-realty' ),
		'customize',
		HVN_REALTY_ADMIN_CUSTOMIZE_SLUG,
		'__return_null'
	);

	if ( defined( 'HVN_REALTY_SYSTEM_STATUS_SLUG' ) ) {
		add_submenu_page(
			HVN_REALTY_ADMIN_SLUG,
			__( 'System Status', 'havenlytics-realty' ),
			__( 'System Status', 'havenlytics-realty' ),
			'manage_options',
			HVN_REALTY_SYSTEM_STATUS_SLUG,
			'hvn_realty_render_system_status_page'
		);
	}
}
add_action( 'admin_menu', 'hvn_realty_register_realty_admin_page', 10 );

/**
 * Keep Dashboard, Customize, and System Status submenus.
 *
 * @return void
 */
function hvn_realty_prune_realty_admin_submenus() {
	global $submenu;

	if ( empty( $submenu[ HVN_REALTY_ADMIN_SLUG ] ) || ! is_array( $submenu[ HVN_REALTY_ADMIN_SLUG ] ) ) {
		return;
	}

	$dashboard     = null;
	$customize     = null;
	$system_status = null;

	foreach ( $submenu[ HVN_REALTY_ADMIN_SLUG ] as $item ) {
		if ( ! is_array( $item ) || ! isset( $item[2] ) ) {
			continue;
		}

		if ( HVN_REALTY_ADMIN_SLUG === $item[2] ) {
			$item[0] = __( 'Dashboard', 'havenlytics-realty' );
			$dashboard = $item;
			continue;
		}

		if ( HVN_REALTY_ADMIN_CUSTOMIZE_SLUG === $item[2] ) {
			$item[2] = 'customize.php';
			$customize = $item;
			continue;
		}

		if ( defined( 'HVN_REALTY_SYSTEM_STATUS_SLUG' ) && HVN_REALTY_SYSTEM_STATUS_SLUG === $item[2] ) {
			$system_status = $item;
		}
	}

	$pruned = array();

	if ( $dashboard ) {
		$pruned[] = $dashboard;
	}

	if ( $customize ) {
		$pruned[] = $customize;
	}

	if ( $system_status ) {
		$pruned[] = $system_status;
	}

	if ( ! empty( $pruned ) ) {
		$submenu[ HVN_REALTY_ADMIN_SLUG ] = $pruned;
	}
}
add_action( 'admin_menu', 'hvn_realty_prune_realty_admin_submenus', 999 );

/**
 * Realty admin menu icon styles (global — every admin screen).
 *
 * @return void
 */
function hvn_realty_admin_menu_icon_assets() {
	if ( 0 === strpos( (string) hvn_realty_get_admin_menu_icon(), 'dashicons-' ) ) {
		return;
	}

	if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {
		hvn_realty_enqueue_theme_style( 'hvn-realty-admin-menu-icon', 'assets/css/admin-realty.css' );
		return;
	}

	if ( file_exists( get_template_directory() . '/assets/css/admin-realty.css' ) ) {
		wp_enqueue_style(
			'hvn-realty-admin-menu-icon',
			HVN_REALTY_TEMPLATE_URL . '/assets/css/admin-realty.css',
			array(),
			HVN_REALTY_VERSION
		);
	}
}
add_action( 'admin_enqueue_scripts', 'hvn_realty_admin_menu_icon_assets' );

/**
 * Theme action link on Themes screen.
 *
 * @param array<string, string> $actions Links.
 * @return array<string, string>
 */
function hvn_realty_theme_action_links( $actions ) {
	$actions['hvn-realty-admin'] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( hvn_realty_get_realty_admin_url() ),
		esc_html__( 'Realty', 'havenlytics-realty' )
	);

	return $actions;
}
add_filter( 'theme_action_links_' . get_template(), 'hvn_realty_theme_action_links' );

/**
 * Enqueue Realty admin assets.
 *
 * @param string $hook_suffix Hook.
 * @return void
 */
function hvn_realty_admin_assets( $hook_suffix ) {
	$allowed_hooks = array( 'toplevel_page_' . HVN_REALTY_ADMIN_SLUG );

	if ( defined( 'HVN_REALTY_SYSTEM_STATUS_SLUG' ) ) {
		$allowed_hooks[] = 'realty_page_' . HVN_REALTY_SYSTEM_STATUS_SLUG;
	}

	if ( ! in_array( $hook_suffix, $allowed_hooks, true ) ) {
		return;
	}

	if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {
		wp_dequeue_style( 'hvn-realty-admin-menu-icon' );
		hvn_realty_enqueue_theme_style( 'hvn-realty-admin', 'assets/css/admin-realty.css' );
	} elseif ( file_exists( get_template_directory() . '/assets/css/admin-realty.css' ) ) {
		wp_dequeue_style( 'hvn-realty-admin-menu-icon' );
		wp_enqueue_style(
			'hvn-realty-admin',
			HVN_REALTY_TEMPLATE_URL . '/assets/css/admin-realty.css',
			array(),
			HVN_REALTY_VERSION
		);
	}
}
add_action( 'admin_enqueue_scripts', 'hvn_realty_admin_assets' );

/**
 * Handle manual theme setup action.
 *
 * @return void
 */
function hvn_realty_admin_handle_setup() {
	if ( ! isset( $_GET['hvn-realty-run-setup'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'hvn_realty_run_setup' ) ) {
		return;
	}

	if ( ! hvn_realty_is_havenlytics_plugin_active() || ! get_option( 'hvnly_demo_properties_imported', false ) ) {
		wp_safe_redirect( hvn_realty_get_realty_admin_url() );
		exit;
	}

	if ( ! get_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, false ) ) {
		hvn_realty_run_launch();
	}

	wp_safe_redirect(
		add_query_arg( 'hvn-realty-setup-done', '1', hvn_realty_get_realty_admin_url() )
	);
	exit;
}
add_action( 'admin_init', 'hvn_realty_admin_handle_setup' );

/**
 * Render Realty admin support sidebar panels.
 *
 * @return void
 */
function hvn_realty_render_admin_support_panels() {
	$doc_url     = 'https://havenlytics.com/documentation/';
	$support_url = 'https://havenlytics.com/support/';
	$email       = 'support@havenlytics.com';
	?>
	<div class="hvn-realty-admin__panel">
		<h2><?php esc_html_e( 'Need Help?', 'havenlytics-realty' ); ?></h2>
		<p class="hvn-realty-admin__panel-desc">
			<?php esc_html_e( 'Need help setting up Havenlytics Realty or Havenlytics?', 'havenlytics-realty' ); ?>
		</p>
		<ul class="hvn-realty-admin__links">
			<li>
				<a href="<?php echo esc_url( $doc_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Documentation', 'havenlytics-realty' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( $support_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Support Center', 'havenlytics-realty' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( 'mailto:' . $email ); ?>">
					<?php echo esc_html( $email ); ?>
				</a>
			</li>
		</ul>
	</div>

	<div class="hvn-realty-admin__panel">
		<h2><?php esc_html_e( 'Be Part of Our Ecosystem', 'havenlytics-realty' ); ?></h2>
		<ul class="hvn-realty-admin__links">
			<li>
				<a href="<?php echo esc_url( 'https://facebook.com/groups/havenlytics/' ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Join Havenlytics Facebook Community', 'havenlytics-realty' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( 'https://www.youtube.com/@havenlytics' ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Subscribe to Our YouTube Channel', 'havenlytics-realty' ); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo esc_url( $support_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Need Help? Contact Support', 'havenlytics-realty' ); ?>
				</a>
			</li>
		</ul>
	</div>
	<?php
}

/**
 * Render step action button.
 *
 * @param array<string, mixed> $step Step data.
 * @param string               $setup_url Setup nonce URL.
 * @return void
 */
function hvn_realty_render_step_action( $step, $setup_url ) {
	$is_complete = ! empty( $step['complete'] );
	$is_disabled = ! empty( $step['disabled'] ) || ( $is_complete && empty( $step['external'] ) );

	if ( ! empty( $step['is_setup'] ) && ! $is_complete ) {
		printf(
			'<a href="%1$s" class="button button-primary">%2$s</a>',
			esc_url( $setup_url ),
			esc_html( $step['action_label'] )
		);
		return;
	}

	if ( $is_disabled ) {
		printf(
			'<span class="button button-secondary hvn-realty-admin__btn-done" aria-disabled="true" tabindex="-1">%1$s</span>',
			esc_html( $step['action_label'] )
		);
		return;
	}

	$class = ! empty( $step['action_primary'] ) ? 'button button-primary' : 'button button-secondary';
	printf(
		'<a href="%1$s" class="%2$s"%3$s>%4$s</a>',
		esc_url( $step['action_url'] ),
		esc_attr( $class ),
		! empty( $step['external'] ) ? ' target="_blank" rel="noopener noreferrer"' : '',
		esc_html( $step['action_label'] )
	);
}

/**
 * Render Realty admin page.
 *
 * @return void
 */
function hvn_realty_render_realty_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( hvn_realty_is_site_setup_complete() && function_exists( 'hvn_realty_render_dashboard_overview' ) ) {
		hvn_realty_render_dashboard_overview();
		return;
	}

	$progress   = hvn_realty_get_setup_progress();
	$steps      = hvn_realty_get_setup_checklist();
	$all_done   = hvn_realty_is_site_setup_complete();
	$setup_url  = wp_nonce_url(
		add_query_arg( 'hvn-realty-run-setup', '1', hvn_realty_get_realty_admin_url() ),
		'hvn_realty_run_setup'
	);
	$customize  = admin_url( 'customize.php?autofocus[panel]=hvn_realty_homepage_panel' );
	?>
	<div class="wrap hvn-realty-admin">
		<?php if ( isset( $_GET['hvn-realty-setup-done'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['hvn-realty-setup-done'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Theme setup completed. Your homepage and primary menu are ready.', 'havenlytics-realty' ); ?></p>
			</div>
		<?php endif; ?>

		<header class="hvn-realty-admin__hero">
			<div class="hvn-realty-admin__hero-copy">
				<p class="hvn-realty-admin__eyebrow"><?php esc_html_e( 'Havenlytics Realty', 'havenlytics-realty' ); ?></p>
				<h1 class="hvn-realty-admin__title"><?php esc_html_e( 'Launch your real estate site', 'havenlytics-realty' ); ?></h1>
				<p class="hvn-realty-admin__lead">
					<?php esc_html_e( 'Install Havenlytics, import demo content, and configure your homepage in a few guided steps.', 'havenlytics-realty' ); ?>
				</p>
			</div>
			<div class="hvn-realty-admin__progress-card" role="progressbar" aria-valuenow="<?php echo esc_attr( (string) $progress['percent'] ); ?>" aria-valuemin="0" aria-valuemax="100">
				<div class="hvn-realty-admin__progress-ring" style="--hvn-progress: <?php echo esc_attr( (string) $progress['percent'] ); ?>;">
					<span class="hvn-realty-admin__progress-value"><?php echo esc_html( (string) $progress['percent'] ); ?>%</span>
				</div>
				<p class="hvn-realty-admin__progress-label">
					<?php
					printf(
						/* translators: 1: completed steps, 2: total steps */
						esc_html__( '%1$d of %2$d steps complete', 'havenlytics-realty' ),
						(int) $progress['complete'],
						(int) $progress['total']
					);
					?>
				</p>
			</div>
		</header>

		<?php if ( $all_done ) : ?>
			<div class="hvn-realty-admin__banner hvn-realty-admin__banner--success">
				<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
				<div>
					<strong><?php esc_html_e( 'Your site is ready!', 'havenlytics-realty' ); ?></strong>
					<p><?php esc_html_e( 'Plugin, demo import, and homepage configuration are complete.', 'havenlytics-realty' ); ?></p>
				</div>
				<a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'View website', 'havenlytics-realty' ); ?>
				</a>
			</div>
		<?php endif; ?>

		<div class="hvn-realty-admin__layout">
			<section class="hvn-realty-admin__steps" aria-label="<?php esc_attr_e( 'Setup steps', 'havenlytics-realty' ); ?>">
				<?php foreach ( $steps as $index => $step ) : ?>
					<article class="hvn-realty-admin__step <?php echo ! empty( $step['complete'] ) ? 'is-complete' : 'is-pending'; ?>">
						<div class="hvn-realty-admin__step-head">
							<span class="hvn-realty-admin__step-icon dashicons <?php echo esc_attr( $step['icon'] ); ?>" aria-hidden="true"></span>
							<span class="hvn-realty-admin__step-index"><?php echo esc_html( sprintf( __( 'Step %d', 'havenlytics-realty' ), $index + 1 ) ); ?></span>
							<?php if ( ! empty( $step['complete'] ) ) : ?>
								<span class="hvn-realty-admin__step-badge"><?php esc_html_e( 'Done', 'havenlytics-realty' ); ?></span>
							<?php endif; ?>
						</div>
						<h2 class="hvn-realty-admin__step-title"><?php echo esc_html( $step['title'] ); ?></h2>
						<p class="hvn-realty-admin__step-desc"><?php echo esc_html( $step['description'] ); ?></p>
						<div class="hvn-realty-admin__step-action">
							<?php hvn_realty_render_step_action( $step, $setup_url ); ?>
						</div>
					</article>
				<?php endforeach; ?>
			</section>

			<aside class="hvn-realty-admin__sidebar">
				<div class="hvn-realty-admin__panel">
					<h2><?php esc_html_e( 'Quick actions', 'havenlytics-realty' ); ?></h2>
					<ul class="hvn-realty-admin__links">
						<li><a href="<?php echo esc_url( $customize ); ?>"><?php esc_html_e( 'Homepage settings', 'havenlytics-realty' ); ?></a></li>
						<li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Customize theme', 'havenlytics-realty' ); ?></a></li>
						<?php if ( hvn_realty_is_havenlytics_plugin_active() ) : ?>
							<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hvnly_property' ) ); ?>"><?php esc_html_e( 'Manage properties', 'havenlytics-realty' ); ?></a></li>
							<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hvnly_agent' ) ); ?>"><?php esc_html_e( 'Manage agents', 'havenlytics-realty' ); ?></a></li>
						<?php else : ?>
							<li><a href="<?php echo esc_url( hvn_realty_get_plugin_install_url() ); ?>"><?php esc_html_e( 'Install Havenlytics plugin', 'havenlytics-realty' ); ?></a></li>
						<?php endif; ?>
						<li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Edit menus', 'havenlytics-realty' ); ?></a></li>
					</ul>
				</div>

				<?php if ( hvn_realty_is_havenlytics_plugin_active() ) : ?>
					<div class="hvn-realty-admin__panel">
						<h2><?php esc_html_e( 'Site snapshot', 'havenlytics-realty' ); ?></h2>
						<ul class="hvn-realty-admin__stats">
							<li>
								<span class="hvn-realty-admin__stat-value"><?php echo esc_html( number_format_i18n( hvn_realty_get_property_count() ) ); ?></span>
								<span class="hvn-realty-admin__stat-label"><?php esc_html_e( 'Properties', 'havenlytics-realty' ); ?></span>
							</li>
							<li>
								<span class="hvn-realty-admin__stat-value"><?php echo esc_html( number_format_i18n( hvn_realty_get_agent_count() ) ); ?></span>
								<span class="hvn-realty-admin__stat-label"><?php esc_html_e( 'Agents', 'havenlytics-realty' ); ?></span>
							</li>
							<li>
								<span class="hvn-realty-admin__stat-value"><?php echo esc_html( number_format_i18n( hvn_realty_get_agency_count() ) ); ?></span>
								<span class="hvn-realty-admin__stat-label"><?php esc_html_e( 'Agencies', 'havenlytics-realty' ); ?></span>
							</li>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( function_exists( 'hvn_realty_render_onboarding_tutorial_card' ) ) : ?>
					<?php hvn_realty_render_onboarding_tutorial_card(); ?>
				<?php endif; ?>

				<?php hvn_realty_render_admin_support_panels(); ?>
			</aside>
		</div>

		<footer class="hvn-realty-admin__footer">
			<p>
				<?php
				printf(
					wp_kses(
						__( 'Official companion theme for the free <a href="%1$s">Havenlytics plugin</a>. <a href="%2$s">View on WordPress.org</a>.', 'havenlytics-realty' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url( 'https://wordpress.org/plugins/havenlytics/' ),
					esc_url( 'https://wordpress.org/themes/havenlytics-realty/' )
				);
				?>
			</p>
		</footer>

		<?php if ( function_exists( 'hvn_realty_render_onboarding_modal' ) ) : ?>
			<?php hvn_realty_render_onboarding_modal(); ?>
		<?php endif; ?>
	</div>
	<?php
}
