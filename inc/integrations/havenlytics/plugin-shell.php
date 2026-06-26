<?php
/**
 * Havenlytics plugin shell — theme breadcrumbs and layout hooks.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output theme breadcrumbs before plugin template content.
 *
 * @return void
 */
function hvn_realty_plugin_template_breadcrumbs() {
	if ( ! hvn_realty_show_theme_breadcrumbs_on_plugin_view() ) {
		return;
	}

	// Page templates already output breadcrumbs; shortcode query swap would duplicate them.
	global $hvnly_has_shortcode;
	if ( ! empty( $hvnly_has_shortcode ) ) {
		return;
	}

	if ( function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() ) {
		return;
	}

	if ( ! is_singular( 'hvnly_property' ) && ! is_singular( 'hvnly_agent' ) && ! hvn_realty_is_property_view() && ! hvn_realty_is_agent_context() && ! hvn_realty_is_agency_context() ) {
		return;
	}

	echo '<div class="hvn-realty-plugin-shell__breadcrumbs hvn-theme-container">';
	hvn_realty_breadcrumbs();
	echo '</div>';
}
add_action( 'hvnly_before_main_content', 'hvn_realty_plugin_template_breadcrumbs', 5 );

/**
 * Add theme integration class to plugin wrapper via filter.
 *
 * @param array $classes Wrapper classes.
 * @return array
 */
function hvn_realty_plugin_wrapper_classes( $classes ) {
	if ( ! is_array( $classes ) ) {
		$classes = array();
	}

	$classes[] = 'hvn-realty-plugin-shell';

	return array_unique( $classes );
}
add_filter( 'hvnly_content_wrapper_classes', 'hvn_realty_plugin_wrapper_classes' );

/**
 * Brand-primary CSS variables for plugin pages (Customizer tokens).
 *
 * @return void
 */
function hvn_realty_plugin_inline_design_tokens() {
	if ( ! hvn_realty_is_havenlytics_view() && ! hvn_realty_should_show_realty_home() ) {
		return;
	}

	$primary   = hvn_realty_get_design_token_css_value( 'primary' );
	$secondary = hvn_realty_get_design_token_css_value( 'secondary' );
	$accent    = hvn_realty_get_design_token_css_value( 'accent' );
	$text      = sanitize_hex_color( get_theme_mod( 'hvn_realty_text_color', '#1E1E2F' ) ) ?: '#1E1E2F';

	$css = sprintf(
		':root { --hvn-realty-plugin-primary: %1$s; --hvn-realty-plugin-secondary: %2$s; --hvn-realty-plugin-accent: %3$s; --hvn-realty-plugin-text: %4$s; }',
		esc_attr( $primary ),
		esc_attr( $secondary ),
		esc_attr( $accent ),
		esc_attr( $text )
	);

	$handle = wp_style_is( 'hvn-realty-havenlytics-compat', 'enqueued' ) ? 'hvn-realty-havenlytics-compat' : 'hvn-realty-theme';

	if ( wp_style_is( $handle, 'enqueued' ) ) {
		wp_add_inline_style( $handle, $css );
	}
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_plugin_inline_design_tokens', 30 );
