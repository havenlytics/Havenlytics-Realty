<?php
/**
 * Blog template loader — templates/blog/ with legacy fallbacks.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load a blog module template partial.
 *
 * @param string $slug Template slug (content, pagination, etc.).
 * @param string $name Optional name suffix.
 * @return void
 */
function hvn_realty_get_blog_template_part( $slug, $name = null ) {
	get_template_part( 'templates/blog/' . $slug, $name );
}

/**
 * Inline style attribute for grid column CSS variable.
 *
 * Column count is driven by body data-blog-cols, loop classes, and
 * wp_add_inline_style() so responsive breakpoints can override desktop values.
 * Element-level --hvn-blog-columns inline styles block @media rules.
 *
 * @return string Always empty; Customizer preview sets columns via JS.
 */
function hvn_realty_get_blog_grid_style_attr() {
	return '';
}
