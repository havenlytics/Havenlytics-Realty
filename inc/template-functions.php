<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Havenlytics_Realty
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function hvn_realty_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	$sidebar_position = hvn_realty_get_sidebar_position();

	if ( 'none' === $sidebar_position ) {
		$classes[] = 'hvn-theme-no-sidebar';
		$classes[] = 'hvn-theme-sidebar-none';
	} else {
		$classes[] = 'hvn-theme-has-sidebar';
		if ( 'left' === $sidebar_position ) {
			$classes[] = 'hvn-theme-sidebar-left';
		} elseif ( 'right' === $sidebar_position ) {
			$classes[] = 'hvn-theme-sidebar-right';
		}
	}

	if ( has_custom_logo() ) {
		$classes[] = 'hvn-theme-has-custom-logo';
	}

	$classes[] = 'mouse-user';

	if ( is_singular() ) {
		$classes[] = 'hvn-theme-singular';
	}

	if ( is_page() || is_404() ) {
		$classes[] = 'hvn-view-page';
	}

	if ( ( is_home() || is_search() || ( is_archive() && ! ( function_exists( 'hvn_realty_is_property_context' ) && hvn_realty_is_property_context() ) && ! ( function_exists( 'hvn_realty_is_havenlytics_view' ) && hvn_realty_is_havenlytics_view() ) ) ) || ( function_exists( 'hvn_realty_is_blog_view' ) && hvn_realty_is_blog_view() && is_front_page() ) ) {
		$classes[] = 'hvn-view-blog';
		$classes[] = 'hvn-posts-cols-' . hvn_realty_get_blog_column_count();
		if ( 'list' === hvn_realty_get_blog_layout() ) {
			$classes[] = 'hvn-blog-view-list';
		} else {
			$classes[] = 'hvn-blog-view-grid';
		}
	}

	if ( is_single() && ! ( post_type_exists( 'hvnly_property' ) && is_singular( 'hvnly_property' ) ) && ! ( post_type_exists( 'hvnly_agent' ) && is_singular( 'hvnly_agent' ) ) ) {
		$classes[] = 'hvn-view-single';
	}

	if ( is_archive() ) {
		$classes[] = 'hvn-theme-archive';
	}

	if ( is_search() ) {
		$classes[] = 'hvn-theme-search';
	}

	if ( hvn_realty_is_sticky_header() ) {
		$classes[] = 'hvn-theme-sticky-header';
	}

	$classes[] = 'hvn-header-layout-' . hvn_realty_get_header_layout();
	$classes[] = 'hvn-footer-cols-' . hvn_realty_get_footer_columns();
	$classes[] = 'hvn-container-' . hvn_realty_get_container_mode();

	if ( ! function_exists( 'hvn_realty_show_header_search' ) || ! hvn_realty_show_header_search() ) {
		$classes[] = 'hvn-header-no-search';
	}

	if ( ! function_exists( 'hvn_realty_show_header_cta' ) || ! hvn_realty_show_header_cta() ) {
		$classes[] = 'hvn-header-no-cta';
	}

	if ( hvn_realty_show_back_to_top() ) {
		$classes[] = 'hvn-has-back-to-top';
	}

	if ( function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) && hvn_realty_is_havenlytics_plugin_active() ) {
		$classes[] = 'hvn-havenlytics-active';
	}

	if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {
		$classes[] = 'hvn-realty-homepage';
	}

	if ( function_exists( 'hvn_realty_is_home_design' ) && hvn_realty_is_home_design() ) {
		$classes[] = 'hvn-theme-home';
	}

	return apply_filters( 'hvn_realty_body_classes', $classes );
}
add_filter( 'body_class', 'hvn_realty_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function hvn_realty_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'hvn_realty_pingback_header' );

/**
 * Filter the excerpt length.
 *
 * @param int $length Excerpt length.
 * @return int
 */
function hvn_realty_excerpt_length( $length ) {
	if ( is_admin() ) {
		return $length;
	}
	return 25;
}
add_filter( 'excerpt_length', 'hvn_realty_excerpt_length' );

/**
 * Filter the excerpt "read more" string.
 *
 * @param string $more The "read more" excerpt string.
 * @return string
 */
function hvn_realty_excerpt_more( $more ) {
	if ( is_admin() ) {
		return $more;
	}
	return '...';
}
add_filter( 'excerpt_more', 'hvn_realty_excerpt_more' );

/**
 * Add custom classes to post container.
 *
 * @param array $classes Post classes.
 * @return array
 */
function hvn_realty_post_classes( $classes ) {
	if ( ! is_singular() && has_post_thumbnail() ) {
		$classes[] = 'hvn-theme-has-thumbnail';
	}

	if ( is_sticky() && is_home() && ! is_paged() ) {
		$classes[] = 'hvn-theme-sticky';
	}

	return $classes;
}
add_filter( 'post_class', 'hvn_realty_post_classes' );

/**
 * Add custom classes to archive title.
 *
 * @param string $title Archive title.
 * @return string
 */
function hvn_realty_archive_title( $title ) {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = '<span class="vcard">' . get_the_author() . '</span>';
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'hvn_realty_archive_title' );

/**
 * Remove archive description prefix.
 *
 * @param string $description Archive description.
 * @return string
 */
function hvn_realty_archive_description( $description ) {
	if ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
	}
	return $description;
}
add_filter( 'get_the_archive_description', 'hvn_realty_archive_description' );

/**
 * Add custom link attributes for skip link.
 */
function hvn_realty_skip_link_focus_fix() {
	?>
	<script>
	/(trident|msie)/i.test(navigator.userAgent) && document.getElementById && window.addEventListener && window.addEventListener("hashchange", function() {
		var t, e = location.hash.substring(1);
		/^[A-z0-9_-]+$/.test(e) && (t = document.getElementById(e)) && (/^(?:a|select|input|button|textarea)$/i.test(t.tagName) || (t.tabIndex = -1), t.focus())
	}, !1);
	</script>
	<?php
}
add_action( 'wp_print_footer_scripts', 'hvn_realty_skip_link_focus_fix' );

/**
 * Add schema markup to header.
 */
function hvn_realty_schema_markup() {
	if ( is_single() || is_page() ) {
		echo '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '">';
		echo '<meta property="og:type" content="article">';
		echo '<meta property="og:url" content="' . esc_url( get_permalink() ) . '">';

		if ( has_post_thumbnail() ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
			if ( ! empty( $image[0] ) ) {
				echo '<meta property="og:image" content="' . esc_url( $image[0] ) . '">';
			}
		}

		$excerpt = get_the_excerpt();
		if ( ! empty( $excerpt ) ) {
			echo '<meta property="og:description" content="' . esc_attr( wp_trim_words( $excerpt, 30 ) ) . '">';
		}
		echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
	}
}
add_action( 'wp_head', 'hvn_realty_schema_markup' );

/**
 * Whether the theme preloader should display.
 *
 * @return bool
 */
function hvn_realty_show_preloader() {
	return (bool) get_theme_mod( 'hvn_realty_show_preloader', true );
}

