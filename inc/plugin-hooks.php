<?php
/**
 * Havenlytics plugin integration hooks (architecture only).
 *
 * @package Havenlytics_Realty
 */

/**
 * Whether the Havenlytics companion plugin is active.
 *
 * Canonical detection helper — use this (or hvn_realty_is_havenlytics_plugin_active)
 * instead of scattering class_exists / function_exists checks.
 *
 * @return bool
 */
function hvn_realty_has_havenlytics() {
	return class_exists( 'HvnlyNab' ) || function_exists( 'HVNLY_NAB' );
}

/**
 * Whether the Havenlytics plugin is active.
 *
 * @return bool
 */
function hvn_realty_is_havenlytics_plugin_active() {
	return hvn_realty_has_havenlytics();
}

/**
 * Whether the theme is running without the Havenlytics plugin (standalone blog mode).
 *
 * @return bool
 */
function hvn_realty_is_standalone_blog_mode() {
	return ! hvn_realty_has_havenlytics();
}

/**
 * Whether the current request is a Havenlytics property view.
 *
 * @return bool
 */
function hvn_realty_is_property_context() {
	return post_type_exists( 'hvnly_property' ) && ( is_singular( 'hvnly_property' ) || is_post_type_archive( 'hvnly_property' ) );
}

/**
 * Resolve a Havenlytics plugin page ID by key (read-only; no plugin code changes).
 *
 * @param string $page_key Page key, e.g. property_search, property_grid.
 * @return int Page ID or 0.
 */
function hvn_realty_get_plugin_page_id( $page_key ) {
	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return 0;
	}

	if ( class_exists( '\HvnlyNab\Setup\PageInstaller' ) ) {
		return (int) \HvnlyNab\Setup\PageInstaller::get_page_id( $page_key );
	}

	$option_map = array(
		'property_search'   => 'hvnly_property_search_page_id',
		'property_grid'     => 'hvnly_property_grid_page_id',
		'property_lists'    => 'hvnly_property_list_page_id',
		'property_agents'   => 'hvnly_property_agents_page_id',
		'property_agencies' => 'hvnly_property_agencies_page_id',
	);

	$option_key = $option_map[ $page_key ] ?? '';
	if ( '' === $option_key ) {
		return 0;
	}

	$page_id = absint( get_option( $option_key, 0 ) );

	return ( $page_id > 0 && get_post( $page_id ) ) ? $page_id : 0;
}

/**
 * WordPress.org plugin install URL (or plugins screen when already active).
 *
 * @return string
 */
if ( ! function_exists( 'hvn_realty_get_plugin_install_url' ) ) {
	function hvn_realty_get_plugin_install_url() {
		if ( hvn_realty_is_havenlytics_plugin_active() ) {
			return admin_url( 'plugins.php' );
		}

		return admin_url( 'plugin-install.php?s=havenlytics&tab=search&type=term' );
	}
}

/**
 * Register integration hooks when plugin is present.
 */
function hvn_realty_register_plugin_hooks() {
	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return;
	}

	/**
	 * Fires when Havenlytics plugin is active and theme hooks are ready.
	 */
	do_action( 'hvn_realty_havenlytics_ready' );
}
add_action( 'after_setup_theme', 'hvn_realty_register_plugin_hooks', 20 );

/**
 * Filter property archive layout classes.
 *
 * @param array $classes CSS classes.
 * @return array
 */
function hvn_realty_property_archive_classes( $classes ) {
	if ( function_exists( 'hvn_realty_is_property_view' ) && hvn_realty_is_property_view() ) {
		$classes[] = 'hvn-property-view';
		$classes   = apply_filters( 'hvn_realty_property_layout_classes', $classes );
	}
	return $classes;
}
add_filter( 'hvn_realty_body_classes', 'hvn_realty_property_archive_classes' );

/**
 * Enqueue slot for plugin-provided property styles.
 */
function hvn_realty_property_styles_hook() {
	if ( ! function_exists( 'hvn_realty_is_property_view' ) || ! hvn_realty_is_property_view() ) {
		return;
	}

	/**
	 * Fires before theme outputs property-specific inline styles.
	 * Plugin may enqueue its own stylesheet here.
	 */
	do_action( 'hvn_realty_enqueue_property_styles' );
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_property_styles_hook', 5 );

/**
 * Search integration hook for Havenlytics property search.
 *
 * @param WP_Query $query Main query.
 */
function hvn_realty_search_integration( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	/**
	 * Allow Havenlytics plugin to modify property search queries.
	 *
	 * @param WP_Query $query Main query.
	 */
	do_action( 'hvn_realty_search_query', $query );
}
add_action( 'pre_get_posts', 'hvn_realty_search_integration', 20 );

/**
 * Template hook before property archive content.
 */
function hvn_realty_property_archive_before() {
	if ( ! post_type_exists( 'hvnly_property' ) || ! is_post_type_archive( 'hvnly_property' ) ) {
		return;
	}
	do_action( 'hvn_realty_before_property_archive' );
}

/**
 * Template hook after property single content.
 */
function hvn_realty_property_single_after() {
	if ( ! post_type_exists( 'hvnly_property' ) || ! is_singular( 'hvnly_property' ) ) {
		return;
	}
	do_action( 'hvn_realty_after_property_single' );
}
