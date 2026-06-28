<?php
/**
 * Homepage 2.0.0 scripts.
 *
 * The rebuilt homepage uses a single vanilla-JS file and renders its own
 * markup, so no plugin shortcode priming or plugin frontend bundles are
 * loaded here.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the homepage interaction script (homepage only).
 *
 * @return void
 */
function hvn_realty_enqueue_homepage_scripts() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) ) {
		return;
	}

	if ( ! hvn_realty_is_home_design() && ! is_customize_preview() ) {
		return;
	}

	if ( ! file_exists( get_template_directory() . '/assets/js/home.js' ) ) {
		return;
	}

	wp_enqueue_script(
		'hvn-realty-home',
		HVN_REALTY_TEMPLATE_URL . '/assets/js/home.js',
		array(),
		HVN_REALTY_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_homepage_scripts', 35 );
