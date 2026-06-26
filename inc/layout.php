<?php
/**
 * Layout engine — Astra-style page, blog, and single shells.
 *
 * @package Havenlytics_Realty
 */

/**
 * URL for the header property search link.
 *
 * Uses the Havenlytics Property Search page when the plugin is active,
 * otherwise falls back to the standard WordPress search URL.
 *
 * @return string
 */
function hvn_realty_get_search_url() {
	$url = home_url( '/?s=' );

	if ( function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) && hvn_realty_is_havenlytics_plugin_active() && function_exists( 'hvn_realty_get_plugin_page_id' ) ) {
		$search_id = hvn_realty_get_plugin_page_id( 'property_search' );
		if ( $search_id > 0 ) {
			$permalink = get_permalink( $search_id );
			if ( is_string( $permalink ) && $permalink !== '' ) {
				$url = $permalink;
			}
		}
	}

	return apply_filters( 'hvn_realty_search_url', $url );
}

/**
 * Hero search display mode from Customizer.
 *
 * @return string header|hero|both
 */
function hvn_realty_get_hero_search_display() {
	$mode = get_theme_mod( 'hvn_realty_home_hero_search_display', 'header' );

	return in_array( $mode, array( 'header', 'hero', 'both' ), true ) ? $mode : 'header';
}

/**
 * Whether the header property search icon/panel should render.
 *
 * @return bool
 */
function hvn_realty_show_header_property_search() {
	if ( ! function_exists( 'hvn_realty_show_header_search' ) || ! hvn_realty_show_header_search() ) {
		return false;
	}

	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		return true;
	}

	return in_array( hvn_realty_get_hero_search_display(), array( 'header', 'both' ), true );
}

/**
 * Whether the hero map should show an inline property search card.
 *
 * @return bool
 */
function hvn_realty_show_hero_search_panel() {
	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		return false;
	}

	if ( ! in_array( hvn_realty_get_hero_search_display(), array( 'hero', 'both' ), true ) ) {
		return false;
	}

	$url = function_exists( 'hvn_realty_get_property_search_url' ) ? hvn_realty_get_property_search_url() : hvn_realty_get_search_url();

	return is_string( $url ) && '' !== $url && home_url( '/' ) !== untrailingslashit( $url );
}

/**
 * Hero search panel horizontal alignment slug.
 *
 * @return string left|center|right
 */
function hvn_realty_get_hero_search_horizontal() {
	$horizontal = get_theme_mod( 'hvn_realty_hero_search_horizontal', 'left' );

	return in_array( $horizontal, array( 'left', 'center', 'right' ), true ) ? $horizontal : 'left';
}

/**
 * Hero search panel vertical alignment slug.
 *
 * @return string top|center|bottom
 */
function hvn_realty_get_hero_search_vertical() {
	$vertical = get_theme_mod( 'hvn_realty_hero_search_vertical', 'center' );

	return in_array( $vertical, array( 'top', 'center', 'bottom' ), true ) ? $vertical : 'center';
}

/**
 * Hero search panel horizontal offset in pixels (0–100).
 *
 * @return int
 */
function hvn_realty_get_hero_search_offset_x() {
	$offset = absint( get_theme_mod( 'hvn_realty_hero_search_offset_x', 0 ) );

	return min( 100, $offset );
}

/**
 * Hero search panel vertical offset in pixels (0–100).
 *
 * @return int
 */
function hvn_realty_get_hero_search_offset_y() {
	$offset = absint( get_theme_mod( 'hvn_realty_hero_search_offset_y', 0 ) );

	return min( 100, $offset );
}

/**
 * Hero search panel width in pixels (400–700, desktop).
 *
 * @return int
 */
function hvn_realty_get_hero_search_width() {
	$width = absint( get_theme_mod( 'hvn_realty_hero_search_width', 400 ) );

	if ( $width < 400 ) {
		return 400;
	}

	if ( $width > 700 ) {
		return 700;
	}

	return $width;
}

/**
 * Map hero search alignment slug to flexbox keyword.
 *
 * @param string $axis       horizontal|vertical.
 * @param string $alignment  Alignment slug.
 * @return string
 */
function hvn_realty_get_hero_search_flex_value( $axis, $alignment ) {
	if ( 'horizontal' === $axis ) {
		$map = array(
			'left'   => 'flex-start',
			'center' => 'center',
			'right'  => 'flex-end',
		);
	} else {
		$map = array(
			'top'    => 'flex-start',
			'center' => 'center',
			'bottom' => 'flex-end',
		);
	}

	return $map[ $alignment ] ?? ( 'horizontal' === $axis ? 'flex-start' : 'center' );
}

/**
 * Build hero search position CSS variables from Customizer.
 *
 * @return string
 */
function hvn_realty_get_hero_search_position_css() {
	$justify = hvn_realty_get_hero_search_flex_value( 'horizontal', hvn_realty_get_hero_search_horizontal() );
	$align   = hvn_realty_get_hero_search_flex_value( 'vertical', hvn_realty_get_hero_search_vertical() );
	$offset_x = hvn_realty_get_hero_search_offset_x();
	$offset_y = hvn_realty_get_hero_search_offset_y();
	$width    = hvn_realty_get_hero_search_width();

	return '.hvn-realty-section--hero-has-search{'
		. '--hvn-realty-hero-search-justify:' . $justify . ';'
		. '--hvn-realty-hero-search-align:' . $align . ';'
		. '--hvn-realty-hero-search-offset-x:' . $offset_x . 'px;'
		. '--hvn-realty-hero-search-offset-y:' . $offset_y . 'px;'
		. '--hvn-realty-hero-search-width:' . $width . 'px;'
		. '}';
}

/**
 * Hero search panel title text.
 *
 * @return string
 */
function hvn_realty_get_hero_search_title() {
	$default = __( 'Find your next property', 'havenlytics-realty' );
	$title   = get_theme_mod( 'hvn_realty_hero_search_title', $default );

	return is_string( $title ) && '' !== $title ? $title : $default;
}

/**
 * Hero search panel optional subtitle.
 *
 * @return string
 */
function hvn_realty_get_hero_search_subtitle() {
	$subtitle = get_theme_mod( 'hvn_realty_hero_search_subtitle', '' );

	return is_string( $subtitle ) ? $subtitle : '';
}

/**
 * Hero search submit button label.
 *
 * @return string
 */
function hvn_realty_get_hero_search_button_text() {
	$default = __( 'Search properties', 'havenlytics-realty' );
	$text    = get_theme_mod( 'hvn_realty_hero_search_button_text', $default );

	return is_string( $text ) && '' !== $text ? $text : $default;
}

/**
 * Hero search department tabs label.
 *
 * @return string
 */
function hvn_realty_get_hero_search_tabs_label() {
	$default = __( 'Browse by Department', 'havenlytics-realty' );
	$label   = get_theme_mod( 'hvn_realty_hero_search_tabs_label', $default );

	return is_string( $label ) && '' !== $label ? $label : $default;
}

/**
 * Whether hero search department tabs should render.
 *
 * @return bool
 */
function hvn_realty_show_hero_department_tabs() {
	if ( ! get_theme_mod( 'hvn_realty_show_hero_department_tabs', true ) ) {
		return false;
	}

	if ( ! function_exists( 'hvn_realty_get_property_departments' ) ) {
		return false;
	}

	return ! empty( hvn_realty_get_property_departments() );
}

/**
 *
 * @return bool
 */
function hvn_realty_use_header_property_search_panel() {
	if ( ! function_exists( 'hvn_realty_show_header_property_search' ) || ! hvn_realty_show_header_property_search() ) {
		return false;
	}

	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		return false;
	}

	$url = function_exists( 'hvn_realty_get_property_search_url' ) ? hvn_realty_get_property_search_url() : hvn_realty_get_search_url();

	return is_string( $url ) && $url !== '' && home_url( '/' ) !== untrailingslashit( $url );
}

/**
 * Taxonomy terms for header property search selects.
 *
 * @param string $taxonomy Taxonomy slug.
 * @return WP_Term[]
 */
function hvn_realty_get_header_search_terms( $taxonomy ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => 100,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Bedroom/bathroom option list for header property search.
 *
 * @return array<string, string> Value => label.
 */
function hvn_realty_get_search_count_select_options() {
	$options = array(
		'' => __( 'Any', 'havenlytics-realty' ),
	);

	for ( $i = 1; $i <= 5; $i++ ) {
		$options[ (string) $i ] = (string) $i;
	}

	$options['6'] = '6+';

	return $options;
}

/**
 * Whether a sidebar position is configured (independent of widget assignment).
 *
 * @return bool
 */
function hvn_realty_sidebar_layout_enabled() {
	return 'none' !== hvn_realty_get_sidebar_position();
}

/**
 * Whether the optional sidebar module should render for the current view.
 *
 * @return bool
 */
function hvn_realty_has_sidebar() {
	if ( ! hvn_realty_sidebar_layout_enabled() ) {
		return false;
	}

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		return true;
	}

	// Allow layout preview in Customizer before widgets are assigned.
	return is_customize_preview();
}

/**
 * Sidebar position modifier classes for layout wrappers.
 *
 * @return string
 */
function hvn_realty_get_layout_sidebar_classes() {
	if ( ! hvn_realty_sidebar_layout_enabled() ) {
		return '';
	}

	return 'hvn-has-sidebar hvn-sidebar-' . sanitize_html_class( hvn_realty_get_sidebar_position() );
}

/**
 * Render the optional sidebar module (landmark markup lives in sidebar.php).
 */
function hvn_realty_render_sidebar() {
	if ( ! hvn_realty_has_sidebar() ) {
		return;
	}

	get_sidebar();
}
