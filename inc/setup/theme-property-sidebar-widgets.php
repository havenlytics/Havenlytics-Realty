<?php
/**
 * Theme launch — Havenlytics single property sidebar widgets.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option: property sidebar widgets seeded flag. */
define( 'HVN_REALTY_PROPERTY_SIDEBAR_WIDGETS_OPTION', 'hvn_realty_default_widgets_inserted' );

/** Plugin-registered single property sidebar ID. */
define( 'HVN_REALTY_PROPERTY_SIDEBAR_ID', 'hvnly_single_property_sidebar_widgets_area' );

/**
 * Seed Havenlytics widgets into the single property sidebar after launch.
 *
 * @return void
 */
function hvn_realty_maybe_seed_property_sidebar_widgets() {
	if ( get_option( HVN_REALTY_PROPERTY_SIDEBAR_WIDGETS_OPTION, false ) ) {
		return;
	}

	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		return;
	}

	if ( ! get_option( 'hvnly_demo_properties_imported', false ) ) {
		return;
	}

	$active_ids = hvn_realty_property_sidebar_get_active_widget_ids();

	if ( ! hvn_realty_property_sidebar_can_auto_seed( $active_ids ) ) {
		update_option( HVN_REALTY_PROPERTY_SIDEBAR_WIDGETS_OPTION, true );
		return;
	}

	hvn_realty_launch_setup_property_sidebar_widgets( $active_ids );
	update_option( HVN_REALTY_PROPERTY_SIDEBAR_WIDGETS_OPTION, true );
}

/**
 * Active widget tokens assigned to the single property sidebar.
 *
 * @return array<int, string>
 */
function hvn_realty_property_sidebar_get_active_widget_ids() {
	$sidebars = get_option( 'sidebars_widgets', array() );

	if ( ! is_array( $sidebars ) || empty( $sidebars[ HVN_REALTY_PROPERTY_SIDEBAR_ID ] ) || ! is_array( $sidebars[ HVN_REALTY_PROPERTY_SIDEBAR_ID ] ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$sidebars[ HVN_REALTY_PROPERTY_SIDEBAR_ID ],
			static function ( $widget_id ) {
				return is_string( $widget_id ) && 'wp_inactive_widgets' !== $widget_id;
			}
		)
	);
}

/**
 * Whether the sidebar is empty or only contains a plugin auto-seeded Property Agent widget.
 *
 * @param array<int, string> $widget_ids Sidebar widget tokens.
 * @return bool
 */
function hvn_realty_property_sidebar_can_auto_seed( $widget_ids ) {
	if ( empty( $widget_ids ) ) {
		return true;
	}

	foreach ( $widget_ids as $widget_id ) {
		if ( ! is_string( $widget_id ) || ! preg_match( '/^hvnly_property_agent-\d+$/', $widget_id ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Find an existing sidebar widget token by id base.
 *
 * @param array<int, string> $widget_ids Sidebar widget tokens.
 * @param string             $id_base    Widget id base.
 * @return string
 */
function hvn_realty_property_sidebar_find_widget_id( $widget_ids, $id_base ) {
	foreach ( $widget_ids as $widget_id ) {
		if ( is_string( $widget_id ) && str_starts_with( $widget_id, $id_base . '-' ) ) {
			return $widget_id;
		}
	}

	return '';
}

/**
 * Default instance settings for a Havenlytics property sidebar widget.
 *
 * @param string $id_base Widget id base.
 * @return array<string, mixed>
 */
function hvn_realty_launch_get_havenlytics_widget_instance( $id_base ) {
	switch ( $id_base ) {
		case 'hvnly_property_agent':
			if ( class_exists( 'HvnlyNab\Agent\PropertyAgentWidgetRenderer' ) ) {
				return \HvnlyNab\Agent\PropertyAgentWidgetRenderer::get_sidebar_defaults();
			}

			return array(
				'title'         => __( 'Contact Agent', 'havenlytics-realty' ),
				'show_phone'    => '1',
				'show_email'    => '1',
				'show_whatsapp' => '1',
				'show_social'   => '1',
			);

		case 'hvnly_featured_properties':
			return array(
				'title'          => __( 'Featured Properties', 'havenlytics-realty' ),
				'number'         => 4,
				'show_price'     => '1',
				'show_bedrooms'  => '1',
				'show_bathrooms' => '1',
				'show_sqft'      => '1',
			);

		case 'hvnly_related_properties':
			return array(
				'title'          => __( 'Related Properties', 'havenlytics-realty' ),
				'number'         => 3,
				'relation_type'  => 'location_type',
				'show_price'     => '0',
				'show_bedrooms'  => '0',
				'show_bathrooms' => '0',
				'show_sqft'      => '0',
			);

		case 'hvnly_agent_listings_carousel':
			if ( class_exists( 'HvnlyNab\Agent\AgentListingsCarouselWidgetRenderer' ) ) {
				$instance = \HvnlyNab\Agent\AgentListingsCarouselWidgetRenderer::get_defaults();
			} else {
				$instance = array(
					'agent_id'      => 0,
					'title'         => '',
					'number'        => 6,
					'orderby'       => 'assigned',
					'show_price'    => '1',
					'show_location' => '1',
					'show_status'   => '1',
					'autoplay'      => '0',
					'show_nav'      => '1',
				);
			}

			if ( empty( $instance['title'] ) ) {
				$instance['title'] = __( 'Agent Listings', 'havenlytics-realty' );
			}

			return $instance;

		default:
			return array();
	}
}

/**
 * Insert Havenlytics widgets into the single property sidebar.
 *
 * @param array<int, string> $existing_ids Existing sidebar widget tokens.
 * @return void
 */
function hvn_realty_launch_setup_property_sidebar_widgets( $existing_ids = array() ) {
	if ( ! function_exists( 'hvn_realty_launch_insert_widget' ) || ! function_exists( 'hvn_realty_launch_assign_sidebar' ) ) {
		return;
	}

	$widget_bases = array(
		'hvnly_property_agent',
		'hvnly_featured_properties',
		'hvnly_related_properties',
		'hvnly_agent_listings_carousel',
	);

	$assigned = array();

	foreach ( $widget_bases as $id_base ) {
		$existing = hvn_realty_property_sidebar_find_widget_id( $existing_ids, $id_base );

		if ( '' !== $existing ) {
			$assigned[] = $existing;
			continue;
		}

		$instance = hvn_realty_launch_get_havenlytics_widget_instance( $id_base );

		if ( empty( $instance ) ) {
			continue;
		}

		$widget_id = hvn_realty_launch_insert_widget( $id_base, $instance );

		if ( '' !== $widget_id ) {
			$assigned[] = $widget_id;
		}
	}

	if ( empty( $assigned ) ) {
		return;
	}

	hvn_realty_launch_assign_sidebar( HVN_REALTY_PROPERTY_SIDEBAR_ID, $assigned );
}
