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
 * Map Customizer homepage section IDs to preview selectors.
 *
 * @return array<string, string>
 */
function hvn_realty_get_customizer_home_section_selectors() {
	return array(
		'hvn_realty_home_hero'           => '#hvn-theme-home-hero',
		'hvn_realty_home_search'         => '#hvn-theme-home-search',
		'hvn_realty_home_why'            => '.hvn-theme-home-why',
		'hvn_realty_home_featured'       => '#hvn-theme-home-properties',
		'hvn_realty_home_property_types' => '#hvn-theme-home-types',
		'hvn_realty_home_locations'      => '#hvn-theme-home-locations',
		'hvn_realty_home_agents'         => '#hvn-theme-home-agents',
		'hvn_realty_home_testimonials'   => '#hvn-theme-home-testimonials',
		'hvn_realty_home_blog'           => '#hvn-theme-home-blog',
		'hvn_realty_home_cta'            => '.hvn-theme-home-cta',
	);
}

/**
 * Map homepage visibility settings to preview selectors.
 *
 * @return array<string, string>
 */
function hvn_realty_get_customizer_home_section_visibility_map() {
	return array(
		'hvn_realty_home_show_hero'         => '#hvn-theme-home-hero',
		'hvn_realty_home_show_search'       => '#hvn-theme-home-search',
		'hvn_realty_home_show_why'          => '.hvn-theme-home-why',
		'hvn_realty_home_show_properties'   => '#hvn-theme-home-properties',
		'hvn_realty_home_show_types'        => '#hvn-theme-home-types',
		'hvn_realty_home_show_locations'    => '#hvn-theme-home-locations',
		'hvn_realty_home_show_agents'       => '#hvn-theme-home-agents',
		'hvn_realty_home_show_testimonials' => '#hvn-theme-home-testimonials',
		'hvn_realty_home_show_blog'         => '#hvn-theme-home-blog',
		'hvn_realty_home_show_cta'          => '.hvn-theme-home-cta',
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

	if ( file_exists( get_template_directory() . '/assets/js/customizer-why-control.js' ) ) {
		wp_enqueue_script(
			'hvn-realty-customizer-why-control',
			get_template_directory_uri() . '/assets/js/customizer-why-control.js',
			array( 'customize-controls', 'jquery', 'hvn-realty-customizer-controls-framework' ),
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

	if ( file_exists( get_template_directory() . '/assets/js/customizer-search-builder-control.js' ) ) {
		wp_enqueue_script(
			'hvn-realty-customizer-search-builder-control',
			get_template_directory_uri() . '/assets/js/customizer-search-builder-control.js',
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
