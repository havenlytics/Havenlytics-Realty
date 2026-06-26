<?php
/**
 * Move core WordPress Customizer sections into Havenlytics Theme Settings panel.
 *
 * @package Havenlytics_Realty
 */

/**
 * Attach native WP sections and set Havenlytics section order.
 *
 * Order:
 * 1. Global Design System
 * 2. Site Identity
 * 3. Header Settings
 * 4. Typography Settings
 * 5. Layout Settings
 * 6. Footer Settings
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_panel_integration( $wp_customize ) {
	$theme_sections = array(
		'hvn_realty_global_design'       => 10,
		'hvn_realty_header_settings'     => 30,
		'hvn_realty_typography_settings' => 40,
		'hvn_realty_layout_settings'     => 50,
		'hvn_realty_footer_settings'     => 60,
	);

	foreach ( $theme_sections as $section_id => $priority ) {
		if ( $wp_customize->get_section( $section_id ) ) {
			$wp_customize->get_section( $section_id )->panel    = HVN_REALTY_CUSTOMIZER_PANEL;
			$wp_customize->get_section( $section_id )->priority = $priority;
		}
	}

	if ( $wp_customize->get_section( 'title_tagline' ) ) {
		$wp_customize->get_section( 'title_tagline' )->panel    = HVN_REALTY_CUSTOMIZER_PANEL;
		$wp_customize->get_section( 'title_tagline' )->priority = 20;
		$wp_customize->get_section( 'title_tagline' )->title    = esc_html__( 'Site Identity', 'havenlytics-realty' );
	}
}
add_action( 'customize_register', 'hvn_realty_customizer_panel_integration', 20 );

/**
 * Remove header/background image sections from Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_remove_image_customizer_sections( $wp_customize ) {
	$wp_customize->remove_section( 'header_image' );
	$wp_customize->remove_section( 'background_image' );
}
add_action( 'customize_register', 'hvn_realty_remove_image_customizer_sections', 999 );
