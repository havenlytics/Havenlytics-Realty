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
 * @return string Empty for list layout.
 */
function hvn_realty_get_blog_grid_style_attr() {
	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {
		return '';
	}

	$cols = function_exists( 'hvn_realty_get_blog_column_count' ) ? hvn_realty_get_blog_column_count() : 3;

	return sprintf( ' style="%s"', esc_attr( '--hvn-blog-columns: ' . (int) $cols ) );
}
