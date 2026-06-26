<?php
/**
 * Blog accessibility helpers — navigation labels and search landmark.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Comment navigation markup on single posts.
 *
 * @param string $template             Default template.
 * @param string $class                Nav class.
 * @param string $screen_reader_text   Screen reader heading.
 * @param string $aria_label           Nav aria-label.
 * @param string $location             Navigation context.
 * @return string
 */
function hvn_realty_blog_comments_navigation_markup( $template, $class = '', $screen_reader_text = '', $aria_label = '', $location = '' ) {
	if ( 'comments' !== $location || ! is_singular( 'post' ) ) {
		return $template;
	}

	return '
	<nav class="navigation hvn-theme-pagination hvn-theme-comments-navigation %1$s" aria-label="%4$s">
		<h2 class="screen-reader-text">%2$s</h2>
		<div class="nav-links hvn-theme-pagination-links">%3$s</div>
	</nav>
	';
}
add_filter( 'navigation_markup_template', 'hvn_realty_blog_comments_navigation_markup', 15 );

/**
 * Block names that duplicate theme comments_template() on single posts.
 *
 * @return string[]
 */
function hvn_realty_blog_duplicate_comment_blocks() {
	return array(
		'core/comments',
		'core/post-comments',
		'core/post-comments-form',
		'core/comment-template',
	);
}

/**
 * Suppress Gutenberg comment blocks when the theme renders comments below the post.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function hvn_realty_suppress_duplicate_comment_blocks( $block_content, $block ) {
	if ( ! is_singular( 'post' ) || empty( $block['blockName'] ) ) {
		return $block_content;
	}

	if ( in_array( $block['blockName'], hvn_realty_blog_duplicate_comment_blocks(), true ) ) {
		return '';
	}

	return $block_content;
}
add_filter( 'render_block', 'hvn_realty_suppress_duplicate_comment_blocks', 10, 2 );
