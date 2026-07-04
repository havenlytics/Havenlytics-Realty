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

	$controls_css = hvn_realty_enqueue_style_safe(
		'hvn-realty-customizer-controls-ui',
		'assets/css/customizer-controls.css'
	);

	if ( $controls_css && function_exists( 'hvn_realty_get_design_tokens_css' ) ) {
		$tokens_css = hvn_realty_get_design_tokens_css();
		if ( is_string( $tokens_css ) && '' !== $tokens_css ) {
			wp_add_inline_style( 'hvn-realty-customizer-controls-ui', $tokens_css );
		}
	}

	hvn_realty_enqueue_script_safe(
		'hvn-realty-customizer-controls-framework',
		'assets/js/customizer-controls-framework.js',
		array( 'customize-controls', 'jquery', 'jquery-ui-sortable' )
	);

	hvn_realty_enqueue_script_safe(
		'hvn-realty-customizer-testimonials-control',
		'assets/js/customizer-testimonials-control.js',
		array( 'customize-controls', 'jquery', 'media-models', 'hvn-realty-customizer-controls-framework' )
	);

	hvn_realty_enqueue_script_safe(
		'hvn-realty-customizer-why-control',
		'assets/js/customizer-why-control.js',
		array( 'customize-controls', 'jquery', 'hvn-realty-customizer-controls-framework' )
	);

	hvn_realty_enqueue_script_safe(
		'hvn-realty-customizer-section-order-control',
		'assets/js/customizer-section-order-control.js',
		array( 'customize-controls', 'jquery', 'jquery-ui-sortable', 'hvn-realty-customizer-controls-framework' )
	);

	hvn_realty_enqueue_script_safe(
		'hvn-realty-customizer-search-builder-control',
		'assets/js/customizer-search-builder-control.js',
		array( 'customize-controls', 'jquery', 'jquery-ui-sortable', 'hvn-realty-customizer-controls-framework' )
	);

	$controls_loaded = hvn_realty_enqueue_script_safe(
		'hvn-realty-customizer-controls',
		'assets/js/customizer-controls.js',
		array( 'customize-controls', 'jquery' )
	);

	if ( $controls_loaded ) {
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
