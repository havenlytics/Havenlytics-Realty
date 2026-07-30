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
 * Enqueue Homepage 3.0 stylesheets (homepage only).
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
		hvn_realty_enqueue_theme_style( 'hvn-realty-home-v3', 'assets/css/home-v3.css', array( 'hvn-realty-home' ) );
	} elseif ( file_exists( get_template_directory() . '/assets/css/home.css' ) ) {
		wp_enqueue_style(
			'hvn-realty-home',
			HVN_REALTY_TEMPLATE_URL . '/assets/css/home.css',
			$dependency,
			HVN_REALTY_VERSION
		);
		if ( file_exists( get_template_directory() . '/assets/css/home-v3.css' ) ) {
			wp_enqueue_style(
				'hvn-realty-home-v3',
				HVN_REALTY_TEMPLATE_URL . '/assets/css/home-v3.css',
				array( 'hvn-realty-home' ),
				HVN_REALTY_VERSION
			);
		}
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
 * Homepage 3.0 body font (Plus Jakarta Sans).
 *
 * Fraunces remains the theme heading font via Typography Customizer.
 *
 * @return void
 */
function hvn_realty_enqueue_homepage_fonts() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) || ! hvn_realty_is_home_design() ) {
		return;
	}

	wp_enqueue_style(
		'hvn-realty-home-jakarta',
		'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_homepage_fonts', 5 );
