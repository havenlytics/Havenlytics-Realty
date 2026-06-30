<?php
/**
 * Page heading (H1) resolution and rendering for theme templates.
 *
 * Plugin shortcode pages intentionally hide the WordPress page title in
 * content-page.php, while the plugin suppresses its own archive title during
 * shortcode rendering — leaving only breadcrumbs visible. This module restores
 * a single, context-aware H1 above the page content without changing layout.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve the active Property Department term for the current request.
 *
 * Checks URL filters first, then whether the current page slug matches a
 * department term (e.g. /rent/, /sale/, /commercial/).
 *
 * @return WP_Term|null
 */
function hvn_realty_get_active_department_term() {
	if ( ! taxonomy_exists( 'hvnly_prop_depts' ) ) {
		return null;
	}

	$slug = '';

	if ( function_exists( 'hvnly_get_current_filters' ) ) {
		$filters = hvnly_get_current_filters();
		if ( is_array( $filters ) && ! empty( $filters['department'] ) ) {
			$slug = sanitize_key( (string) $filters['department'] );
		}
	}

	if ( '' === $slug && isset( $_GET['department'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$slug = sanitize_key( wp_unslash( $_GET['department'] ) );
	}

	if ( '' === $slug && is_page() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$slug = sanitize_key( $post->post_name );
		}
	}

	if ( '' === $slug ) {
		return null;
	}

	$term = get_term_by( 'slug', $slug, 'hvnly_prop_depts' );

	return ( $term instanceof WP_Term && ! is_wp_error( $term ) ) ? $term : null;
}

/**
 * SEO-friendly heading text for a Property Department term.
 *
 * @param WP_Term $term Department term.
 * @return string
 */
function hvn_realty_get_department_page_heading( WP_Term $term ) {
	$map = array(
		'rent'       => __( 'Rent Properties', 'havenlytics-realty' ),
		'sale'       => __( 'Properties for Sale', 'havenlytics-realty' ),
		'buy'        => __( 'Properties for Sale', 'havenlytics-realty' ),
		'commercial' => __( 'Commercial Properties', 'havenlytics-realty' ),
		'let'        => __( 'Properties to Let', 'havenlytics-realty' ),
	);

	$slug = sanitize_key( $term->slug );

	if ( isset( $map[ $slug ] ) ) {
		$title = $map[ $slug ];
	} else {
		$title = sprintf(
			/* translators: %s: property department name */
			__( '%s Properties', 'havenlytics-realty' ),
			$term->name
		);
	}

	/**
	 * Filter the page heading for a Property Department.
	 *
	 * @param string  $title Heading text.
	 * @param WP_Term $term  Department term.
	 */
	return apply_filters( 'hvn_realty_department_page_heading', $title, $term );
}

/**
 * Map a Havenlytics plugin page key to a default heading label.
 *
 * @param string $page_key Plugin page key.
 * @return string Empty when unmapped.
 */
function hvn_realty_get_plugin_page_heading_label( $page_key ) {
	$labels = array(
		'property_search'   => __( 'Property Search', 'havenlytics-realty' ),
		'property_grid'     => __( 'Property Grid', 'havenlytics-realty' ),
		'property_lists'    => __( 'Property Listings', 'havenlytics-realty' ),
		'property_agents'   => __( 'Our Agents', 'havenlytics-realty' ),
		'property_agencies' => __( 'Our Agencies', 'havenlytics-realty' ),
	);

	$page_key = sanitize_key( (string) $page_key );

	/**
	 * Filter default plugin page heading labels.
	 *
	 * @param array<string, string> $labels Page key => heading.
	 */
	$labels = apply_filters( 'hvn_realty_plugin_page_heading_labels', $labels );

	return isset( $labels[ $page_key ] ) ? (string) $labels[ $page_key ] : '';
}

/**
 * Detect which registered Havenlytics plugin page is being viewed.
 *
 * @param int $post_id Optional post ID.
 * @return string Page key or empty string.
 */
function hvn_realty_get_current_plugin_page_key( $post_id = 0 ) {
	if ( ! function_exists( 'hvn_realty_get_plugin_page_map' ) ) {
		return '';
	}

	$post_id = $post_id > 0 ? $post_id : (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return '';
	}

	foreach ( hvn_realty_get_plugin_page_map() as $page_key => $option_key ) {
		if ( (int) get_option( $option_key, 0 ) === $post_id ) {
			return (string) $page_key;
		}
	}

	return '';
}

/**
 * Resolve the H1 text for a Havenlytics plugin shortcode page.
 *
 * @param int $post_id Optional post ID.
 * @return string
 */
function hvn_realty_get_plugin_shortcode_page_heading( $post_id = 0 ) {
	$department = hvn_realty_get_active_department_term();
	if ( $department instanceof WP_Term ) {
		return hvn_realty_get_department_page_heading( $department );
	}

	$page_key = hvn_realty_get_current_plugin_page_key( $post_id );
	if ( '' !== $page_key ) {
		$label = hvn_realty_get_plugin_page_heading_label( $page_key );
		if ( '' !== $label ) {
			return $label;
		}
	}

	$post_id = $post_id > 0 ? $post_id : (int) get_queried_object_id();
	if ( $post_id > 0 ) {
		$title = get_the_title( $post_id );
		if ( is_string( $title ) && '' !== $title ) {
			return $title;
		}
	}

	return '';
}

/**
 * Resolve the visible page heading for the current request.
 *
 * @return string Empty when no theme heading should be shown.
 */
function hvn_realty_get_page_heading_title() {
	if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {
		return '';
	}

	if ( function_exists( 'hvn_realty_is_realty_homepage' ) && hvn_realty_is_realty_homepage() ) {
		return '';
	}

	if ( is_page() && function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() ) {
		$title = hvn_realty_get_plugin_shortcode_page_heading();
	} elseif ( is_tax( 'hvnly_prop_depts' ) ) {
		$term = get_queried_object();
		$title = ( $term instanceof WP_Term ) ? hvn_realty_get_department_page_heading( $term ) : '';
	} elseif ( is_post_type_archive( 'hvnly_agent' ) ) {
		$title = __( 'Our Agents', 'havenlytics-realty' );
	} elseif ( is_post_type_archive( 'hvnly_property' ) ) {
		$title = __( 'Property Search', 'havenlytics-realty' );
	} elseif ( is_tax() && function_exists( 'hvn_realty_is_property_taxonomy_context' ) && hvn_realty_is_property_taxonomy_context() ) {
		$term = get_queried_object();
		$title = ( $term instanceof WP_Term ) ? $term->name : '';
	} else {
		return '';
	}

	$title = is_string( $title ) ? trim( $title ) : '';

	if ( '' === $title ) {
		return '';
	}

	/**
	 * Filter the resolved page heading title.
	 *
	 * @param string $title Heading text.
	 */
	return apply_filters( 'hvn_realty_page_heading_title', $title );
}

/**
 * Whether the theme should output its page heading partial.
 *
 * @return bool
 */
function hvn_realty_should_render_page_heading() {
	if ( is_admin() || is_front_page() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_elementor_page' ) && hvn_realty_is_elementor_page() ) {
		return false;
	}

	if ( is_singular( 'hvnly_property' ) || is_singular( 'hvnly_agent' ) ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_agency_context' ) && hvn_realty_is_agency_context() ) {
		return false;
	}

	if ( is_page() && function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() ) {
		return '' !== hvn_realty_get_page_heading_title();
	}

	return false;
}

/**
 * Output the page heading partial when appropriate.
 *
 * @return void
 */
function hvn_realty_render_page_heading() {
	if ( ! hvn_realty_should_render_page_heading() ) {
		return;
	}

	$title = hvn_realty_get_page_heading_title();
	if ( '' === $title ) {
		return;
	}

	set_query_var( 'hvn_realty_page_heading_title', $title );
	get_template_part( 'template-parts/layout/page-heading' );
}

/**
 * Improve native Havenlytics archive titles rendered by the plugin.
 *
 * @param string $title Plugin page title.
 * @return string
 */
function hvn_realty_filter_hvnly_page_title( $title ) {
	if ( is_tax( 'hvnly_prop_depts' ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			return hvn_realty_get_department_page_heading( $term );
		}
	}

	if ( is_post_type_archive( 'hvnly_agent' ) ) {
		return __( 'Our Agents', 'havenlytics-realty' );
	}

	if ( is_post_type_archive( 'hvnly_property' ) ) {
		return __( 'Property Search', 'havenlytics-realty' );
	}

	return $title;
}
add_filter( 'hvnly_page_title', 'hvn_realty_filter_hvnly_page_title' );
