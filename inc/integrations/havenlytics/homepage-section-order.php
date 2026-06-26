<?php
/**
 * Homepage section order helpers.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default homepage section order (existing sites use this when no priorities are saved).
 *
 * @return string[]
 */
function hvn_realty_get_default_home_section_order() {
	return array(
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
}

/**
 * Sections available in the Section Manager control.
 *
 * @return string[]
 */
function hvn_realty_get_manageable_home_sections() {
	$sections = hvn_realty_get_default_home_section_order();

	if ( function_exists( 'hvn_realty_get_starter_customizer_config' ) ) {
		$config  = hvn_realty_get_starter_customizer_config();
		$allowed = $config['section_order_sections'] ?? array();

		if ( ! empty( $allowed ) ) {
			$sections = array_values( array_intersect( $sections, $allowed ) );
			foreach ( $allowed as $slug ) {
				if ( ! in_array( $slug, $sections, true ) ) {
					$sections[] = $slug;
				}
			}
		}
	}

	return apply_filters( 'hvn_realty_manageable_home_sections', $sections );
}

if ( ! function_exists( 'hvn_realty_get_home_section_labels' ) ) {
	/**
	 * Human-readable labels for manageable homepage sections.
	 *
	 * @return array<string, string>
	 */
	function hvn_realty_get_home_section_labels() {
		$labels = array(
			'hero-map'            => __( 'Hero', 'havenlytics-realty' ),
			'featured-properties' => __( 'Featured Properties', 'havenlytics-realty' ),
			'department-tabs'     => __( 'Departments', 'havenlytics-realty' ),
			'property-taxonomies' => __( 'Property Taxonomies', 'havenlytics-realty' ),
			'property-types'      => __( 'Property Types', 'havenlytics-realty' ),
			'featured-agents'     => __( 'Agents', 'havenlytics-realty' ),
			'featured-agencies'   => __( 'Agencies', 'havenlytics-realty' ),
			'testimonials'        => __( 'Testimonials', 'havenlytics-realty' ),
			'latest-posts'        => __( 'Blog', 'havenlytics-realty' ),
			'cta-banner'          => __( 'CTA', 'havenlytics-realty' ),
			'statistics'          => __( 'Statistics', 'havenlytics-realty' ),
			'footer_cta'          => __( 'Footer CTA', 'havenlytics-realty' ),
		);

		return apply_filters( 'hvn_realty_home_section_labels', $labels );
	}
}

/**
 * Default numeric priority for a section slug.
 *
 * @param string $slug Section slug.
 * @return int
 */
function hvn_realty_get_default_home_section_priority( $slug ) {
	$slug     = sanitize_key( $slug );
	$defaults = array(
		'hero-map'            => 10,
		'featured-properties' => 20,
		'department-tabs'     => 30,
		'property-taxonomies' => 40,
		'property-types'      => 50,
		'featured-agents'     => 60,
		'featured-agencies'   => 70,
		'latest-posts'        => 80,
		'testimonials'        => 90,
		'cta-banner'          => 100,
	);

	return isset( $defaults[ $slug ] ) ? (int) $defaults[ $slug ] : 999;
}

/**
 * Theme mod key for a section priority.
 *
 * @param string $slug Section slug.
 * @return string
 */
function hvn_realty_get_home_section_priority_mod_key( $slug ) {
	return 'hvn_realty_home_section_priority_' . str_replace( '-', '_', sanitize_key( $slug ) );
}

/**
 * Whether any custom section priority theme mods exist.
 *
 * @return bool
 */
function hvn_realty_has_custom_home_section_priorities() {
	foreach ( hvn_realty_get_manageable_home_sections() as $slug ) {
		if ( null !== get_theme_mod( hvn_realty_get_home_section_priority_mod_key( $slug ), null ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Persist section priorities to individual theme mods.
 *
 * @param string[] $order Ordered section slugs.
 * @return void
 */
function hvn_realty_persist_home_section_priority_mods( $order ) {
	if ( ! is_array( $order ) ) {
		return;
	}

	foreach ( array_values( $order ) as $index => $slug ) {
		$slug = sanitize_key( $slug );
		if ( '' === $slug ) {
			continue;
		}

		set_theme_mod(
			hvn_realty_get_home_section_priority_mod_key( $slug ),
			(int) ( ( $index + 1 ) * 10 )
		);
	}
}

/**
 * Normalize and validate a section order array.
 *
 * @param mixed $input Raw order.
 * @return string[]
 */
function hvn_realty_normalize_home_section_order( $input ) {
	$allowed  = hvn_realty_get_manageable_home_sections();
	$allowed  = array_flip( $allowed );
	$default  = hvn_realty_get_default_home_section_order();
	$order    = array();
	$raw_list = array();

	if ( is_string( $input ) && '' !== $input ) {
		$decoded = json_decode( wp_unslash( $input ), true );
		if ( is_array( $decoded ) ) {
			$raw_list = $decoded;
		}
	} elseif ( is_array( $input ) ) {
		$raw_list = $input;
	}

	foreach ( $raw_list as $slug ) {
		if ( ! is_string( $slug ) ) {
			continue;
		}

		$slug = sanitize_key( $slug );
		if ( '' === $slug || ! isset( $allowed[ $slug ] ) || in_array( $slug, $order, true ) ) {
			continue;
		}

		$order[] = $slug;
	}

	foreach ( $default as $slug ) {
		if ( ! in_array( $slug, $order, true ) ) {
			$order[] = $slug;
		}
	}

	return $order;
}

/**
 * Sanitize section order JSON for Customizer storage.
 *
 * @param mixed $input Raw value.
 * @return string JSON string.
 */
function hvn_realty_sanitize_home_section_order( $input ) {
	if ( '' === $input || null === $input ) {
		return '';
	}

	$order = hvn_realty_normalize_home_section_order( $input );

	return wp_json_encode( $order );
}

/**
 * Persist priority theme mods after Customizer save.
 *
 * @return void
 */
function hvn_realty_save_home_section_priority_mods() {
	$raw = get_theme_mod( 'hvn_realty_home_section_order', '' );

	if ( '' === $raw || null === $raw ) {
		return;
	}

	hvn_realty_persist_home_section_priority_mods( hvn_realty_normalize_home_section_order( $raw ) );
}
add_action( 'customize_save_after', 'hvn_realty_save_home_section_priority_mods' );

/**
 * Resolve homepage section order from saved priorities or defaults.
 *
 * @return string[]
 */
function hvn_realty_resolve_home_section_order() {
	$default    = hvn_realty_get_default_home_section_order();
	$manageable = hvn_realty_get_manageable_home_sections();

	if ( ! hvn_realty_has_custom_home_section_priorities() ) {
		$raw = get_theme_mod( 'hvn_realty_home_section_order', null );
		if ( null === $raw || '' === $raw ) {
			return $default;
		}

		$order = hvn_realty_normalize_home_section_order( $raw );
		if ( ! empty( $order ) ) {
			return $order;
		}

		return $default;
	}

	$priorities = array();

	foreach ( $manageable as $slug ) {
		$mod = get_theme_mod( hvn_realty_get_home_section_priority_mod_key( $slug ), null );
		if ( null === $mod ) {
			$priorities[ $slug ] = hvn_realty_get_default_home_section_priority( $slug );
		} else {
			$priorities[ $slug ] = absint( $mod );
		}
	}

	asort( $priorities, SORT_NUMERIC );

	return array_keys( $priorities );
}

/**
 * Order used by the Section Manager control UI.
 *
 * @return string[]
 */
function hvn_realty_get_home_section_order_for_control() {
	$raw = get_theme_mod( 'hvn_realty_home_section_order', null );

	if ( null !== $raw && '' !== $raw ) {
		return hvn_realty_normalize_home_section_order( $raw );
	}

	if ( hvn_realty_has_custom_home_section_priorities() ) {
		return hvn_realty_resolve_home_section_order();
	}

	return hvn_realty_get_default_home_section_order();
}
