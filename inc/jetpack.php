<?php
/**
 * Jetpack Compatibility File
 *
 * @link https://jetpack.com/
 *
 * @package Havenlytics_Realty
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 * See: https://jetpack.com/support/responsive-videos/
 * See: https://jetpack.com/support/content-options/
 */
function hvn_realty_jetpack_setup() {
    // Add theme support for Infinite Scroll.
    add_theme_support(
        'infinite-scroll',
        array(
            'container' => 'primary',
            'render'    => 'hvn_realty_infinite_scroll_render',
            'footer'    => 'page',
            'wrapper'   => false,
        )
    );

    // Add theme support for Responsive Videos.
    add_theme_support( 'jetpack-responsive-videos' );

    // Add theme support for Content Options.
    add_theme_support(
        'jetpack-content-options',
        array(
            'post-details' => array(
                'stylesheet' => 'hvn-realty-theme',
                'date'       => '.posted-on',
                'categories' => '.cat-links',
                'tags'       => '.tags-links',
                'author'     => '.byline',
                'comment'    => '.comments-link',
            ),
            'featured-images' => array(
                'archive' => true,
                'post'    => true,
                'page'    => true,
            ),
        )
    );
}
add_action( 'after_setup_theme', 'hvn_realty_jetpack_setup' );

/**
 * Custom render function for Infinite Scroll.
 */
function hvn_realty_infinite_scroll_render() {
	while ( have_posts() ) {
		the_post();

		if ( function_exists( 'hvn_realty_is_blog_view' ) && hvn_realty_is_blog_view() ) {
			$slug = function_exists( 'hvn_realty_get_blog_card_template' )
				? hvn_realty_get_blog_card_template()
				: 'grid';

			if ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
				hvn_realty_get_blog_template_part( 'content', $slug );
			} else {
				get_template_part( 'templates/blog/content', $slug );
			}
			continue;
		}

		if ( is_search() ) {
			get_template_part( 'template-parts/content', 'search' );
		} else {
			get_template_part( 'template-parts/content', get_post_type() );
		}
	}
}

/**
 * Return early if Infinite Scroll is not supported.
 */
function hvn_realty_infinite_scroll_early() {
	if ( ! class_exists( 'Jetpack' ) || ! Jetpack::is_module_active( 'infinite-scroll' ) ) {
		return;
	}

	if ( function_exists( 'hvn_realty_is_blog_view' ) && ! hvn_realty_is_blog_view() ) {
		return;
	}

	if ( wp_script_is( 'the-neverending-homepage', 'enqueued' ) ) {
		return;
	}

	wp_enqueue_script( 'the-neverending-homepage' );
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_infinite_scroll_early' );