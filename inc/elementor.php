<?php
/**
 * Elementor integration for Havenlytics Realty.
 *
 * @package Havenlytics_Realty
 */

/**
 * Register Elementor theme support.
 */
function hvn_realty_elementor_setup() {
	add_theme_support( 'elementor' );
}
add_action( 'after_setup_theme', 'hvn_realty_elementor_setup' );

/**
 * Whether the current singular view is built with Elementor.
 *
 * @param int|null $post_id Optional post ID.
 * @return bool
 */
function hvn_realty_is_elementor_page( $post_id = null ) {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}

	if ( null === $post_id ) {
		if ( ! is_singular() ) {
			return false;
		}
		$post_id = get_queried_object_id();
	}

	$document = \Elementor\Plugin::$instance->documents->get( $post_id );

	return ( $document && $document->is_built_with_elementor() );
}

/**
 * Add body classes for Elementor full-width and canvas templates.
 *
 * @param array $classes Body classes.
 * @return array
 */
function hvn_realty_elementor_body_classes( $classes ) {
	if ( is_singular() ) {
		$template = get_post_meta( get_queried_object_id(), '_wp_page_template', true );

		if ( 'elementor_canvas' === $template ) {
			$classes[] = 'hvn-elementor-canvas';
			$classes[] = 'hvn-elementor-full-width';
		} elseif ( 'elementor_header_footer' === $template || 'elementor_theme' === $template ) {
			$classes[] = 'hvn-elementor-full-width';
		}

		if ( hvn_realty_is_elementor_page() ) {
			$classes[] = 'elementor-page';
			$classes[] = 'hvn-elementor-page';
		}
	}

	return $classes;
}
add_filter( 'body_class', 'hvn_realty_elementor_body_classes' );

/**
 * Elementor layout reset — full-width content, stable header/footer.
 */
function hvn_realty_elementor_reset_css() {
	if ( ! is_singular() ) {
		return;
	}

	$post_id   = get_queried_object_id();
	$template  = get_post_meta( $post_id, '_wp_page_template', true );
	$is_canvas = ( 'elementor_canvas' === $template );
	$is_built  = hvn_realty_is_elementor_page( $post_id );

	if ( ! $is_canvas && ! $is_built && ! in_array( $template, array( 'elementor_header_footer', 'elementor_theme' ), true ) ) {
		return;
	}

	$css = '
.elementor-page .hvn-theme-content,
.elementor-page .hvn-theme-site-content,
.hvn-elementor-canvas .hvn-theme-content,
.hvn-elementor-canvas .hvn-theme-site-content {
	padding: 0;
	margin: 0;
}
.elementor-page .hvn-theme-page-layout,
.elementor-page .hvn-theme-page-content,
.hvn-elementor-full-width .hvn-theme-page-layout {
	width: 100%;
	max-width: none;
}
/* Full-width Elementor content only — keep header/footer container boxed */
.elementor-page #primary > .hvn-theme-container,
.hvn-elementor-full-width #primary > .hvn-theme-container {
	max-width: 100%;
	padding-left: 0;
	padding-right: 0;
}
.elementor-page .hvn-theme-page-body,
.elementor-page .hvn-theme-page-article {
	max-width: none;
	margin: 0;
	padding: 0;
	border: none;
	box-shadow: none;
	background: transparent;
}
.hvn-elementor-canvas .hvn-theme-header,
.hvn-elementor-canvas .hvn-theme-footer,
.hvn-elementor-canvas .hvn-theme-site-header,
.hvn-elementor-canvas .hvn-theme-site-footer {
	display: none;
}';

	wp_add_inline_style( 'hvn-realty-style', $css );
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_elementor_reset_css', 35 );
