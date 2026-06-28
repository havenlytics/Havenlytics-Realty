<?php
/**
 * Theme launch — pre-import footer widgets.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option: footer widgets seeded flag. */
define( 'HVN_REALTY_FOOTER_WIDGETS_OPTION', 'hvn_realty_footer_widgets_seeded' );

/**
 * Seed footer widgets when empty.
 *
 * Runs once on a fresh theme activation (theme-only OR theme + plugin). Existing
 * widgets are never touched: if any footer area already holds a widget the seed
 * is skipped and only the one-time flag is recorded. Never deletes, replaces or
 * resets user widgets.
 *
 * @return void
 */
function hvn_realty_maybe_seed_footer_widgets() {
	if ( get_option( HVN_REALTY_FOOTER_WIDGETS_OPTION, false ) ) {
		return;
	}

	for ( $i = 1; $i <= 4; $i++ ) {
		if ( is_active_sidebar( 'footer-' . $i ) ) {
			update_option( HVN_REALTY_FOOTER_WIDGETS_OPTION, true );
			return;
		}
	}

	hvn_realty_launch_setup_footer_widgets();
	update_option( HVN_REALTY_FOOTER_WIDGETS_OPTION, true );
}
add_action( 'after_switch_theme', 'hvn_realty_maybe_seed_footer_widgets', 35 );

/**
 * Insert a widget instance and return its sidebar ID token.
 *
 * @param string               $id_base  Widget id base (e.g. text, nav_menu).
 * @param array<string, mixed> $instance Widget instance settings.
 * @return string Widget ID like text-3 or empty string.
 */
function hvn_realty_launch_insert_widget( $id_base, $instance ) {
	if ( ! is_array( $instance ) ) {
		return '';
	}

	$option_name = 'widget_' . $id_base;
	$widgets     = get_option( $option_name, array() );
	if ( ! is_array( $widgets ) ) {
		$widgets = array();
	}

	$next_id = 1;
	foreach ( array_keys( $widgets ) as $key ) {
		if ( is_numeric( $key ) && (int) $key >= $next_id ) {
			$next_id = (int) $key + 1;
		}
	}

	$widgets[ $next_id ]    = $instance;
	$widgets['_multiwidget'] = 1;
	update_option( $option_name, $widgets );

	return $id_base . '-' . $next_id;
}

/**
 * Assign widgets to a registered sidebar.
 *
 * @param string        $sidebar_id Sidebar ID.
 * @param array<string> $widget_ids Widget tokens.
 * @return void
 */
function hvn_realty_launch_assign_sidebar( $sidebar_id, $widget_ids ) {
	$sidebars = get_option( 'sidebars_widgets', array() );
	if ( ! is_array( $sidebars ) ) {
		$sidebars = array();
	}

	$sidebars[ $sidebar_id ] = array_values( array_filter( $widget_ids ) );
	update_option( 'sidebars_widgets', $sidebars );
}

/**
 * Build explore links HTML for footer column.
 *
 * @return string
 */
function hvn_realty_launch_get_footer_explore_html() {
	$links = array();

	// Home always resolves, with or without the plugin.
	$links[] = sprintf(
		'<li><a href="%1$s">%2$s</a></li>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Home', 'havenlytics-realty' )
	);

	// Properties / Search etc. come from plugin pages when available.
	if ( function_exists( 'hvn_realty_launch_get_plugin_menu_pages' ) && function_exists( 'hvn_realty_get_plugin_page_id' ) ) {
		foreach ( hvn_realty_launch_get_plugin_menu_pages() as $page_key => $label ) {
			$page_id = (int) hvn_realty_get_plugin_page_id( $page_key );
			if ( $page_id <= 0 ) {
				continue;
			}

			$links[] = sprintf(
				'<li><a href="%1$s">%2$s</a></li>',
				esc_url( get_permalink( $page_id ) ),
				esc_html( $label )
			);
		}
	}

	$blog_id = absint( get_option( 'page_for_posts', 0 ) );
	if ( $blog_id > 0 ) {
		$links[] = sprintf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( get_permalink( $blog_id ) ),
			esc_html__( 'Blog', 'havenlytics-realty' )
		);
	}

	// Contact page (theme-only friendly): match a page by common slug.
	$contact_page = get_page_by_path( 'contact' );
	if ( $contact_page instanceof WP_Post ) {
		$links[] = sprintf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( get_permalink( $contact_page ) ),
			esc_html__( 'Contact', 'havenlytics-realty' )
		);
	}

	if ( empty( $links ) ) {
		return '';
	}

	return '<ul class="hvn-theme-footer-link-list">' . implode( '', $links ) . '</ul>';
}

/**
 * Populate the footer widget columns with the default 2.0.1 layout.
 *
 * Brand (logo + description + social) is rendered at template level, so the
 * seeded widget columns are: Quick Links, Property Locations (dynamic) and
 * Contact. Only ever runs once on a fresh install when every footer area is
 * empty — existing widgets are never touched or overwritten.
 *
 * @return void
 */
function hvn_realty_launch_setup_footer_widgets() {
	$explore_html = hvn_realty_launch_get_footer_explore_html();

	$footer_1 = '';
	if ( '' !== $explore_html ) {
		$footer_1 = hvn_realty_launch_insert_widget(
			'custom_html',
			array(
				'title'   => __( 'Quick Links', 'havenlytics-realty' ),
				'content' => $explore_html,
			)
		);
	}

	$footer_2 = hvn_realty_launch_insert_widget(
		'hvn_realty_footer_locations',
		array(
			'title' => __( 'Property Locations', 'havenlytics-realty' ),
			'limit' => 6,
		)
	);

	$footer_3 = hvn_realty_launch_insert_widget(
		'hvn_realty_footer_contact',
		array(
			'title'   => __( 'Contact', 'havenlytics-realty' ),
			'address' => '',
			'phone'   => '',
			'email'   => sanitize_email( (string) get_bloginfo( 'admin_email' ) ),
			'hours'   => '',
		)
	);

	if ( '' !== $footer_1 ) {
		hvn_realty_launch_assign_sidebar( 'footer-1', array( $footer_1 ) );
	}
	hvn_realty_launch_assign_sidebar( 'footer-2', array( $footer_2 ) );
	hvn_realty_launch_assign_sidebar( 'footer-3', array( $footer_3 ) );
}
