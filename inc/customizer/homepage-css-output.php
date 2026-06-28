<?php
/**
 * Homepage Customizer preview helpers.
 *
 * The Homepage 2.0.0 design ships its own self-contained stylesheet
 * (assets/css/home.css), so no dynamic inline CSS is generated here.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensure homepage styles load in the Customizer preview iframe.
 *
 * @return void
 */
function hvn_realty_customizer_preview_homepage_assets() {
	if ( function_exists( 'hvn_realty_enqueue_home_styles' ) ) {
		hvn_realty_enqueue_home_styles();
	}
}
add_action( 'customize_preview_init', 'hvn_realty_customizer_preview_homepage_assets', 20 );

/**
 * Default the Customizer preview to the static front page when set.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function hvn_realty_customizer_set_home_preview_url( $wp_customize ) {
	if ( ! function_exists( 'hvn_realty_customizer_homepage_is_active' ) || ! hvn_realty_customizer_homepage_is_active() ) {
		return;
	}

	$home_id = (int) get_option( 'page_on_front', 0 );
	if ( $home_id <= 0 ) {
		return;
	}

	$permalink = get_permalink( $home_id );
	if ( is_string( $permalink ) && $permalink ) {
		$wp_customize->set_preview_url( $permalink );
	}
}
add_action( 'customize_register', 'hvn_realty_customizer_set_home_preview_url', 100 );
