<?php
/**
 * Real estate homepage renderer.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ordered homepage sections.
 *
 * @return string[]
 */
function hvn_realty_get_home_sections() {
	$sections = function_exists( 'hvn_realty_resolve_home_section_order' )
		? hvn_realty_resolve_home_section_order()
		: array(
			'hero-map',
			'featured-properties',
			'department-tabs',
			'property-taxonomies',
			'property-types',
			'featured-agents',
			'featured-agencies',
			'latest-posts',
			'testimonials',
			'cta-banner',
		);

	return apply_filters( 'hvn_realty_home_sections', $sections );
}

/**
 * Required integration helpers per homepage section.
 *
 * @param string $section Section slug.
 * @return string[]
 */
function hvn_realty_get_home_section_requirements( $section ) {
	$requirements = array(
		'hero-map'             => array( 'hvn_realty_render_home_hero_map' ),
		'featured-properties'  => array( 'hvn_realty_render_similar_property_carousel' ),
		'department-tabs'      => array( 'hvn_realty_get_property_departments', 'hvn_realty_render_shortcode' ),
		'property-taxonomies'  => array( 'hvn_realty_get_home_taxonomy_terms', 'hvn_realty_home_section_heading' ),
		'property-types'     => array( 'hvn_realty_get_home_property_type_terms', 'hvn_realty_home_section_heading' ),
		'property-locations'   => array( 'hvn_realty_get_home_taxonomy_terms', 'hvn_realty_home_section_heading' ),
		'property-categories'  => array( 'hvn_realty_get_home_taxonomy_terms', 'hvn_realty_home_section_heading' ),
		'featured-agents'      => array( 'hvn_realty_render_shortcode', 'hvn_realty_home_section_heading' ),
		'featured-agencies'    => array( 'hvn_realty_render_shortcode', 'hvn_realty_home_section_heading' ),
		'latest-posts'         => array( 'hvn_realty_get_home_section_title' ),
		'testimonials'         => array( 'hvn_realty_get_home_testimonials', 'hvn_realty_home_section_heading' ),
		'cta-banner'           => array( 'hvn_realty_get_home_cta_headline' ),
		'statistics'           => array( 'hvn_realty_get_property_count' ),
		'footer-cta'           => array( 'hvn_realty_get_home_footer_cta_text' ),
	);

	return apply_filters( 'hvn_realty_home_section_requirements', $requirements[ $section ] ?? array(), $section );
}

/**
 * Whether a homepage section can render without fatal errors.
 *
 * @param string $section Section slug.
 * @return bool
 */
function hvn_realty_home_section_can_render( $section ) {
	$section = sanitize_key( $section );
	if ( '' === $section ) {
		return false;
	}

	$template = 'template-parts/home/' . $section;
	if ( function_exists( 'hvn_realty_safe_get_template_part' ) ) {
		$located = locate_template( array( $template . '.php' ), false, false );
		if ( ! $located ) {
			return false;
		}
	}

	foreach ( hvn_realty_get_home_section_requirements( $section ) as $callback ) {
		if ( ! is_string( $callback ) || '' === $callback || ! function_exists( $callback ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Output all enabled homepage sections.
 *
 * @return void
 */
function hvn_realty_render_homepage_sections() {
	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		if ( function_exists( 'hvn_realty_safe_get_template_part' ) ) {
			hvn_realty_safe_get_template_part( 'template-parts/home/plugin-inactive' );
		} else {
			get_template_part( 'template-parts/home/plugin-inactive' );
		}
		return;
	}

	if ( ! function_exists( 'hvn_realty_get_home_sections' ) ) {
		return;
	}

	$allowed_sections = array_flip( hvn_realty_get_home_sections() );

	foreach ( hvn_realty_get_home_sections() as $section ) {
		if ( ! isset( $allowed_sections[ $section ] ) ) {
			continue;
		}

		if ( function_exists( 'hvn_realty_is_home_section_enabled' ) && ! hvn_realty_is_home_section_enabled( $section ) ) {
			continue;
		}

		if ( ! hvn_realty_home_section_can_render( $section ) ) {
			continue;
		}

		$template_slug = 'template-parts/home/' . $section;

		if ( function_exists( 'hvn_realty_safe_get_template_part' ) ) {
			hvn_realty_safe_get_template_part( $template_slug );
		} else {
			get_template_part( $template_slug );
		}
	}
}
