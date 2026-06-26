<?php
/**
 * Homepage Property Types section helpers.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Property type terms for the homepage grid.
 *
 * @param int $limit Max terms (0 = Customizer count).
 * @return WP_Term[]
 */
function hvn_realty_get_home_property_type_terms( $limit = 0 ) {
	if ( ! taxonomy_exists( 'hvnly_prop_types' ) ) {
		return array();
	}

	if ( $limit <= 0 ) {
		$limit = hvn_realty_get_home_property_types_count();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'hvnly_prop_types',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => absint( $limit ),
		)
	);

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Number of property types to display.
 *
 * @return int
 */
function hvn_realty_get_home_property_types_count() {
	$count = absint( get_theme_mod( 'hvn_realty_home_property_types_count', 8 ) );

	return max( 4, min( 24, $count ) );
}

/**
 * Grid column count for property type cards.
 *
 * @return int
 */
function hvn_realty_get_home_property_types_columns() {
	$columns = absint( get_theme_mod( 'hvn_realty_home_property_types_columns', 4 ) );

	return max( 2, min( 4, $columns ) );
}

/**
 * Whether property type cards show listing counts.
 *
 * @return bool
 */
function hvn_realty_show_home_property_type_counts() {
	return (bool) get_theme_mod( 'hvn_realty_home_show_property_type_counts', true );
}

/**
 * Whether property type cards show icons or images when available.
 *
 * @return bool
 */
function hvn_realty_show_home_property_type_icons() {
	return (bool) get_theme_mod( 'hvn_realty_home_show_property_type_icons', true );
}

/**
 * Resolve card media for a property type term.
 *
 * @param WP_Term $term Term object.
 * @return array{type: string, url?: string, class?: string}
 */
function hvn_realty_get_home_property_type_card_media( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return array( 'type' => 'none' );
	}

	if ( hvn_realty_show_home_property_type_icons() && function_exists( 'hvn_realty_get_term_image_url' ) ) {
		$image_url = hvn_realty_get_term_image_url( $term->term_id, 'medium' );
		if ( $image_url ) {
			return array(
				'type' => 'image',
				'url'  => $image_url,
			);
		}
	}

	if ( hvn_realty_show_home_property_type_icons() && function_exists( 'hvn_realty_get_term_icon_class' ) ) {
		$icon_class = hvn_realty_get_term_icon_class( $term->term_id );
		if ( $icon_class ) {
			return array(
				'type'  => 'icon',
				'class' => $icon_class,
			);
		}
	}

	if ( hvn_realty_show_home_property_type_icons() ) {
		return array(
			'type'  => 'icon',
			'class' => 'fas fa-building',
		);
	}

	return array( 'type' => 'none' );
}
