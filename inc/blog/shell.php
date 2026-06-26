<?php
/**
 * Blog archive shell class string (#primary wrapper).
 *
 * @return string
 */
function hvn_realty_get_blog_shell_classes() {
	$classes = array(
		'hvn-theme-layout',
		'hvn-layout-blog',
	);

	$layout = function_exists( 'hvn_realty_get_blog_layout' ) ? hvn_realty_get_blog_layout() : 'grid';
	$classes[] = 'list' === $layout ? 'hvn-blog-layout-list' : 'hvn-blog-layout-grid';

	if ( function_exists( 'hvn_realty_get_layout_sidebar_classes' ) ) {
		$sidebar_classes = trim( hvn_realty_get_layout_sidebar_classes() );
		if ( '' !== $sidebar_classes ) {
			$classes[] = $sidebar_classes;
		}
	}

	return implode( ' ', array_filter( $classes ) );
}

/**
 * Whether the blog archive should use the sidebar column layout.
 *
 * @return bool
 */
function hvn_realty_blog_uses_sidebar_layout() {
	if ( function_exists( 'hvn_realty_sidebar_layout_enabled' ) ) {
		return hvn_realty_sidebar_layout_enabled();
	}

	if ( ! function_exists( 'hvn_realty_get_sidebar_position' ) ) {
		return false;
	}

	return 'none' !== hvn_realty_get_sidebar_position();
}
