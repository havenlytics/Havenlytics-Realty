<?php
/**
 * Homepage 2.0.0 renderer.
 *
 * Single source of truth for the rebuilt homepage section order. Each section
 * template part self-guards against missing plugin data, so the homepage
 * degrades gracefully when the Havenlytics plugin is inactive.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ordered homepage sections (slug => section template suffix).
 *
 * @return string[]
 */
function hvn_realty_get_home_sections() {
	$default = array_keys( hvn_realty_get_home_section_registry() );

	$stored = get_theme_mod( 'hvn_realty_home_section_order', '' );
	if ( is_string( $stored ) && '' !== $stored ) {
		$decoded = json_decode( $stored, true );
		if ( is_array( $decoded ) && ! empty( $decoded ) ) {
			$sections = array();
			foreach ( $decoded as $slug ) {
				$slug = sanitize_key( (string) $slug );
				if ( in_array( $slug, $default, true ) && ! in_array( $slug, $sections, true ) ) {
					$sections[] = $slug;
				}
			}
			foreach ( $default as $slug ) {
				if ( ! in_array( $slug, $sections, true ) ) {
					$sections[] = $slug;
				}
			}
			return apply_filters( 'hvn_realty_home_sections', $sections );
		}
	}

	$sections = $default;

	return apply_filters( 'hvn_realty_home_sections', $sections );
}

/**
 * Whether a homepage section is enabled (defaults to visible).
 *
 * @param string $section Section slug.
 * @return bool
 */
function hvn_realty_home_section_visible( $section ) {
	return (bool) get_theme_mod( 'hvn_realty_home_show_' . sanitize_key( $section ), true );
}

/**
 * Output all enabled homepage sections.
 *
 * @return void
 */
function hvn_realty_render_homepage_sections() {
	$sections = hvn_realty_get_home_sections();

	// Safety net: never allow an empty homepage. Fall back to the default order.
	if ( empty( $sections ) ) {
		$sections = array_keys( hvn_realty_get_home_section_registry() );
	}

	foreach ( $sections as $section ) {
		if ( ! hvn_realty_home_section_visible( $section ) ) {
			continue;
		}

		// Isolate each section: a fatal/parse error in one template part must
		// never stop the remaining sections from rendering. ParseError and
		// Error both implement Throwable in PHP 7+, so an include-time compile
		// error in a section template is caught here.
		try {
			get_template_part( 'template-parts/home/section', $section );
		} catch ( \Throwable $hvn_section_error ) {
			if ( function_exists( 'error_log' ) ) {
				error_log(
					sprintf(
						'Havenlytics Realty: homepage section "%s" failed to render: %s',
						$section,
						$hvn_section_error->getMessage()
					)
				);
			}
			continue;
		}
	}
}
