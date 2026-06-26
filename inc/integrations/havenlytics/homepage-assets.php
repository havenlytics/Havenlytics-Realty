<?php

/**

 * Ensure Havenlytics plugin assets load on the theme homepage.

 *

 * Shortcodes render from template parts (not post_content), so the plugin

 * asset loader must be primed before its wp_enqueue_scripts pass.

 *

 * @package Havenlytics_Realty

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



/**

 * Shortcode markers appended to the front page post for plugin detection.

 *

 * @return string

 */

function hvn_realty_get_homepage_shortcode_markers() {

	return '[hvnly_property_search][hvnly_featured_properties][hvnly_property_grid][hvnly_property_agents][hvnly_property_agencies]';

}



/**

 * Prime plugin shortcode detection on the real estate homepage.

 *

 * @return void

 */

function hvn_realty_prime_homepage_plugin_assets() {

	if ( ! hvn_realty_should_show_realty_home() || ! hvn_realty_is_havenlytics_plugin_active() ) {

		return;

	}



	global $post, $hvnly_has_shortcode;



	$hvnly_has_shortcode = true;



	if ( ! is_a( $post, 'WP_Post' ) ) {

		$front_id = (int) get_option( 'page_on_front', 0 );

		if ( $front_id > 0 ) {

			$post = get_post( $front_id );

		}

	}



	if ( ! is_a( $post, 'WP_Post' ) ) {

		$post = new WP_Post(

			(object) array(

				'ID'           => 0,

				'post_type'    => 'page',

				'post_status'  => 'publish',

				'post_content' => hvn_realty_get_homepage_shortcode_markers(),

			)

		);

	}



	$markers = hvn_realty_get_homepage_shortcode_markers();

	if ( false === strpos( $post->post_content, $markers ) ) {

		$post->post_content .= $markers;

	}

}

add_action( 'wp', 'hvn_realty_prime_homepage_plugin_assets', 0 );

add_action( 'wp_enqueue_scripts', 'hvn_realty_prime_homepage_plugin_assets', 0 );



/**

 * Enqueue agent/agency card styles required for listing badges on homepage.

 *

 * @return void

 */

function hvn_realty_enqueue_homepage_plugin_card_styles() {

	if ( ! hvn_realty_should_show_realty_home() || ! hvn_realty_is_havenlytics_plugin_active() ) {

		return;

	}



	foreach ( array( 'hvnly-frontend-cards', 'hvnly-frontend-property-agents-archive' ) as $handle ) {

		if ( wp_style_is( $handle, 'registered' ) ) {

			wp_enqueue_style( $handle );

		}

	}



	if ( function_exists( 'hvnly_enqueue_property_agencies_listing_assets' ) ) {

		hvnly_enqueue_property_agencies_listing_assets();

	}

}

add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_homepage_plugin_card_styles', 32 );



/**

 * Enqueue homepage interaction script.

 *

 * @return void

 */

function hvn_realty_enqueue_homepage_scripts() {

	if ( ! function_exists( 'hvn_realty_should_show_realty_home' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {

		return;

	}

	if ( ! hvn_realty_should_show_realty_home() && ! is_customize_preview() ) {

		return;

	}



	if ( wp_style_is( 'hvnly-frontend-property-single', 'registered' ) ) {

		wp_enqueue_style( 'hvnly-frontend-property-single' );

	}



	$home_script_deps = array( 'jquery' );

	if ( wp_script_is( 'hvnly-frontend-property-ajax-root', 'registered' ) ) {

		$home_script_deps[] = 'hvnly-frontend-property-ajax-root';

	}



	$home_script_path = get_template_directory() . '/assets/js/havenlytics-home.js';

	if ( ! file_exists( $home_script_path ) ) {

		return;

	}



	wp_enqueue_script(

		'hvn-realty-havenlytics-home',

		HVN_REALTY_TEMPLATE_URL . '/assets/js/havenlytics-home.js',

		$home_script_deps,

		HVN_REALTY_VERSION,

		true

	);



	if ( function_exists( 'hvn_realty_get_home_carousel_settings' ) ) {

		wp_localize_script(

			'hvn-realty-havenlytics-home',

			'hvnRealtyHomeCarousel',

			hvn_realty_get_home_carousel_settings()

		);

	}

	if ( function_exists( 'hvn_realty_show_hero_search_panel' ) && hvn_realty_show_hero_search_panel() ) {
		$hero_search_script_path = get_template_directory() . '/assets/js/hero-search.js';

		if ( file_exists( $hero_search_script_path ) ) {
			wp_enqueue_script(
				'hvn-realty-hero-search',
				HVN_REALTY_TEMPLATE_URL . '/assets/js/hero-search.js',
				array( 'jquery' ),
				HVN_REALTY_VERSION,
				true
			);
		}
	}

}

add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_homepage_scripts', 35 );


