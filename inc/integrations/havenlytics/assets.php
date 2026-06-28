<?php
/**
 * Havenlytics integration assets.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue the single Homepage 2.0.0 stylesheet (homepage only).
 *
 * @return void
 */
function hvn_realty_enqueue_home_styles() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) || ! hvn_realty_is_home_design() ) {
		return;
	}

	$dependency = array( 'hvn-realty-theme' );

	if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {
		hvn_realty_enqueue_theme_style( 'hvn-realty-home', 'assets/css/home.css', $dependency );
	} elseif ( file_exists( get_template_directory() . '/assets/css/home.css' ) ) {
		wp_enqueue_style(
			'hvn-realty-home',
			HVN_REALTY_TEMPLATE_URL . '/assets/css/home.css',
			$dependency,
			HVN_REALTY_VERSION
		);
	}
}

/**
 * Enqueue homepage styles and plugin-view compatibility styles.
 *
 * The compatibility stylesheet is only needed on Havenlytics plugin views
 * (archives, single property, shortcode pages). The rebuilt homepage renders
 * its own markup, so it no longer loads plugin card CSS.
 *
 * @return void
 */
function hvn_realty_enqueue_havenlytics_assets() {
	hvn_realty_enqueue_home_styles();

	if (
		function_exists( 'hvn_realty_is_havenlytics_plugin_active' )
		&& hvn_realty_is_havenlytics_plugin_active()
		&& function_exists( 'hvn_realty_is_havenlytics_view' )
		&& hvn_realty_is_havenlytics_view()
	) {
		$compat_deps = array( 'hvn-realty-theme' );
		if ( wp_style_is( 'hvnly-frontend-default', 'registered' ) ) {
			$compat_deps[] = 'hvnly-frontend-default';
		}

		if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {
			hvn_realty_enqueue_theme_style( 'hvn-realty-havenlytics-compat', 'assets/css/havenlytics-compat.css', $compat_deps );
		} elseif ( file_exists( get_template_directory() . '/assets/css/havenlytics-compat.css' ) ) {
			wp_enqueue_style(
				'hvn-realty-havenlytics-compat',
				HVN_REALTY_TEMPLATE_URL . '/assets/css/havenlytics-compat.css',
				$compat_deps,
				HVN_REALTY_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_havenlytics_assets', 25 );

/**
 * Homepage display font.
 *
 * Fraunces is now the theme's default heading font and is loaded globally by
 * hvn_realty_enqueue_google_fonts() (driven by the Typography Customizer), so a
 * separate homepage-only Fraunces request is no longer required. The Customizer
 * heading-font choice remains the single source of truth on every page.
 *
 * @return void
 */
function hvn_realty_enqueue_homepage_fonts() {
}
