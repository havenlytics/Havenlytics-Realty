<?php
/**
 * Havenlytics breadcrumb trail enhancements.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Append a breadcrumb link item to output.
 *
 * @param string $output    Breadcrumb HTML reference.
 * @param int    $position  Position counter reference.
 * @param string $url       Link URL.
 * @param string $label     Link label.
 * @param string $separator Separator HTML.
 * @param array  $schema    Schema items reference.
 */
function hvn_realty_breadcrumb_add_link( &$output, &$position, $url, $label, $separator, &$schema ) {
	$output .= '<span class="hvn-breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
	$output .= '<a href="' . esc_url( $url ) . '" class="hvn-breadcrumb-link" itemprop="item">';
	$output .= '<span itemprop="name">' . esc_html( $label ) . '</span>';
	$output .= '</a>';
	$output .= '<meta itemprop="position" content="' . (int) $position . '" />';
	$output .= '</span>';
	$output .= $separator;

	$schema[] = array(
		'@type'    => 'ListItem',
		'position' => $position,
		'name'     => $label,
		'item'     => $url,
	);

	++$position;
}

/**
 * Build Havenlytics-specific breadcrumb trail (after Home).
 *
 * @param array $args     Breadcrumb args.
 * @param int   $position Current position.
 * @param array $schema   Schema items.
 * @return string|null Trail HTML or null to use default logic.
 */
function hvn_realty_breadcrumbs_plugin_integration( $args, &$position, &$schema ) {
	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return null;
	}

	$output = '';

	if ( is_singular( 'hvnly_property' ) ) {
		$grid_url = hvn_realty_get_plugin_page_url( 'property_grid' );
		if ( $grid_url ) {
			hvn_realty_breadcrumb_add_link(
				$output,
				$position,
				$grid_url,
				__( 'Listings', 'havenlytics-realty' ),
				$args['separator'],
				$schema
			);
		}

		if ( $args['show_current'] ) {
			$output .= $args['before_current'];
			$output .= '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
			$output .= $args['after_current'];
		}

		return $output;
	}

	if ( is_singular( 'hvnly_agent' ) ) {
		$agents_url = hvn_realty_get_plugin_page_url( 'property_agents' );
		if ( $agents_url ) {
			hvn_realty_breadcrumb_add_link(
				$output,
				$position,
				$agents_url,
				__( 'Agents', 'havenlytics-realty' ),
				$args['separator'],
				$schema
			);
		}

		if ( $args['show_current'] ) {
			$output .= $args['before_current'];
			$output .= '<span itemprop="name">' . esc_html( get_the_title() ) . '</span>';
			$output .= $args['after_current'];
		}

		return $output;
	}

	if ( hvn_realty_is_property_taxonomy_context() ) {
		$term     = get_queried_object();
		$grid_url = hvn_realty_get_plugin_page_url( 'property_grid' );
		if ( $grid_url ) {
			hvn_realty_breadcrumb_add_link(
				$output,
				$position,
				$grid_url,
				__( 'Listings', 'havenlytics-realty' ),
				$args['separator'],
				$schema
			);
		}

		if ( $term && $args['show_current'] ) {
			$output .= $args['before_current'];
			$output .= '<span itemprop="name">' . esc_html( $term->name ) . '</span>';
			$output .= $args['after_current'];
		}

		return $output;
	}

	if ( hvn_realty_is_agency_context() ) {
		$term         = get_queried_object();
		$agencies_url = hvn_realty_get_plugin_page_url( 'property_agencies' );
		if ( $agencies_url ) {
			hvn_realty_breadcrumb_add_link(
				$output,
				$position,
				$agencies_url,
				__( 'Agencies', 'havenlytics-realty' ),
				$args['separator'],
				$schema
			);
		}

		if ( $term && $args['show_current'] ) {
			$output .= $args['before_current'];
			$output .= '<span itemprop="name">' . esc_html( $term->name ) . '</span>';
			$output .= $args['after_current'];
		}

		return $output;
	}

	if ( is_post_type_archive( 'hvnly_property' ) ) {
		$output .= $args['before_current'];
		$output .= '<span itemprop="name">' . esc_html__( 'Properties', 'havenlytics-realty' ) . '</span>';
		$output .= $args['after_current'];
		return $output;
	}

	if ( is_post_type_archive( 'hvnly_agent' ) ) {
		$output .= $args['before_current'];
		$output .= '<span itemprop="name">' . esc_html__( 'Agents', 'havenlytics-realty' ) . '</span>';
		$output .= $args['after_current'];
		return $output;
	}

	return null;
}
