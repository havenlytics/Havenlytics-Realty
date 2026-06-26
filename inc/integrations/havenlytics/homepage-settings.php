<?php
/**
 * Real estate homepage Customizer getters and data helpers.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a theme_mod with legacy key fallback (no database writes).
 *
 * @param string       $key      Primary setting ID.
 * @param string|array $legacy   Legacy setting ID(s).
 * @param mixed        $default  Default when unset.
 * @return mixed
 */
function hvn_realty_get_theme_mod_with_legacy( $key, $legacy, $default ) {
	$value = get_theme_mod( $key, null );

	if ( null !== $value && '' !== $value ) {
		return $value;
	}

	foreach ( (array) $legacy as $legacy_key ) {
		$legacy_value = get_theme_mod( $legacy_key, null );
		if ( null !== $legacy_value && '' !== $legacy_value ) {
			return $legacy_value;
		}
	}

	return $default;
}

/**
 * Configurable Havenlytics taxonomy sources for the homepage section.
 *
 * @return array<string, array<string, mixed>>
 */
function hvn_realty_get_home_taxonomy_sources() {
	$sources = array(
		'locations' => array(
			'taxonomy'      => 'hvnly_prop_locations',
			'label'         => __( 'Locations', 'havenlytics-realty' ),
			'media'         => 'image',
			'icon_fallback' => 'fas fa-map-marker-alt',
			'link_label'    => __( 'View location', 'havenlytics-realty' ),
		),
		'types'     => array(
			'taxonomy'      => 'hvnly_prop_types',
			'label'         => __( 'Property Types', 'havenlytics-realty' ),
			'media'         => 'icon',
			'icon_fallback' => 'fas fa-building',
			'link_label'    => __( 'View type', 'havenlytics-realty' ),
		),
		'features'  => array(
			'taxonomy'      => 'hvnly_prop_features',
			'label'         => __( 'Features', 'havenlytics-realty' ),
			'media'         => 'image',
			'icon_fallback' => 'fas fa-check-circle',
			'link_label'    => __( 'View feature', 'havenlytics-realty' ),
		),
		'status'    => array(
			'taxonomy'      => 'hvnly_prop_status',
			'label'         => __( 'Status', 'havenlytics-realty' ),
			'media'         => 'title',
			'icon_fallback' => 'fas fa-info-circle',
			'link_label'    => __( 'View status', 'havenlytics-realty' ),
		),
		'badges'    => array(
			'taxonomy'      => 'hvnly_prop_badges',
			'label'         => __( 'Badges', 'havenlytics-realty' ),
			'media'         => 'icon',
			'icon_fallback' => 'fas fa-certificate',
			'link_label'    => __( 'View badge', 'havenlytics-realty' ),
		),
		'tags'      => array(
			'taxonomy'      => 'hvnly_prop_tags',
			'label'         => __( 'Tags', 'havenlytics-realty' ),
			'media'         => 'title',
			'icon_fallback' => 'fas fa-tag',
			'link_label'    => __( 'View tag', 'havenlytics-realty' ),
		),
	);

	return apply_filters( 'hvn_realty_home_taxonomy_sources', $sources );
}

/**
 * Active taxonomy source key from Customizer.
 *
 * @return string
 */
function hvn_realty_get_home_taxonomy_source() {
	$source = (string) hvn_realty_get_theme_mod_with_legacy(
		'hvn_realty_home_taxonomies_source',
		'hvn_realty_home_locations_source',
		'locations'
	);

	$sources = hvn_realty_get_home_taxonomy_sources();

	if ( ! isset( $sources[ $source ] ) ) {
		return 'locations';
	}

	$taxonomy = $sources[ $source ]['taxonomy'] ?? '';

	return ( is_string( $taxonomy ) && taxonomy_exists( $taxonomy ) ) ? $source : 'locations';
}

/**
 * Section title with legacy Property Locations fallback.
 *
 * @return string
 */
function hvn_realty_get_home_taxonomies_title() {
	$default = __( 'Property Locations', 'havenlytics-realty' );

	return (string) hvn_realty_get_theme_mod_with_legacy(
		'hvn_realty_home_taxonomies_title',
		array( 'hvn_realty_home_locations_title' ),
		$default
	);
}

/**
 * Section subtitle with legacy fallback.
 *
 * @return string
 */
function hvn_realty_get_home_taxonomies_subtitle() {
	$default = __( 'Explore listings by city and region.', 'havenlytics-realty' );

	return (string) hvn_realty_get_theme_mod_with_legacy(
		'hvn_realty_home_taxonomies_subtitle',
		array( 'hvn_realty_home_locations_subtitle' ),
		$default
	);
}

/**
 * Number of taxonomy terms to display.
 *
 * @return int
 */
function hvn_realty_get_home_taxonomies_count() {
	$count = absint(
		hvn_realty_get_theme_mod_with_legacy(
			'hvn_realty_home_taxonomies_count',
			array( 'hvn_realty_home_locations_count' ),
			8
		)
	);

	return max( 4, min( 24, $count ) );
}

/**
 * Grid column count for taxonomy cards.
 *
 * @return int
 */
function hvn_realty_get_home_taxonomies_columns() {
	$columns = absint( get_theme_mod( 'hvn_realty_home_taxonomies_columns', 4 ) );

	return max( 2, min( 6, $columns ) );
}

/**
 * Whether taxonomy cards show listing counts.
 *
 * @return bool
 */
function hvn_realty_show_home_taxonomy_counts() {
	return (bool) get_theme_mod( 'hvn_realty_home_show_taxonomy_counts', true );
}

/**
 * Whether taxonomy cards show icons when available.
 *
 * @return bool
 */
function hvn_realty_show_home_taxonomy_icons() {
	return (bool) get_theme_mod( 'hvn_realty_home_show_taxonomy_icons', true );
}

/**
 * Whether taxonomy cards show images when available.
 *
 * @return bool
 */
function hvn_realty_show_home_taxonomy_images() {
	return (bool) get_theme_mod( 'hvn_realty_home_show_taxonomy_images', true );
}

/**
 * Font Awesome icon class from Havenlytics term meta.
 *
 * @param int $term_id Term ID.
 * @return string
 */
function hvn_realty_get_term_icon_class( $term_id ) {
	$term_id = absint( $term_id );
	if ( $term_id <= 0 ) {
		return '';
	}

	$data = get_term_meta( $term_id, '_hvnly_advanced_icon_data', true );
	if ( ! is_array( $data ) || empty( $data['class'] ) ) {
		return '';
	}

	return is_string( $data['class'] ) && '' !== trim( $data['class'] ) ? trim( (string) $data['class'] ) : '';
}

/**
 * Resolve card media for a taxonomy term.
 *
 * @param WP_Term $term        Term object.
 * @param string  $source_key  Source key.
 * @return array{type: string, url?: string, class?: string}
 */
function hvn_realty_get_home_taxonomy_card_media( $term, $source_key ) {
	if ( ! $term instanceof WP_Term ) {
		return array( 'type' => 'none' );
	}

	$sources = hvn_realty_get_home_taxonomy_sources();
	$profile = $sources[ $source_key ] ?? array();
	$prefer  = $profile['media'] ?? 'title';
	$fallback = $profile['icon_fallback'] ?? 'fas fa-tag';

	if ( hvn_realty_show_home_taxonomy_images() && in_array( $prefer, array( 'image', 'icon' ), true ) ) {
		$image_url = hvn_realty_get_term_image_url( $term->term_id, 'medium_large' );
		if ( $image_url ) {
			return array(
				'type' => 'image',
				'url'  => $image_url,
			);
		}
	}

	if ( hvn_realty_show_home_taxonomy_icons() ) {
		$icon_class = hvn_realty_get_term_icon_class( $term->term_id );
		if ( $icon_class ) {
			return array(
				'type'  => 'icon',
				'class' => $icon_class,
			);
		}
	}

	if ( in_array( $prefer, array( 'image', 'icon' ), true ) ) {
		return array(
			'type'  => 'icon',
			'class' => $fallback,
		);
	}

	return array( 'type' => 'none' );
}

/**
 * Terms for the active homepage taxonomy source.
 *
 * @param int         $limit       Max terms (0 = use Customizer count).
 * @param string|null $source_key  Optional source override.
 * @return WP_Term[]
 */
function hvn_realty_get_home_taxonomy_terms( $limit = 0, $source_key = null ) {
	$source_key = is_string( $source_key ) && '' !== $source_key ? $source_key : hvn_realty_get_home_taxonomy_source();
	$sources    = hvn_realty_get_home_taxonomy_sources();

	if ( ! isset( $sources[ $source_key ] ) ) {
		return array();
	}

	$taxonomy = $sources[ $source_key ]['taxonomy'] ?? '';
	if ( ! is_string( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}

	if ( $limit <= 0 ) {
		$limit = hvn_realty_get_home_taxonomies_count();
	}

	$args = array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => absint( $limit ),
	);

	$terms = get_terms( $args );

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Property location terms for the homepage grid.
 *
 * @param int $limit Max terms to return (0 = all).
 * @return WP_Term[]
 */
function hvn_realty_get_property_locations( $limit = 0 ) {
	return hvn_realty_get_home_taxonomy_terms( $limit, 'locations' );
}

/**
 * Term image URL from Havenlytics advanced term image meta.
 *
 * @param int    $term_id Term ID.
 * @param string $size    Image size.
 * @return string
 */
function hvn_realty_get_term_image_url( $term_id, $size = 'medium_large' ) {
	$term_id = absint( $term_id );
	if ( $term_id <= 0 ) {
		return '';
	}

	$data = get_term_meta( $term_id, '_hvnly_term_advanced_image_data', true );
	if ( ! is_array( $data ) || empty( $data['id'] ) ) {
		return '';
	}

	$url = wp_get_attachment_image_url( absint( $data['id'] ), $size );

	return is_string( $url ) ? $url : '';
}

/**
 * Homepage section title from Customizer.
 *
 * @param string $section Section key (featured, department, locations, agents, agencies).
 * @param string $default Default title.
 * @return string
 */
function hvn_realty_get_home_section_title( $section, $default ) {
	$key = 'hvn_realty_home_' . sanitize_key( $section ) . '_title';

	return (string) get_theme_mod( $key, $default );
}

/**
 * Homepage section subtitle from Customizer.
 *
 * @param string $section Section key.
 * @param string $default Default subtitle.
 * @return string
 */
function hvn_realty_get_home_section_subtitle( $section, $default ) {
	$key = 'hvn_realty_home_' . sanitize_key( $section ) . '_subtitle';

	return (string) get_theme_mod( $key, $default );
}

/**
 * Department section footer button label.
 *
 * @return string
 */
function hvn_realty_get_home_department_button_text() {
	return (string) get_theme_mod(
		'hvn_realty_home_department_button_text',
		__( 'View All Properties', 'havenlytics-realty' )
	);
}

/**
 * Department section footer button URL.
 *
 * @return string
 */
function hvn_realty_get_home_department_button_url() {
	$fallback = function_exists( 'hvn_realty_get_property_search_url' )
		? hvn_realty_get_property_search_url()
		: home_url( '/' );

	return hvn_realty_resolve_theme_link(
		get_theme_mod( 'hvn_realty_home_department_button_url', '' ),
		$fallback
	);
}

/**
 * CTA banner subtext.
 *
 * @return string
 */
function hvn_realty_get_home_cta_subtext() {
	$text = get_theme_mod(
		'hvn_realty_home_cta_subtext',
		__( 'Browse listings or connect with an agent today.', 'havenlytics-realty' )
	);

	return apply_filters( 'hvn_realty_home_cta_subtext', $text );
}

/**
 * CTA background attachment ID.
 *
 * @return int
 */
function hvn_realty_get_home_cta_bg_image_id() {
	return absint( get_theme_mod( 'hvn_realty_home_cta_bg_image', 0 ) );
}

/**
 * CTA background overlay opacity (0–90).
 *
 * @return int
 */
function hvn_realty_get_home_cta_overlay_opacity() {
	$value = absint( get_theme_mod( 'hvn_realty_home_cta_overlay', 65 ) );

	return min( 90, max( 0, $value ) );
}

/**
 * Hero map height in viewport units.
 *
 * @return int
 */
function hvn_realty_get_home_hero_height() {
	$height = absint( get_theme_mod( 'hvn_realty_home_hero_height', 70 ) );

	return max( 40, min( 100, $height ) );
}

/**
 * Hero map height on mobile (vh).
 *
 * @return int
 */
function hvn_realty_get_home_hero_height_mobile() {
	$height = absint( get_theme_mod( 'hvn_realty_home_hero_height_mobile', 50 ) );

	return max( 40, min( 100, $height ) );
}

/**
 * Global homepage carousel settings for JS.
 *
 * @return array<string, mixed>
 */
function hvn_realty_get_home_carousel_settings() {
	$settings = array(
		'gap'              => absint( get_theme_mod( 'hvn_realty_home_carousel_gap', 16 ) ),
		'autoplay'         => (bool) get_theme_mod( 'hvn_realty_home_carousel_autoplay', true ),
		'autoplaySpeed'    => absint( get_theme_mod( 'hvn_realty_home_carousel_speed', 5000 ) ),
		'cardAutoplay'     => (bool) get_theme_mod( 'hvn_realty_home_carousel_card_autoplay', false ),
		'featuredDesktop'  => absint( get_theme_mod( 'hvn_realty_home_carousel_featured_desktop', 3 ) ),
		'featuredTablet'   => absint( get_theme_mod( 'hvn_realty_home_carousel_featured_tablet', 2 ) ),
		'featuredMobile'   => absint( get_theme_mod( 'hvn_realty_home_carousel_featured_mobile', 1 ) ),
		'cardsDesktop'     => absint( get_theme_mod( 'hvn_realty_home_carousel_cards_desktop', 4 ) ),
		'cardsTablet'      => absint( get_theme_mod( 'hvn_realty_home_carousel_cards_tablet', 2 ) ),
		'cardsMobile'      => absint( get_theme_mod( 'hvn_realty_home_carousel_cards_mobile', 1 ) ),
		'blogDesktop'      => absint( get_theme_mod( 'hvn_realty_home_carousel_blog_desktop', 3 ) ),
		'blogTablet'       => absint( get_theme_mod( 'hvn_realty_home_carousel_blog_tablet', 2 ) ),
		'blogMobile'       => absint( get_theme_mod( 'hvn_realty_home_carousel_blog_mobile', 1 ) ),
		'blogAutoplay'     => (bool) get_theme_mod( 'hvn_realty_home_blog_autoplay', true ),
	);

	$settings['gap']           = max( 8, min( 48, $settings['gap'] ) );
	$settings['autoplaySpeed'] = max( 2000, min( 15000, $settings['autoplaySpeed'] ) );

	foreach ( array( 'featuredDesktop', 'featuredTablet', 'featuredMobile', 'cardsDesktop', 'cardsTablet', 'cardsMobile', 'blogDesktop', 'blogTablet', 'blogMobile' ) as $key ) {
		$settings[ $key ] = max( 1, min( 6, $settings[ $key ] ) );
	}

	return apply_filters( 'hvn_realty_home_carousel_settings', $settings );
}

/**
 * Build inline CTA background style attribute.
 *
 * @return string Empty or ` style="..."`.
 */
function hvn_realty_get_home_cta_background_attr() {
	$image_id = hvn_realty_get_home_cta_bg_image_id();
	$overlay  = hvn_realty_get_home_cta_overlay_opacity();

	if ( $image_id <= 0 ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $image_id, 'full' );
	if ( ! $url ) {
		return '';
	}

	$style = sprintf(
		'--hvn-realty-cta-bg-image:url(%s);--hvn-realty-cta-overlay:%s%%;',
		esc_url( $url ),
		$overlay
	);

	return ' style="' . esc_attr( $style ) . '"';
}

/**
 * Department slugs to show on the homepage hero map (empty = all).
 *
 * @return string[]
 */
function hvn_realty_get_home_map_department_slugs() {
	if ( 'all' === get_theme_mod( 'hvn_realty_home_map_department_mode', 'all' ) ) {
		return array();
	}

	$slugs = array();

	if ( ! function_exists( 'hvn_realty_get_property_departments' ) ) {
		return $slugs;
	}

	foreach ( hvn_realty_get_property_departments() as $term ) {
		$setting_id = 'hvn_realty_home_map_dept_' . $term->slug;
		if ( (bool) get_theme_mod( $setting_id, false ) ) {
			$slugs[] = $term->slug;
		}
	}

	return $slugs;
}

/**
 * Published property count optionally filtered by departments.
 *
 * @param string[] $department_slugs Department slugs (empty = all).
 * @return int
 */
function hvn_realty_get_property_count_for_map( $department_slugs = array() ) {
	if ( empty( $department_slugs ) || ! post_type_exists( 'hvnly_property' ) ) {
		return hvn_realty_get_property_count();
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'hvnly_property',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'hvnly_prop_depts',
					'field'    => 'slug',
					'terms'    => array_map( 'sanitize_key', $department_slugs ),
				),
			),
		)
	);

	return absint( $query->found_posts );
}
