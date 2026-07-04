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

	if ( ! hvn_realty_enqueue_theme_script( 'hvn-realty-home', 'assets/js/home.js' ) ) {
		return;
	}
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_homepage_scripts', 35 );
