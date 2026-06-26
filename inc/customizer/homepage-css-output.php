<?php

/**

 * Homepage Customizer CSS output.

 *

 * @package Havenlytics_Realty

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



/**

 * Build hero background CSS from Customizer settings.

 *

 * @return string

 */

function hvn_realty_get_home_hero_background_css() {

	$color    = sanitize_hex_color( get_theme_mod( 'hvn_realty_home_hero_bg_color', '' ) );

	$image_id = absint( get_theme_mod( 'hvn_realty_home_hero_bg_image', 0 ) );

	$image    = $image_id > 0 ? wp_get_attachment_image_url( $image_id, 'full' ) : '';



	if ( ! $color && ! $image ) {

		return '';

	}



	$rules = array();



	if ( $image ) {

		$rules[] = 'background-image: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 0, 0, 0.35) 100%), url(' . esc_url( $image ) . ')';

		$rules[] = 'background-size: cover';

		$rules[] = 'background-position: center center';

		$rules[] = 'background-repeat: no-repeat';

	} elseif ( $color ) {

		$secondary = sanitize_hex_color( get_theme_mod( 'hvn_realty_secondary_color', '#764ba2' ) ) ?: '#764ba2';

		$rules[]   = 'background: linear-gradient(135deg, ' . $color . ' 0%, ' . $secondary . ' 100%)';

	}



	return '.hvn-realty-home-hero {' . implode( '; ', $rules ) . ';}';

}



/**

 * Build homepage carousel gap CSS variable.

 *

 * @return string

 */

function hvn_realty_get_home_carousel_css() {

	if ( ! function_exists( 'hvn_realty_get_home_carousel_settings' ) ) {

		return '';

	}



	$settings = hvn_realty_get_home_carousel_settings();

	$gap      = absint( $settings['gap'] ?? 16 );



	return ':root{--hvn-realty-carousel-gap:' . $gap . 'px;}';

}



/**

 * Build hero height CSS variables from Customizer.

 *

 * @return string

 */

function hvn_realty_get_home_hero_height_css() {

	if ( ! function_exists( 'hvn_realty_get_home_hero_height' ) || ! function_exists( 'hvn_realty_get_home_hero_height_mobile' ) ) {

		return '';

	}



	$desktop = hvn_realty_get_home_hero_height();

	$mobile  = hvn_realty_get_home_hero_height_mobile();



	return '.hvn-realty-section--hero{--hvn-realty-hero-height:' . $desktop . 'vh;--hvn-realty-hero-height-mobile:' . $mobile . 'vh;}';

}



/**

 * Build hero search position CSS variables from Customizer.

 *

 * @return string

 */

function hvn_realty_get_home_hero_search_position_css() {

	if ( ! function_exists( 'hvn_realty_get_hero_search_position_css' ) ) {

		return '';

	}



	return hvn_realty_get_hero_search_position_css();

}



/**
 * Stylesheet handle for homepage Customizer inline CSS.
 *
 * @return string
 */
function hvn_realty_get_homepage_inline_style_handle() {
	if ( wp_style_is( 'hvn-realty-havenlytics-compat', 'registered' ) ) {
		return 'hvn-realty-havenlytics-compat';
	}

	if ( wp_style_is( 'hvn-realty-home-blog', 'registered' ) ) {
		return 'hvn-realty-home-blog';
	}

	return 'hvn-realty-theme';
}



/**

 * Output homepage Customizer styles on the front end.

 *

 * @return void

 */

function hvn_realty_enqueue_homepage_customizer_css() {

	if ( ! function_exists( 'hvn_realty_should_show_realty_home' ) || ( ! hvn_realty_should_show_realty_home() && ! is_customize_preview() ) ) {

		return;

	}



	$css = hvn_realty_get_home_hero_background_css();

	$css .= hvn_realty_get_home_carousel_css();

	$css .= hvn_realty_get_home_hero_height_css();

	$css .= hvn_realty_get_home_hero_search_position_css();



	if ( '' === $css ) {

		return;

	}

	$handle = hvn_realty_get_homepage_inline_style_handle();

	if ( ! wp_style_is( $handle, 'enqueued' ) ) {
		wp_enqueue_style( $handle );
	}

	wp_add_inline_style( $handle, $css );

}

add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_homepage_customizer_css', 30 );



/**

 * Ensure homepage styles load in the Customizer preview iframe.

 *

 * @return void

 */

function hvn_realty_customizer_preview_homepage_assets() {

	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {

		return;

	}



	if ( function_exists( 'hvn_realty_enqueue_home_styles' ) ) {

		hvn_realty_enqueue_home_styles();

	}

}

add_action( 'customize_preview_init', 'hvn_realty_customizer_preview_homepage_assets', 20 );



/**

 * Default Customizer preview to the static front page when set.

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


