<?php
/**
 * Homepage 3.0 scripts.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the interactive map section should load Leaflet assets.
 *
 * @return bool
 */
function hvn_realty_home_should_enqueue_map_assets() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) || ! hvn_realty_is_home_design() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_home_section_visible' ) ) {
		return hvn_realty_home_section_visible( 'map' );
	}

	return (bool) get_theme_mod( 'hvn_realty_home_show_map', true );
}

/**
 * Register/enqueue Leaflet from the Havenlytics plugin when available.
 *
 * @return string[] Script handles that were successfully prepared.
 */
function hvn_realty_home_enqueue_leaflet() {
	$handles = array();

	if ( ! defined( 'HVNLYNAB_ASSETS_URL' ) ) {
		return $handles;
	}

	$base = trailingslashit( HVNLYNAB_ASSETS_URL ) . 'frontend/lib/leaflet/';

	if ( ! wp_style_is( 'hvnly-frontend-property-leaflet', 'registered' ) ) {
		wp_register_style(
			'hvnly-frontend-property-leaflet',
			esc_url( $base . 'css/leaflet.css' ),
			array(),
			'1.9.4'
		);
	}
	if ( ! wp_script_is( 'hvnly-frontend-property-leaflet', 'registered' ) ) {
		wp_register_script(
			'hvnly-frontend-property-leaflet',
			esc_url( $base . 'js/leaflet.js' ),
			array(),
			'1.9.4',
			true
		);
	}

	wp_enqueue_style( 'hvnly-frontend-property-leaflet' );
	wp_enqueue_script( 'hvnly-frontend-property-leaflet' );
	$handles[] = 'hvnly-frontend-property-leaflet';

	if ( ! wp_style_is( 'hvnly-frontend-property-leaflet-markercluster', 'registered' ) ) {
		wp_register_style(
			'hvnly-frontend-property-leaflet-markercluster',
			esc_url( $base . 'css/MarkerCluster.css' ),
			array(),
			'1.5.3'
		);
	}
	if ( ! wp_style_is( 'hvnly-frontend-property-leaflet-markercluster-default', 'registered' ) ) {
		wp_register_style(
			'hvnly-frontend-property-leaflet-markercluster-default',
			esc_url( $base . 'css/MarkerCluster.Default.css' ),
			array( 'hvnly-frontend-property-leaflet-markercluster' ),
			'1.5.3'
		);
	}
	if ( ! wp_script_is( 'hvnly-frontend-property-leaflet-markercluster', 'registered' ) ) {
		wp_register_script(
			'hvnly-frontend-property-leaflet-markercluster',
			esc_url( $base . 'js/leaflet.markercluster.js' ),
			array( 'hvnly-frontend-property-leaflet' ),
			'1.5.3',
			true
		);
	}

	wp_enqueue_style( 'hvnly-frontend-property-leaflet-markercluster' );
	wp_enqueue_style( 'hvnly-frontend-property-leaflet-markercluster-default' );
	wp_enqueue_script( 'hvnly-frontend-property-leaflet-markercluster' );
	$handles[] = 'hvnly-frontend-property-leaflet-markercluster';

	return $handles;
}

/**
 * Enqueue the homepage interaction scripts (homepage only).
 *
 * @return void
 */
function hvn_realty_enqueue_homepage_scripts() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) ) {
		return;
	}

	if ( ! hvn_realty_is_home_design() && ! is_customize_preview() ) {
		return;
	}

	if ( ! hvn_realty_enqueue_theme_script( 'hvn-realty-home', 'assets/js/home.js' ) ) {
		return;
	}

	$hero_bg_mode = get_theme_mod( 'hvn_realty_home_hero_bg_mode', 'static' );
	if ( 'carousel' === $hero_bg_mode || is_customize_preview() ) {
		hvn_realty_enqueue_theme_script(
			'hvn-realty-home-hero',
			'assets/js/home-hero.js',
			array( 'hvn-realty-home' )
		);
	}

	if ( ! hvn_realty_home_should_enqueue_map_assets() ) {
		return;
	}

	$leaflet_handles = hvn_realty_home_enqueue_leaflet();
	if ( empty( $leaflet_handles ) ) {
		return;
	}

	$map_deps = array_merge( array( 'hvn-realty-home' ), $leaflet_handles );
	if ( ! hvn_realty_enqueue_theme_script( 'hvn-realty-home-map', 'assets/js/home-map.js', $map_deps ) ) {
		return;
	}

	$markers = function_exists( 'hvn_realty_get_home_map_markers_payload' )
		? hvn_realty_get_home_map_markers_payload()
		: array();

	wp_localize_script(
		'hvn-realty-home-map',
		'hvnRealtyHomeMap',
		array(
			'markers' => $markers,
			'i18n'    => array(
				'beds'             => __( 'bd', 'havenlytics-realty' ),
				'baths'            => __( 'ba', 'havenlytics-realty' ),
				'save'             => __( 'Save property', 'havenlytics-realty' ),
				'saveFavorite'     => __( 'Save property', 'havenlytics-realty' ),
				'removeFavorite'   => __( 'Remove property from favorites', 'havenlytics-realty' ),
				'view'             => __( 'View Property', 'havenlytics-realty' ),
				/* translators: %s: property title */
				'viewNamed'        => __( 'View property: %s', 'havenlytics-realty' ),
				'openPreview'      => __( 'Open property preview', 'havenlytics-realty' ),
				/* translators: %s: property title */
				'openPreviewNamed' => __( 'Open property preview: %s', 'havenlytics-realty' ),
				/* translators: %d: number of properties in cluster */
				'clusterLabel'     => __( 'Show %d properties in this area', 'havenlytics-realty' ),
				'closePreview'     => __( 'Close property preview', 'havenlytics-realty' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_homepage_scripts', 35 );

/**
 * Preload the homepage hero LCP background image when known.
 *
 * @return void
 */
function hvn_realty_preload_home_hero_image() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) || ! hvn_realty_is_home_design() ) {
		return;
	}

	$url = '';

	$mode = function_exists( 'hvn_realty_sanitize_home_hero_bg_mode' )
		? hvn_realty_sanitize_home_hero_bg_mode( get_theme_mod( 'hvn_realty_home_hero_bg_mode', 'static' ) )
		: 'static';

	if ( 'carousel' === $mode && function_exists( 'hvn_realty_get_home_hero_carousel_slides' ) ) {
		$count  = function_exists( 'hvn_realty_sanitize_home_hero_carousel_count' )
			? hvn_realty_sanitize_home_hero_carousel_count( get_theme_mod( 'hvn_realty_home_hero_carousel_count', 5 ) )
			: 5;
		$slides = hvn_realty_get_home_hero_carousel_slides( $count );
		if ( ! empty( $slides[0]['url'] ) ) {
			$url = (string) $slides[0]['url'];
		}
	}

	if ( '' === $url ) {
		$image_id = absint( get_theme_mod( 'hvn_realty_home_hero_image_a', 0 ) );
		if ( $image_id > 0 ) {
			$maybe = wp_get_attachment_image_url( $image_id, 'large' );
			if ( is_string( $maybe ) && '' !== $maybe ) {
				$url = $maybe;
			}
		}
	}

	if ( '' === $url ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n",
		esc_url( $url )
	);
}
add_action( 'wp_head', 'hvn_realty_preload_home_hero_image', 2 );
