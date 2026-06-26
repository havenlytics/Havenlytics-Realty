<?php

/**

 * Blog stylesheet loader — isolated from theme.css / homepage.css.

 *

 * @package Havenlytics_Realty

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



/**

 * Whether blog styles should load on the current request.

 *

 * @return bool

 */

function hvn_realty_should_enqueue_blog_assets() {

	if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {

		return false;

	}



	if ( function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() ) {

		return false;

	}



	return hvn_realty_is_blog_view();

}



/**

 * Enqueue shared blog styles used on archive and single views.

 *

 * @param array $extra_handles Optional extra stylesheet handles to enqueue after base.

 * @return void

 */

function hvn_realty_enqueue_blog_shared_styles( $extra_handles = array() ) {

	$deps = array( 'hvn-realty-layouts' );



	if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {

		hvn_realty_enqueue_theme_style( 'hvn-realty-blog-base', 'assets/css/blog/blog-base.css', $deps );

		hvn_realty_enqueue_theme_style( 'hvn-realty-blog-pagination', 'assets/css/blog/blog-pagination.css', array( 'hvn-realty-blog-base' ) );

		hvn_realty_enqueue_theme_style( 'hvn-realty-blog-sidebar', 'assets/css/blog/blog-sidebar.css', array( 'hvn-realty-blog-base' ) );



		foreach ( $extra_handles as $handle => $path ) {

			hvn_realty_enqueue_theme_style( $handle, $path, array( 'hvn-realty-blog-base' ) );

		}

	} else {

		wp_enqueue_style( 'hvn-realty-blog-base', HVN_REALTY_TEMPLATE_URL . '/assets/css/blog/blog-base.css', $deps, HVN_REALTY_VERSION );

		wp_enqueue_style( 'hvn-realty-blog-pagination', HVN_REALTY_TEMPLATE_URL . '/assets/css/blog/blog-pagination.css', array( 'hvn-realty-blog-base' ), HVN_REALTY_VERSION );

		wp_enqueue_style( 'hvn-realty-blog-sidebar', HVN_REALTY_TEMPLATE_URL . '/assets/css/blog/blog-sidebar.css', array( 'hvn-realty-blog-base' ), HVN_REALTY_VERSION );



		foreach ( $extra_handles as $handle => $path ) {

			wp_enqueue_style( $handle, HVN_REALTY_TEMPLATE_URL . '/' . $path, array( 'hvn-realty-blog-base' ), HVN_REALTY_VERSION );

		}

	}

}



/**

 * Enqueue blog module stylesheets.

 *

 * @return void

 */

function hvn_realty_enqueue_blog_assets() {

	if ( ! hvn_realty_should_enqueue_blog_assets() ) {

		return;

	}



	$layout = function_exists( 'hvn_realty_get_blog_layout' ) ? hvn_realty_get_blog_layout() : 'grid';

	$extra  = array();



	if ( 'list' === $layout ) {

		$extra['hvn-realty-blog-list'] = 'assets/css/blog/blog-list.css';

	} else {

		$extra['hvn-realty-blog-grid'] = 'assets/css/blog/blog-grid.css';

	}



	hvn_realty_enqueue_blog_shared_styles( $extra );

}



add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_blog_assets', 20 );



/**

 * Enqueue single-post styles from the blog module.

 *

 * @return void

 */

function hvn_realty_enqueue_blog_single_assets() {

	if ( ! is_single( 'post' ) ) {

		return;

	}



	if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {

		hvn_realty_enqueue_theme_style( 'hvn-realty-blog-single', 'assets/css/blog/blog-single.css', array( 'hvn-realty-layouts' ) );

		hvn_realty_enqueue_theme_style( 'hvn-realty-blog-pagination', 'assets/css/blog/blog-pagination.css', array( 'hvn-realty-blog-single' ) );

		hvn_realty_enqueue_theme_style( 'hvn-realty-blog-sidebar', 'assets/css/blog/blog-sidebar.css', array( 'hvn-realty-blog-single' ) );

	} else {

		wp_enqueue_style( 'hvn-realty-blog-single', HVN_REALTY_TEMPLATE_URL . '/assets/css/blog/blog-single.css', array( 'hvn-realty-layouts' ), HVN_REALTY_VERSION );

		wp_enqueue_style( 'hvn-realty-blog-pagination', HVN_REALTY_TEMPLATE_URL . '/assets/css/blog/blog-pagination.css', array( 'hvn-realty-blog-single' ), HVN_REALTY_VERSION );

		wp_enqueue_style( 'hvn-realty-blog-sidebar', HVN_REALTY_TEMPLATE_URL . '/assets/css/blog/blog-sidebar.css', array( 'hvn-realty-blog-single' ), HVN_REALTY_VERSION );

	}

}



add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_blog_single_assets', 20 );



/**

 * Sidebar-aware column count for grid inline CSS.

 *

 * @param int $cols Saved column count.

 * @return int

 */

function hvn_realty_get_blog_grid_inline_columns( $cols ) {

	$cols = max( 1, min( 4, (int) $cols ) );



	if ( function_exists( 'hvn_realty_sidebar_layout_enabled' ) && hvn_realty_sidebar_layout_enabled() ) {

		return min( 2, $cols );

	}



	return $cols;

}



/**

 * Output dynamic column rules for the saved Customizer grid (grid layout only).

 *

 * @return void

 */

function hvn_realty_output_blog_grid_inline_css() {

	if ( ! hvn_realty_should_enqueue_blog_assets() ) {

		return;

	}



	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {

		return;

	}



	$handle = wp_style_is( 'hvn-realty-blog-grid', 'enqueued' ) ? 'hvn-realty-blog-grid' : 'hvn-realty-blog-base';



	if ( ! wp_style_is( $handle, 'enqueued' ) ) {

		return;

	}



	$cols        = hvn_realty_get_blog_column_count();

	$tablet      = ( $cols > 2 ) ? 2 : $cols;

	$sidebar_cols = hvn_realty_get_blog_grid_inline_columns( $cols );



	$css = '

.hvn-layout-blog .hvn-blog-grid {

	--hvn-blog-columns: ' . (int) $cols . ';

}

@media (max-width: 991px) {

	.hvn-layout-blog .hvn-blog-grid {

		--hvn-blog-columns: ' . (int) $tablet . ';

	}

}

@media (min-width: 992px) {

	.hvn-layout-blog.hvn-has-sidebar .hvn-blog-grid,

	body.hvn-theme-has-sidebar .hvn-layout-blog.hvn-has-sidebar .hvn-blog-grid {

		--hvn-blog-columns: ' . (int) $sidebar_cols . ';

	}

}';



	wp_add_inline_style( $handle, $css );

}



add_action( 'wp_enqueue_scripts', 'hvn_realty_output_blog_grid_inline_css', 25 );


