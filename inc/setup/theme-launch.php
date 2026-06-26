<?php
/**
 * Theme launch pack — creates homepage, menu, and front page assignment.
 *
 * Runs once when Havenlytics demo import completes. Theme-only; no plugin edits.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attempt launch when demo import option updates.
 *
 * @param mixed  $old_value Old option value.
 * @param mixed  $value     New option value.
 * @param string $option    Option name.
 * @return void
 */
function hvn_realty_on_demo_import_updated( $old_value, $value, $option ) {
	if ( 'hvnly_demo_properties_imported' !== $option || empty( $value ) ) {
		return;
	}

	hvn_realty_maybe_run_launch();
}
add_action( 'updated_option', 'hvn_realty_on_demo_import_updated', 10, 3 );

/**
 * Run launch when demo import option is first added.
 *
 * @param string $option Option name.
 * @param mixed  $value  Option value.
 * @return void
 */
function hvn_realty_on_demo_import_added( $option, $value ) {
	if ( 'hvnly_demo_properties_imported' !== $option || empty( $value ) ) {
		return;
	}

	hvn_realty_maybe_run_launch();
}
add_action( 'added_option', 'hvn_realty_on_demo_import_added', 10, 2 );

/**
 * Deferred launch check for sites that imported before theme update.
 *
 * @return void
 */
function hvn_realty_launch_admin_check() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! get_option( 'hvnly_demo_properties_imported', false ) ) {
		return;
	}

	hvn_realty_maybe_run_launch();
	hvn_realty_maybe_seed_footer_widgets();
}
add_action( 'admin_init', 'hvn_realty_launch_admin_check', 20 );

/**
 * Run launch when theme is activated and demo import already completed.
 *
 * @return void
 */
function hvn_realty_launch_on_theme_switch() {
	if ( 'havenlytics-realty' !== get_template() ) {
		return;
	}

	if ( get_option( 'hvnly_demo_properties_imported', false ) ) {
		hvn_realty_maybe_run_launch();
	}
}
add_action( 'after_switch_theme', 'hvn_realty_launch_on_theme_switch', 30 );

/**
 * Run launch pack if conditions are met.
 *
 * @return bool True when launch ran successfully.
 */
function hvn_realty_maybe_run_launch() {
	if ( get_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, false ) ) {
		return false;
	}

	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return false;
	}

	if ( ! hvn_realty_is_home_auto_setup_enabled() ) {
		return false;
	}

	if ( 'havenlytics-realty' !== get_template() ) {
		return false;
	}

	hvn_realty_run_launch();

	return true;
}

/**
 * Execute launch steps.
 *
 * @return void
 */
function hvn_realty_run_launch() {
	$home_id = hvn_realty_launch_create_home_page();

	if ( $home_id > 0 ) {
		hvn_realty_launch_assign_front_page( $home_id );
		hvn_realty_launch_create_menu( $home_id );
	}

	hvn_realty_launch_seed_search_layout_defaults();
	hvn_realty_launch_seed_homepage_section_defaults();
	hvn_realty_maybe_seed_footer_widgets();
	hvn_realty_maybe_seed_property_sidebar_widgets();

	update_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, true );

	/**
	 * Fires after the theme launch pack completes.
	 *
	 * @param int $home_id Home page ID.
	 */
	do_action( 'hvn_realty_launch_complete', $home_id );
}

/**
 * Seed hero search layout defaults for fresh installs only.
 *
 * Runs during the one-time launch pack after demo import. Skips any
 * search-related theme_mod that was already saved (Customizer or prior setup).
 *
 * @return void
 */
function hvn_realty_launch_seed_search_layout_defaults() {
	$mods = get_theme_mods();

	if ( ! is_array( $mods ) ) {
		$mods = array();
	}

	if ( ! isset( $mods['hvn_realty_home_hero_search_display'] ) ) {
		set_theme_mod( 'hvn_realty_home_hero_search_display', 'hero' );
	}

	if ( ! isset( $mods['hvn_realty_show_header_search'] ) ) {
		set_theme_mod( 'hvn_realty_show_header_search', false );
	}
}

/**
 * Enable new homepage sections for fresh installs only.
 *
 * @return void
 */
function hvn_realty_launch_seed_homepage_section_defaults() {
	$mods = get_theme_mods();

	if ( ! is_array( $mods ) ) {
		$mods = array();
	}

	if ( ! isset( $mods['hvn_realty_home_show_property_types'] ) ) {
		set_theme_mod( 'hvn_realty_home_show_property_types', true );
	}

	if ( ! isset( $mods['hvn_realty_home_show_testimonials'] ) ) {
		set_theme_mod( 'hvn_realty_home_show_testimonials', true );
	}

	if ( ! isset( $mods['hvn_realty_home_testimonials'] ) && function_exists( 'hvn_realty_get_default_home_testimonials' ) ) {
		set_theme_mod( 'hvn_realty_home_testimonials', wp_json_encode( hvn_realty_get_default_home_testimonials() ) );
	}
}

/**
 * @return int Author ID.
 */
function hvn_realty_launch_get_author_id() {
	$user_id = get_current_user_id();
	if ( $user_id > 0 ) {
		return $user_id;
	}

	$admins = get_users(
		array(
			'role'   => 'administrator',
			'number' => 1,
			'fields' => 'ID',
		)
	);

	return ! empty( $admins ) ? (int) $admins[0] : 1;
}

/**
 * Create or resolve the Home page with real estate template.
 *
 * @return int Page ID or 0.
 */
function hvn_realty_launch_create_home_page() {
	$existing = (int) get_option( HVN_REALTY_HOME_PAGE_OPTION, 0 );
	if ( $existing > 0 && get_post( $existing ) ) {
		return $existing;
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'title'          => 'Home',
			'fields'         => 'ids',
		)
	);

	if ( $query->have_posts() ) {
		$page_id = (int) $query->posts[0];
		update_post_meta( $page_id, '_wp_page_template', HVN_REALTY_HOME_TEMPLATE );
		update_option( HVN_REALTY_HOME_PAGE_OPTION, $page_id );
		return $page_id;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => __( 'Home', 'havenlytics-realty' ),
			'post_name'    => 'home',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => hvn_realty_launch_get_author_id(),
		),
		true
	);

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return 0;
	}

	update_post_meta( $page_id, '_wp_page_template', HVN_REALTY_HOME_TEMPLATE );
	update_option( HVN_REALTY_HOME_PAGE_OPTION, (int) $page_id );

	return (int) $page_id;
}

/**
 * @param int $home_id Home page ID.
 * @return void
 */
function hvn_realty_launch_assign_front_page( $home_id ) {
	if ( $home_id <= 0 ) {
		return;
	}

	$show_on_front = get_option( 'show_on_front', 'posts' );
	$page_on_front = absint( get_option( 'page_on_front', 0 ) );

	if ( 'posts' !== $show_on_front && $page_on_front > 0 && $page_on_front !== $home_id ) {
		return;
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home_id );
}

/**
 * Admin notice after launch completes.
 *
 * @return void
 */
function hvn_realty_launch_success_notice() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! get_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, false ) ) {
		return;
	}

	if ( get_option( 'hvn_realty_launch_notice_dismissed', false ) ) {
		return;
	}

	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return;
	}

	$dismiss_url = wp_nonce_url(
		add_query_arg( 'hvn-realty-dismiss-launch-notice', '1' ),
		'hvn_realty_launch_notice_dismiss'
	);

	$home_url = home_url( '/' );
	?>
	<div class="notice notice-success is-dismissible hvn-realty-launch-notice">
		<p>
			<strong><?php esc_html_e( 'Your real estate site is ready!', 'havenlytics-realty' ); ?></strong>
			<?php esc_html_e( 'Homepage, menu, footer widgets, and demo listings are live.', 'havenlytics-realty' ); ?>
		</p>
		<p class="submit">
			<a href="<?php echo esc_url( $home_url ); ?>" class="button button-primary" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'View Website', 'havenlytics-realty' ); ?>
			</a>
			<a href="<?php echo esc_url( function_exists( 'hvn_realty_get_realty_admin_url' ) ? hvn_realty_get_realty_admin_url() : admin_url( 'themes.php' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Open Realty', 'havenlytics-realty' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Customize', 'havenlytics-realty' ); ?>
			</a>
			<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Dismiss', 'havenlytics-realty' ); ?>
			</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'hvn_realty_launch_success_notice' );

/**
 * Dismiss launch success notice.
 *
 * @return void
 */
function hvn_realty_launch_notice_dismiss() {
	if ( ! isset( $_GET['hvn-realty-dismiss-launch-notice'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'hvn_realty_launch_notice_dismiss' ) ) {
		return;
	}

	update_option( 'hvn_realty_launch_notice_dismissed', true );

	wp_safe_redirect( remove_query_arg( array( 'hvn-realty-dismiss-launch-notice', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'hvn_realty_launch_notice_dismiss' );
