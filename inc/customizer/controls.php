<?php

/**

 * Customizer controls panel — scroll preview to homepage sections.

 *

 * @package Havenlytics_Realty

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



/**

 * Map Customizer section IDs to homepage preview selectors.

 *

 * @return array<string, string>

 */

function hvn_realty_get_customizer_home_section_selectors() {

	return array(

		'hvn_realty_home_hero'       => '#hvn-realty-section-hero',

		'hvn_realty_home_hero_search' => '#hvn-realty-hero-search',

		'hvn_realty_home_hero_search_position' => '#hvn-realty-hero-search',

		'hvn_realty_home_featured'   => '#hvn-realty-section-featured',

		'hvn_realty_home_department' => '#hvn-realty-section-departments',

		'hvn_realty_home_taxonomies' => '#hvn-realty-section-taxonomies',

		'hvn_realty_home_property_types' => '#hvn-realty-section-property-types',

		'hvn_realty_home_agents'     => '#hvn-realty-section-agents',

		'hvn_realty_home_agencies'   => '#hvn-realty-section-agencies',

		'hvn_realty_home_blog'       => '#hvn-realty-section-blog',

		'hvn_realty_home_testimonials' => '#hvn-realty-section-testimonials',

		'hvn_realty_home_cta'        => '#hvn-realty-section-cta',

		'hvn_realty_home_carousel'   => '#hvn-realty-section-featured',

	);

}



/**

 * Map homepage visibility settings to preview selectors.

 *

 * @return array<string, string>

 */

function hvn_realty_get_customizer_home_section_visibility_map() {

	return array(

		'hvn_realty_home_show_hero_map'            => '#hvn-realty-section-hero',

		'hvn_realty_home_show_featured_properties' => '#hvn-realty-section-featured',

		'hvn_realty_home_show_latest_properties'   => '#hvn-realty-section-departments',

		'hvn_realty_home_show_property_taxonomies' => '#hvn-realty-section-taxonomies',

		'hvn_realty_home_show_property_locations'  => '#hvn-realty-section-taxonomies',

		'hvn_realty_home_show_property_types'      => '#hvn-realty-section-property-types',

		'hvn_realty_home_show_featured_agents'     => '#hvn-realty-section-agents',

		'hvn_realty_home_show_featured_agencies'   => '#hvn-realty-section-agencies',

		'hvn_realty_home_show_latest_posts'        => '#hvn-realty-section-blog',

		'hvn_realty_home_show_testimonials'      => '#hvn-realty-section-testimonials',

		'hvn_realty_home_show_cta_banner'          => '#hvn-realty-section-cta',

		'hvn_realty_home_show_statistics'          => '.hvn-realty-home-stats',

		'hvn_realty_home_show_footer_cta'          => '.hvn-realty-home-footer-cta',

	);

}



/**

 * Enqueue Customizer controls panel script.

 *

 * @return void

 */

function hvn_realty_customize_controls_js() {

	wp_enqueue_script( 'jquery-ui-sortable' );



	if ( file_exists( get_template_directory() . '/assets/css/customizer-controls.css' ) ) {

		wp_enqueue_style(

			'hvn-realty-customizer-controls-ui',

			get_template_directory_uri() . '/assets/css/customizer-controls.css',

			array(),

			HVN_REALTY_VERSION

		);

	}



	if ( file_exists( get_template_directory() . '/assets/js/customizer-controls-framework.js' ) ) {

		wp_enqueue_script(

			'hvn-realty-customizer-controls-framework',

			get_template_directory_uri() . '/assets/js/customizer-controls-framework.js',

			array( 'customize-controls', 'jquery', 'jquery-ui-sortable' ),

			HVN_REALTY_VERSION,

			true

		);

	}



	if ( file_exists( get_template_directory() . '/assets/js/customizer-testimonials-control.js' ) ) {

		wp_enqueue_script(

			'hvn-realty-customizer-testimonials-control',

			get_template_directory_uri() . '/assets/js/customizer-testimonials-control.js',

			array( 'customize-controls', 'jquery', 'media-models', 'hvn-realty-customizer-controls-framework' ),

			HVN_REALTY_VERSION,

			true

		);

	}



	if ( file_exists( get_template_directory() . '/assets/js/customizer-section-order-control.js' ) ) {

		wp_enqueue_script(

			'hvn-realty-customizer-section-order-control',

			get_template_directory_uri() . '/assets/js/customizer-section-order-control.js',

			array( 'customize-controls', 'jquery', 'jquery-ui-sortable', 'hvn-realty-customizer-controls-framework' ),

			HVN_REALTY_VERSION,

			true

		);

	}



	if ( file_exists( get_template_directory() . '/assets/js/customizer-controls.js' ) ) {
		wp_enqueue_script(
			'hvn-realty-customizer-controls',
			get_template_directory_uri() . '/assets/js/customizer-controls.js',
			array( 'customize-controls', 'jquery' ),
			HVN_REALTY_VERSION,
			true
		);

		wp_localize_script(
			'hvn-realty-customizer-controls',
			'hvnRealtyCustomizerControls',
			array(
				'homeSections' => hvn_realty_get_customizer_home_section_selectors(),
			)
		);
	}

}

add_action( 'customize_controls_enqueue_scripts', 'hvn_realty_customize_controls_js' );


