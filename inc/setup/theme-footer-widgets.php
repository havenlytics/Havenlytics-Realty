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
 * Seed footer widgets when empty (launch or deferred admin check).
 *
 * @return void
 */
function hvn_realty_maybe_seed_footer_widgets() {
	if ( get_option( HVN_REALTY_FOOTER_WIDGETS_OPTION, false ) ) {
		return;
	}

	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
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

	$widgets[ $next_id ] = $instance;
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

	$home_id = (int) get_option( HVN_REALTY_HOME_PAGE_OPTION, 0 );
	if ( $home_id > 0 ) {
		$links[] = sprintf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( get_permalink( $home_id ) ),
			esc_html__( 'Home', 'havenlytics-realty' )
		);
	}

	foreach ( hvn_realty_launch_get_plugin_menu_pages() as $page_key => $label ) {
		$page_id = hvn_realty_get_plugin_page_id( $page_key );
		if ( $page_id <= 0 ) {
			continue;
		}

		$links[] = sprintf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( get_permalink( $page_id ) ),
			esc_html( $label )
		);
	}

	$blog_id = absint( get_option( 'page_for_posts', 0 ) );
	if ( $blog_id > 0 ) {
		$links[] = sprintf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( get_permalink( $blog_id ) ),
			esc_html__( 'Blog', 'havenlytics-realty' )
		);
	}

	if ( empty( $links ) ) {
		return '';
	}

	return '<ul class="hvn-theme-footer-link-list">' . implode( '', $links ) . '</ul>';
}

/**
 * Populate footer-1 … footer-4 with default widgets.
 *
 * @return void
 */
function hvn_realty_launch_setup_footer_widgets() {
	if ( ! function_exists( 'hvn_realty_launch_create_footer_menus' ) ) {
		return;
	}

	$footer_menus = hvn_realty_launch_create_footer_menus();
	$site_name    = get_bloginfo( 'name', 'display' );
	$tagline      = get_bloginfo( 'description', 'display' );

	$about_text = $tagline ? $tagline : __( 'Browse properties, connect with agents, and explore listings powered by Havenlytics.', 'havenlytics-realty' );

	$footer_1 = hvn_realty_launch_insert_widget(
		'text',
		array(
			'title'  => $site_name ? $site_name : __( 'About', 'havenlytics-realty' ),
			'text'   => esc_html( $about_text ),
			'filter' => true,
			'visual' => false,
		)
	);

	$footer_2 = '';
	if ( ! empty( $footer_menus['properties'] ) ) {
		$footer_2 = hvn_realty_launch_insert_widget(
			'nav_menu',
			array(
				'title'    => __( 'Properties', 'havenlytics-realty' ),
				'nav_menu' => (int) $footer_menus['properties'],
			)
		);
	}

	$footer_3 = '';
	if ( ! empty( $footer_menus['directory'] ) ) {
		$footer_3 = hvn_realty_launch_insert_widget(
			'nav_menu',
			array(
				'title'    => __( 'Agents & Agencies', 'havenlytics-realty' ),
				'nav_menu' => (int) $footer_menus['directory'],
			)
		);
	}

	$explore_html = hvn_realty_launch_get_footer_explore_html();
	$footer_4     = hvn_realty_launch_insert_widget(
		'custom_html',
		array(
			'title'   => __( 'Explore', 'havenlytics-realty' ),
			'content' => $explore_html,
		)
	);

	hvn_realty_launch_assign_sidebar( 'footer-1', array( $footer_1 ) );
	hvn_realty_launch_assign_sidebar( 'footer-2', array( $footer_2 ) );
	hvn_realty_launch_assign_sidebar( 'footer-3', array( $footer_3 ) );
	hvn_realty_launch_assign_sidebar( 'footer-4', array( $footer_4 ) );
}
