<?php
/**
 * Havenlytics body class integration.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add context-specific body classes for Havenlytics views.
 *
 * @param array $classes Body classes.
 * @return array
 */
function hvn_realty_havenlytics_body_classes( $classes ) {
	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return $classes;
	}

	if ( hvn_realty_is_property_view() ) {
		$classes[] = 'hvn-realty-plugin-property';
	}

	if ( hvn_realty_is_agent_context() ) {
		$classes[] = 'hvn-realty-plugin-agent';
	}

	if ( hvn_realty_is_agency_context() ) {
		$classes[] = 'hvn-realty-plugin-agency';
	}

	if ( hvn_realty_is_plugin_shortcode_page() ) {
		$classes[] = 'hvn-realty-plugin-page';
	}

	if ( hvn_realty_is_havenlytics_view() ) {
		$classes[] = 'hvn-realty-havenlytics-view';
	}

	return array_unique( $classes );
}
add_filter( 'hvn_realty_body_classes', 'hvn_realty_havenlytics_body_classes' );

/**
 * Prevent blog layout classes on Havenlytics plugin views.
 *
 * @param array $classes Body classes.
 * @return array
 */
function hvn_realty_strip_blog_classes_on_plugin_views( $classes ) {
	if ( ! function_exists( 'hvn_realty_is_havenlytics_view' ) || ! hvn_realty_is_havenlytics_view() ) {
		return $classes;
	}

	$remove = array(
		'hvn-view-blog',
		'hvn-blog-view-list',
		'hvn-blog-view-grid',
	);

	foreach ( $classes as $key => $class ) {
		if ( in_array( $class, $remove, true ) ) {
			unset( $classes[ $key ] );
		}
		if ( 0 === strpos( $class, 'hvn-posts-cols-' ) ) {
			unset( $classes[ $key ] );
		}
	}

	return array_values( $classes );
}
add_filter( 'hvn_realty_body_classes', 'hvn_realty_strip_blog_classes_on_plugin_views', 99 );

/**
 * Use page layout class on plugin shortcode pages instead of generic page only.
 *
 * @param array $classes Body classes.
 * @return array
 */
function hvn_realty_plugin_page_layout_classes( $classes ) {
	if ( hvn_realty_is_plugin_shortcode_page() ) {
		$classes[] = 'hvn-realty-plugin-shortcode-page';
		$classes[] = 'hvn-view-page';
	}

	return $classes;
}
add_filter( 'hvn_realty_body_classes', 'hvn_realty_plugin_page_layout_classes', 20 );
