<?php
/**
 * Homepage Featured Properties — settings, sanitizers, and query helpers.
 *
 * Additive API only. Existing theme_mod keys are never renamed.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed Featured Properties source modes.
 *
 * @return string[]
 */
function hvn_realty_get_home_featured_source_choices() {
	return array( 'all', 'featured', 'departments' );
}

/**
 * Sanitize Featured Properties source.
 *
 * @param mixed $input Raw value.
 * @return string all|featured|departments
 */
function hvn_realty_sanitize_home_featured_source( $input ) {
	$input = sanitize_key( (string) $input );
	$allowed = hvn_realty_get_home_featured_source_choices();

	return in_array( $input, $allowed, true ) ? $input : 'all';
}

/**
 * Featured Properties source mode (default: all).
 *
 * @return string all|featured|departments
 */
function hvn_realty_get_home_featured_source() {
	return hvn_realty_sanitize_home_featured_source(
		get_theme_mod( 'hvn_realty_home_featured_source', 'all' )
	);
}

/**
 * Featured Properties display count.
 *
 * @return int
 */
function hvn_realty_get_home_featured_count() {
	$count = absint( get_theme_mod( 'hvn_realty_home_featured_count', 6 ) );
	if ( function_exists( 'hvn_realty_sanitize_home_property_count' ) ) {
		return hvn_realty_sanitize_home_property_count( $count );
	}

	return max( 3, min( 24, $count ) );
}

/**
 * Allowed orderby values for Featured Properties.
 *
 * @return array<string, string> value => label
 */
function hvn_realty_get_home_featured_orderby_choices() {
	return array(
		'newest'     => __( 'Newest', 'havenlytics-realty' ),
		'oldest'     => __( 'Oldest', 'havenlytics-realty' ),
		'price'      => __( 'Price', 'havenlytics-realty' ),
		'rand'       => __( 'Random', 'havenlytics-realty' ),
		'title'      => __( 'Title', 'havenlytics-realty' ),
		'menu_order' => __( 'Menu Order', 'havenlytics-realty' ),
	);
}

/**
 * Sanitize orderby.
 *
 * @param mixed $input Raw value.
 * @return string
 */
function hvn_realty_sanitize_home_featured_orderby( $input ) {
	$input = sanitize_key( (string) $input );
	$allowed = array_keys( hvn_realty_get_home_featured_orderby_choices() );

	return in_array( $input, $allowed, true ) ? $input : 'newest';
}

/**
 * Sanitize ASC/DESC order.
 *
 * @param mixed $input Raw value.
 * @return string ASC|DESC
 */
function hvn_realty_sanitize_home_featured_order( $input ) {
	$input = strtoupper( sanitize_key( (string) $input ) );

	return in_array( $input, array( 'ASC', 'DESC' ), true ) ? $input : 'DESC';
}

/**
 * Sanitize JSON/CSV list of department term IDs.
 *
 * @param mixed $input Raw value.
 * @return string JSON array of validated term IDs.
 */
function hvn_realty_sanitize_home_featured_departments( $input ) {
	$ids = array();

	if ( is_string( $input ) ) {
		$decoded = json_decode( $input, true );
		if ( is_array( $decoded ) ) {
			$ids = $decoded;
		} elseif ( '' !== trim( $input ) ) {
			$ids = preg_split( '/[\s,]+/', $input );
		}
	} elseif ( is_array( $input ) ) {
		$ids = $input;
	}

	$valid = array();
	$taxonomy_exists = taxonomy_exists( 'hvnly_prop_depts' );

	foreach ( (array) $ids as $id ) {
		$id = absint( $id );
		if ( $id <= 0 ) {
			continue;
		}
		if ( $taxonomy_exists ) {
			$term = get_term( $id, 'hvnly_prop_depts' );
			if ( ! $term || is_wp_error( $term ) ) {
				continue;
			}
		}
		$valid[] = $id;
	}

	$valid = array_values( array_unique( $valid ) );

	return wp_json_encode( $valid );
}

/**
 * Selected department term IDs for Featured Properties.
 *
 * @return int[]
 */
function hvn_realty_get_home_featured_department_ids() {
	$raw = get_theme_mod( 'hvn_realty_home_featured_departments', '[]' );
	$sanitized = hvn_realty_sanitize_home_featured_departments( $raw );
	$decoded   = json_decode( $sanitized, true );

	if ( ! is_array( $decoded ) ) {
		return array();
	}

	return array_map( 'absint', $decoded );
}

/**
 * Sanitize tab source.
 *
 * @param mixed $input Raw value.
 * @return string selected|all
 */
function hvn_realty_sanitize_home_featured_tab_source( $input ) {
	$input = sanitize_key( (string) $input );

	return in_array( $input, array( 'selected', 'all' ), true ) ? $input : 'all';
}

/**
 * Sanitize active tab preference.
 *
 * @param mixed $input Raw value.
 * @return string all|first
 */
function hvn_realty_sanitize_home_featured_active_tab( $input ) {
	$input = sanitize_key( (string) $input );

	return in_array( $input, array( 'all', 'first' ), true ) ? $input : 'all';
}

/**
 * Sanitize column count within a range.
 *
 * @param mixed $input Raw value.
 * @param int   $min   Minimum.
 * @param int   $max   Maximum.
 * @param int   $default Default.
 * @return int
 */
function hvn_realty_sanitize_home_featured_columns( $input, $min = 1, $max = 4, $default = 3 ) {
	$input = absint( $input );
	if ( $input < $min || $input > $max ) {
		return (int) $default;
	}

	return $input;
}

/**
 * Desktop columns (1–4, default 3).
 *
 * @param mixed $input Raw.
 * @return int
 */
function hvn_realty_sanitize_home_featured_columns_desktop( $input ) {
	return hvn_realty_sanitize_home_featured_columns( $input, 1, 4, 3 );
}

/**
 * Tablet columns (1–3, default 2).
 *
 * @param mixed $input Raw.
 * @return int
 */
function hvn_realty_sanitize_home_featured_columns_tablet( $input ) {
	return hvn_realty_sanitize_home_featured_columns( $input, 1, 3, 2 );
}

/**
 * Mobile columns (1–2, default 1).
 *
 * @param mixed $input Raw.
 * @return int
 */
function hvn_realty_sanitize_home_featured_columns_mobile( $input ) {
	return hvn_realty_sanitize_home_featured_columns( $input, 1, 2, 1 );
}

/**
 * Cached department terms for Featured Properties / tabs.
 *
 * @param array<string, mixed> $args Optional get_terms overrides.
 * @return WP_Term[]
 */
function hvn_realty_get_home_featured_department_terms( $args = array() ) {
	static $cache = array();

	if ( ! taxonomy_exists( 'hvnly_prop_depts' ) ) {
		return array();
	}

	$defaults = array(
		'taxonomy'   => 'hvnly_prop_depts',
		'hide_empty' => true,
		'orderby'    => 'name',
		'order'      => 'ASC',
	);

	$query_args = wp_parse_args( $args, $defaults );
	$query_args['taxonomy'] = 'hvnly_prop_depts';

	$cache_key = md5( (string) wp_json_encode( $query_args ) );
	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$terms = get_terms( $query_args );
	$cache[ $cache_key ] = ( is_wp_error( $terms ) || ! is_array( $terms ) ) ? array() : $terms;

	return $cache[ $cache_key ];
}

/**
 * Department terms used for Featured Properties filter tabs.
 *
 * @return WP_Term[]
 */
function hvn_realty_get_home_featured_tab_terms() {
	$hide_empty = (bool) get_theme_mod( 'hvn_realty_home_featured_hide_empty_tabs', true );
	$tab_source = hvn_realty_sanitize_home_featured_tab_source(
		get_theme_mod( 'hvn_realty_home_featured_tab_source', 'all' )
	);

	$terms = hvn_realty_get_home_featured_department_terms(
		array(
			'hide_empty' => $hide_empty,
		)
	);

	if ( 'selected' !== $tab_source ) {
		return $terms;
	}

	$selected_ids = hvn_realty_get_home_featured_department_ids();
	if ( empty( $selected_ids ) ) {
		// Empty selection = all departments (matches query empty-selection behavior).
		return $terms;
	}

	$filtered = array();
	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term && in_array( (int) $term->term_id, $selected_ids, true ) ) {
			$filtered[] = $term;
		}
	}

	return $filtered;
}

/**
 * Resolve WP_Query orderby/order from Customizer settings.
 *
 * @return array{orderby: string|array<string,string>, order: string, meta_key?: string}
 */
function hvn_realty_get_home_featured_ordering_args() {
	$orderby = hvn_realty_sanitize_home_featured_orderby(
		get_theme_mod( 'hvn_realty_home_featured_orderby', 'newest' )
	);
	$order = hvn_realty_sanitize_home_featured_order(
		get_theme_mod( 'hvn_realty_home_featured_order', 'DESC' )
	);

	$args = array(
		'orderby' => 'date',
		'order'   => $order,
	);

	switch ( $orderby ) {
		case 'oldest':
			$args['orderby'] = 'date';
			$args['order']   = 'ASC';
			break;
		case 'newest':
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
		case 'title':
			$args['orderby'] = 'title';
			break;
		case 'menu_order':
			$args['orderby'] = 'menu_order';
			break;
		case 'rand':
			$args['orderby'] = 'rand';
			unset( $args['order'] );
			break;
		case 'price':
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = '_hvnly_property_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			break;
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
	}

	return $args;
}

/**
 * Build Featured Properties WP_Query args from Customizer settings.
 *
 * Single query path — no duplicate fallback queries except for featured-only
 * empty handling (featured mode does not fall back to all properties).
 *
 * @return array<string, mixed>
 */
function hvn_realty_get_home_featured_query_args() {
	$count    = hvn_realty_get_home_featured_count();
	$source   = hvn_realty_get_home_featured_source();
	$ordering = hvn_realty_get_home_featured_ordering_args();

	$args = array(
		'post_type'           => 'hvnly_property',
		'posts_per_page'      => $count,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => $ordering['orderby'],
		'order'               => isset( $ordering['order'] ) ? $ordering['order'] : 'DESC',
	);

	if ( ! empty( $ordering['meta_key'] ) ) {
		$args['meta_key'] = $ordering['meta_key']; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	}

	if ( 'featured' === $source ) {
		// Preserve both plugin featured meta keys (existing query shape).
		$featured_meta = array(
			'relation' => 'OR',
			array(
				'key'     => '_hvnly_property_featured',
				'value'   => '1',
				'compare' => '=',
			),
			array(
				'key'     => '_hvnly_property_action_tool_is_featured',
				'value'   => '1',
				'compare' => '=',
			),
		);

		if ( ! empty( $ordering['meta_key'] ) ) {
			// Combine featured filter with price meta for orderby=meta_value_num.
			$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'AND',
				$featured_meta,
				array(
					'key'     => '_hvnly_property_price',
					'compare' => 'EXISTS',
				),
			);
		} else {
			$args['meta_query'] = $featured_meta; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}
	}

	if ( 'departments' === $source ) {
		$term_ids = hvn_realty_get_home_featured_department_ids();
		if ( ! empty( $term_ids ) && taxonomy_exists( 'hvnly_prop_depts' ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'hvnly_prop_depts',
					'field'    => 'term_id',
					'terms'    => $term_ids,
					'operator' => 'IN',
				),
			);
		}
		// Empty selection = all properties (no tax_query).
	}

	/**
	 * Filter Featured Properties query args.
	 *
	 * @param array<string, mixed> $args Query args.
	 */
	return apply_filters( 'hvn_realty_home_featured_query_args', $args );
}

/**
 * Run the Featured Properties query (single WP_Query).
 *
 * @return WP_Query
 */
function hvn_realty_get_home_featured_query() {
	return new WP_Query( hvn_realty_get_home_featured_query_args() );
}

/**
 * Whether filter tabs should render.
 *
 * @return bool
 */
function hvn_realty_home_featured_tabs_enabled() {
	return (bool) get_theme_mod( 'hvn_realty_home_featured_enable_tabs', true );
}

/**
 * Active tab preference: all|first.
 *
 * @return string
 */
function hvn_realty_get_home_featured_active_tab() {
	return hvn_realty_sanitize_home_featured_active_tab(
		get_theme_mod( 'hvn_realty_home_featured_active_tab', 'all' )
	);
}

/**
 * Section description text.
 *
 * @return string
 */
function hvn_realty_get_home_featured_description() {
	return (string) get_theme_mod( 'hvn_realty_home_featured_description', '' );
}

/**
 * Whether the View All button should show.
 *
 * @return bool
 */
function hvn_realty_home_featured_show_view_all() {
	return (bool) get_theme_mod( 'hvn_realty_home_featured_show_view_all', true );
}

/**
 * View All button label.
 *
 * @param int $total Total listing count for default label.
 * @return string
 */
function hvn_realty_get_home_featured_view_all_text( $total = 0 ) {
	$custom = trim( (string) get_theme_mod( 'hvn_realty_home_featured_view_all_text', '' ) );
	if ( '' !== $custom ) {
		return $custom;
	}

	if ( $total > 0 ) {
		return sprintf(
			/* translators: %s: listing count. */
			__( 'View All %s Listings', 'havenlytics-realty' ),
			number_format_i18n( $total )
		);
	}

	return __( 'View All Listings', 'havenlytics-realty' );
}

/**
 * View All button URL.
 *
 * @return string
 */
function hvn_realty_get_home_featured_view_all_url() {
	$custom = trim( (string) get_theme_mod( 'hvn_realty_home_featured_view_all_url', '' ) );
	if ( '' !== $custom ) {
		return esc_url_raw( $custom );
	}

	if ( function_exists( 'hvn_realty_get_property_search_url' ) ) {
		return (string) hvn_realty_get_property_search_url();
	}

	return '';
}

/**
 * Grid column settings.
 *
 * @return array{desktop:int,tablet:int,mobile:int}
 */
function hvn_realty_get_home_featured_columns() {
	return array(
		'desktop' => hvn_realty_sanitize_home_featured_columns_desktop(
			get_theme_mod( 'hvn_realty_home_featured_columns_desktop', 3 )
		),
		'tablet'  => hvn_realty_sanitize_home_featured_columns_tablet(
			get_theme_mod( 'hvn_realty_home_featured_columns_tablet', 2 )
		),
		'mobile'  => hvn_realty_sanitize_home_featured_columns_mobile(
			get_theme_mod( 'hvn_realty_home_featured_columns_mobile', 1 )
		),
	);
}

/**
 * Inline CSS for Featured Properties columns (additive; keeps existing classes).
 *
 * @return string
 */
function hvn_realty_get_home_featured_columns_css() {
	$cols = hvn_realty_get_home_featured_columns();

	$css  = sprintf(
		'body.hvn-theme-home .hvn-realty-prop-grid{grid-template-columns:repeat(%d,1fr);}',
		(int) $cols['desktop']
	);
	$css .= sprintf(
		'@media(max-width:980px){body.hvn-theme-home .hvn-realty-prop-grid{grid-template-columns:repeat(%d,1fr);}}',
		(int) $cols['tablet']
	);
	$css .= sprintf(
		'@media(max-width:640px){body.hvn-theme-home .hvn-realty-prop-grid{grid-template-columns:repeat(%d,1fr);}}',
		(int) $cols['mobile']
	);

	return $css;
}

/**
 * Department slugs assigned to a property (all terms, not just the first).
 *
 * @param int $post_id Property ID.
 * @return string[]
 */
function hvn_realty_get_property_department_slugs( $post_id ) {
	$post_id = absint( $post_id );
	if ( $post_id <= 0 || ! taxonomy_exists( 'hvnly_prop_depts' ) ) {
		return array();
	}

	$terms = get_the_terms( $post_id, 'hvnly_prop_depts' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}

	$slugs = array();
	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term && '' !== $term->slug ) {
			$slugs[] = sanitize_title( $term->slug );
		}
	}

	return array_values( array_unique( $slugs ) );
}

/**
 * Customizer active_callback: show department multi-select.
 *
 * Visible when Property Source is Selected Departments, or when Tab Source
 * is Selected Departments (so tabs can be curated independently).
 *
 * @return bool
 */
function hvn_realty_customizer_featured_is_departments_source() {
	$source     = hvn_realty_get_home_featured_source();
	$tab_source = hvn_realty_sanitize_home_featured_tab_source(
		get_theme_mod( 'hvn_realty_home_featured_tab_source', 'all' )
	);

	if ( class_exists( 'WP_Customize_Manager' ) && isset( $GLOBALS['wp_customize'] ) && $GLOBALS['wp_customize'] instanceof WP_Customize_Manager ) {
		$manager        = $GLOBALS['wp_customize'];
		$source_setting = $manager->get_setting( 'hvn_realty_home_featured_source' );
		if ( $source_setting ) {
			$source = hvn_realty_sanitize_home_featured_source( $source_setting->value() );
		}
		$tab_setting = $manager->get_setting( 'hvn_realty_home_featured_tab_source' );
		if ( $tab_setting ) {
			$tab_source = hvn_realty_sanitize_home_featured_tab_source( $tab_setting->value() );
		}
	}

	return ( 'departments' === $source ) || ( 'selected' === $tab_source );
}

/**
 * Customizer active_callback: tabs enabled.
 *
 * @return bool
 */
function hvn_realty_customizer_featured_tabs_enabled() {
	if ( class_exists( 'WP_Customize_Manager' ) && isset( $GLOBALS['wp_customize'] ) && $GLOBALS['wp_customize'] instanceof WP_Customize_Manager ) {
		$setting = $GLOBALS['wp_customize']->get_setting( 'hvn_realty_home_featured_enable_tabs' );
		if ( $setting ) {
			return (bool) $setting->value();
		}
	}

	return hvn_realty_home_featured_tabs_enabled();
}

/**
 * Customizer active_callback: View All enabled.
 *
 * @return bool
 */
function hvn_realty_customizer_featured_view_all_enabled() {
	if ( class_exists( 'WP_Customize_Manager' ) && isset( $GLOBALS['wp_customize'] ) && $GLOBALS['wp_customize'] instanceof WP_Customize_Manager ) {
		$setting = $GLOBALS['wp_customize']->get_setting( 'hvn_realty_home_featured_show_view_all' );
		if ( $setting ) {
			return (bool) $setting->value();
		}
	}

	return hvn_realty_home_featured_show_view_all();
}
