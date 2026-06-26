<?php
/**
 * Blog pagination markup and renderer.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blog-scoped pagination nav markup.
 *
 * @param string $template             Default template.
 * @param string $class                Nav class.
 * @param string $screen_reader_text   Screen reader heading.
 * @param string $aria_label           Nav aria-label.
 * @param string $location             Navigation context (posts, comments, etc.).
 * @return string
 */
function hvn_realty_blog_pagination_markup( $template, $class = '', $screen_reader_text = '', $aria_label = '', $location = '' ) {
	if ( 'comments' === $location ) {
		return $template;
	}

	if ( ! function_exists( 'hvn_realty_is_blog_view' ) || ! hvn_realty_is_blog_view() ) {
		return $template;
	}

	return '
	<nav class="navigation hvn-theme-pagination hvn-blog-pagination %1$s" role="navigation" aria-label="%4$s">
		<h2 class="screen-reader-text">%2$s</h2>
		<div class="nav-links hvn-theme-pagination-links hvn-blog-pagination__links">%3$s</div>
	</nav>
	';
}
add_filter( 'navigation_markup_template', 'hvn_realty_blog_pagination_markup', 20 );

/**
 * Output blog archive pagination.
 *
 * @return void
 */
function hvn_realty_blog_the_pagination() {
	global $wp_query;

	if ( ! $wp_query instanceof WP_Query || (int) $wp_query->max_num_pages <= 1 ) {
		return;
	}

	the_posts_pagination(
		array(
			'prev_text'          => '<span class="hvn-blog-pagination__prev">' . esc_html__( 'Prev', 'havenlytics-realty' ) . '</span>',
			'next_text'          => '<span class="hvn-blog-pagination__next">' . esc_html__( 'Next', 'havenlytics-realty' ) . '</span>',
			'mid_size'           => 2,
			'screen_reader_text' => esc_html__( 'Posts navigation', 'havenlytics-realty' ),
		)
	);
}
