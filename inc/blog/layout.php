<?php
/**
 * Blog layout helpers (grid / list / columns / body attributes).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current front-end view is a theme blog archive context.
 *
 * @return bool
 */
function hvn_realty_is_blog_view() {
	if ( is_admin() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_property_context' ) && hvn_realty_is_property_context() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_havenlytics_view' ) && hvn_realty_is_havenlytics_view() ) {
		return false;
	}

	return is_home() || is_archive() || is_search();
}

/**
 * Blog loop container class for grid mode.
 *
 * @return string
 */
function hvn_realty_get_blog_grid_class() {
	return 'hvn-blog-grid';
}

/**
 * Blog loop container class for list mode.
 *
 * @return string
 */
function hvn_realty_get_blog_list_class() {
	return 'hvn-blog-list';
}

/**
 * Raw column count from Customizer (matches live preview data-blog-cols).
 *
 * @return int
 */
function hvn_realty_get_blog_column_count() {
	if ( ! function_exists( 'hvn_realty_get_blog_columns' ) ) {
		return 3;
	}

	$columns = max( 1, min( 4, (int) hvn_realty_get_blog_columns() ) );

	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {
		return 1;
	}

	return $columns;
}

/**
 * Column class for the blog grid element.
 *
 * @return string
 */
function hvn_realty_get_blog_cols_class() {
	return 'hvn-cols-' . hvn_realty_get_blog_column_count();
}

/**
 * Combined loop wrapper classes for the active blog layout.
 *
 * @return string
 */
function hvn_realty_get_blog_loop_classes() {
	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {
		return hvn_realty_get_blog_list_class();
	}

	return trim( hvn_realty_get_blog_grid_class() . ' ' . hvn_realty_get_blog_cols_class() );
}

/**
 * Template slug for the active blog card partial.
 *
 * @return string grid|list
 */
function hvn_realty_get_blog_card_template() {
	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {
		return 'list';
	}

	return 'grid';
}

/**
 * Output data attributes on <body> for blog grid (matches Customizer value).
 *
 * @return void
 */
function hvn_realty_body_layout_attrs() {
	if ( ! hvn_realty_is_blog_view() ) {
		return;
	}

	$layout = function_exists( 'hvn_realty_get_blog_layout' ) ? hvn_realty_get_blog_layout() : 'grid';

	printf(
		' data-blog-cols="%1$s" data-blog-layout="%2$s"',
		esc_attr( (string) hvn_realty_get_blog_column_count() ),
		esc_attr( $layout )
	);
}
