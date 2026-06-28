<?php
/**
 * Customizer CSS variable output (frontend + editor).
 *
 * @package Havenlytics_Realty
 */

/**
 * Build :root design token CSS from Customizer settings.
 *
 * @return string
 */
function hvn_realty_get_design_tokens_css() {
	$defaults    = function_exists( 'hvn_realty_get_brand_color_defaults' ) ? hvn_realty_get_brand_color_defaults() : array( 'primary' => '#C9A36B', 'secondary' => '#A9803F', 'accent' => '#FF9AA2' );
	$primary     = hvn_realty_get_design_token( 'primary', $defaults['primary'] );
	$secondary   = hvn_realty_get_design_token( 'secondary', $defaults['secondary'] );
	$accent      = hvn_realty_get_design_token( 'accent', $defaults['accent'] );
	$color_bridge = function_exists( 'hvn_realty_get_color_bridge_css' ) ? hvn_realty_get_color_bridge_css() : '';
	$text        = get_theme_mod( 'hvn_realty_text_color', '#1E1E2F' );
	$background  = get_theme_mod( 'hvn_realty_background_color', '#F8F8F8' );
	$border      = get_theme_mod( 'hvn_realty_border_color', '#E4E4ED' );
	$radius      = absint( get_theme_mod( 'hvn_realty_border_radius', 8 ) );
	$container   = absint( get_theme_mod( 'hvn_realty_container_width', 1280 ) );
	$font_size   = hvn_realty_get_base_font_size();
	$line_height = hvn_realty_sanitize_line_height( get_theme_mod( 'hvn_realty_line_height', 1.5 ) );
	$scale       = hvn_realty_get_heading_scale_multiplier();

	$body_font    = hvn_realty_sanitize_font_choice( get_theme_mod( 'hvn_realty_body_font_family', 'inter' ) );
	$heading_font = hvn_realty_sanitize_font_choice( get_theme_mod( 'hvn_realty_heading_font_family', 'fraunces' ) );
	$nav_font     = hvn_realty_sanitize_font_choice( get_theme_mod( 'hvn_realty_nav_font_family', 'inter' ) );

	$footer_bg   = get_theme_mod( 'hvn_realty_footer_bg_color', '#212529' );
	$footer_text = get_theme_mod( 'hvn_realty_footer_text_color', '#adb5bd' );
	$footer_link = get_theme_mod( 'hvn_realty_footer_link_color', '#dee2e6' );

	$primary_dark  = hvn_realty_darken_color( $primary, 10 );
	$primary_light = hvn_realty_lighten_color( $primary, 20 );

	$success = sanitize_hex_color( get_theme_mod( 'hvn_realty_success_color', '#00B46A' ) );
	$warning = sanitize_hex_color( get_theme_mod( 'hvn_realty_warning_color', '#FFB507' ) );
	$danger  = sanitize_hex_color( get_theme_mod( 'hvn_realty_danger_color', '#FF4D4F' ) );
	$success = $success ? $success : '#00B46A';
	$warning = $warning ? $warning : '#FFB507';
	$danger  = $danger ? $danger : '#FF4D4F';

	return ':root {
	' . $color_bridge . '
	--hvn-primary-dark: ' . esc_attr( $primary_dark ) . ';
	--hvn-primary-light: ' . esc_attr( $primary_light ) . ';
	--hvn-success: ' . esc_attr( $success ) . ';
	--hvn-warning: ' . esc_attr( $warning ) . ';
	--hvn-danger: ' . esc_attr( $danger ) . ';
	--hvn-theme-primary: var(--hvn-primary);
	--hvn-theme-secondary: var(--hvn-secondary);
	--hvn-theme-accent: var(--hvn-accent);
	--hvn-theme-success: var(--hvn-success);
	--hvn-theme-warning: var(--hvn-warning);
	--hvn-theme-danger: var(--hvn-danger);
	--hvn-text: ' . esc_attr( $text ) . ';
	--hvn-bg: ' . esc_attr( $background ) . ';
	--hvn-border: ' . esc_attr( $border ) . ';
	--hvn-container: ' . esc_attr( $container ) . 'px;
	--hvn-radius: ' . esc_attr( $radius ) . 'px;
	--hvn-font-size: ' . esc_attr( $font_size ) . 'px;
	--hvn-line-height: ' . esc_attr( $line_height ) . ';
	--hvn-heading-scale: ' . esc_attr( $scale ) . ';
	--hvn-font-body: ' . hvn_realty_get_font_stack_for_css( $body_font ) . ';
	--hvn-font-heading: ' . hvn_realty_get_font_stack_for_css( $heading_font ) . ';
	--hvn-font-nav: ' . hvn_realty_get_font_stack_for_css( $nav_font ) . ';
	--hvn-h1-size: calc(var(--hvn-font-size) * 3 * var(--hvn-heading-scale));
	--hvn-h2-size: calc(var(--hvn-font-size) * 2.25 * var(--hvn-heading-scale));
	--hvn-h3-size: calc(var(--hvn-font-size) * 1.875 * var(--hvn-heading-scale));
	--hvn-h4-size: calc(var(--hvn-font-size) * 1.5 * var(--hvn-heading-scale));
	--hvn-h5-size: calc(var(--hvn-font-size) * 1.25 * var(--hvn-heading-scale));
	--hvn-h6-size: calc(var(--hvn-font-size) * 1.125 * var(--hvn-heading-scale));
	' . hvn_realty_get_heading_weight_css_declarations() . '
	--hvn-footer-bg: ' . esc_attr( $footer_bg ) . ';
	--hvn-footer-text: ' . esc_attr( $footer_text ) . ';
	--hvn-footer-link: ' . esc_attr( $footer_link ) . ';
	--hvn-theme-brand-primary-dark: ' . esc_attr( $primary_dark ) . ';
	--hvn-theme-brand-primary-light: ' . esc_attr( $primary_light ) . ';
	--hvn-theme-text-primary: var(--hvn-text);
	--hvn-theme-color-bg-light: var(--hvn-bg);
	--hvn-theme-border-color: var(--hvn-border);
	--hvn-theme-border-radius-md: var(--hvn-radius);
	--hvn-theme-container-max-width: var(--hvn-container);
	--hvn-theme-container-width: var(--hvn-container);
	--hvn-theme-radius: var(--hvn-radius);
	--hvn-theme-spacing: 1.5rem;
	--hvn-theme-section-gap: clamp(3rem, 6vw, 6rem);
	--hvn-theme-shadow: 0 18px 40px -12px rgba(16, 24, 40, 0.12);
	--hvn-theme-shadow-sm: 0 2px 10px rgba(16, 24, 40, 0.06);
	--hvn-theme-font-size-md: var(--hvn-font-size);
	--hvn-theme-font-family-base: var(--hvn-font-body);
	--hvn-theme-font-family-heading: var(--hvn-font-heading);
	--hvn-theme-line-height-normal: var(--hvn-line-height);
	--hvn-theme-button-radius: var(--hvn-radius);
	--hvn-theme-footer-bg: var(--hvn-footer-bg);
	--hvn-theme-footer-color: var(--hvn-footer-text);
	--hvn-theme-footer-link-color: var(--hvn-footer-link);
}
';
}

/**
 * Output design tokens on the frontend.
 */
function hvn_realty_output_global_css_variables() {
	wp_add_inline_style( 'hvn-realty-theme', hvn_realty_get_design_tokens_css() );
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_output_global_css_variables', 20 );

/**
 * Output design tokens in the block editor.
 */
function hvn_realty_output_editor_css_variables() {
	wp_add_inline_style( 'hvn-realty-editor', hvn_realty_get_design_tokens_css() );
}
add_action( 'enqueue_block_editor_assets', 'hvn_realty_output_editor_css_variables', 20 );

/**
 * Register editor stylesheet handle for inline tokens.
 */
function hvn_realty_register_editor_style_handle() {
	wp_register_style( 'hvn-realty-editor', false, array(), HVN_REALTY_VERSION );
	wp_enqueue_style( 'hvn-realty-editor' );
}
add_action( 'enqueue_block_editor_assets', 'hvn_realty_register_editor_style_handle', 10 );

/**
 * Output layout-specific inline CSS.
 */
function hvn_realty_output_layout_inline_css() {
	if ( 'full' === hvn_realty_get_container_mode() ) {
		wp_add_inline_style(
			'hvn-realty-theme',
			'.hvn-container-full .hvn-theme-container { max-width: 100%; }'
		);
	}
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_output_layout_inline_css', 25 );
