<?php
/**
 * Legacy post template — delegates to the blog module on archive views.
 *
 * @package Havenlytics_Realty
 */

if ( ! is_singular() && function_exists( 'hvn_realty_is_blog_view' ) && hvn_realty_is_blog_view() ) {
	$slug = function_exists( 'hvn_realty_get_blog_card_template' ) ? hvn_realty_get_blog_card_template() : 'grid';

	if ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
		hvn_realty_get_blog_template_part( 'content', $slug );
	} else {
		get_template_part( 'templates/blog/content', $slug );
	}
	return;
}

get_template_part( 'templates/blog/content', 'grid' );
