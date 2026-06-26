<?php
/**
 * Fallback stubs when Havenlytics integration files are not loaded.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_havenlytics_plugin_active() {
		return class_exists( 'HvnlyNab' ) || function_exists( 'HVNLY_NAB' );
	}
}

if ( ! function_exists( 'hvn_realty_is_property_context' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_property_context() {
		return post_type_exists( 'hvnly_property' ) && ( is_singular( 'hvnly_property' ) || is_post_type_archive( 'hvnly_property' ) );
	}
}

if ( ! function_exists( 'hvn_realty_get_plugin_page_id' ) ) {
	/**
	 * @param string $page_key Page key.
	 * @return int
	 */
	function hvn_realty_get_plugin_page_id( $page_key ) {
		if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
			return 0;
		}

		$option_map = array(
			'property_search'   => 'hvnly_property_search_page_id',
			'property_grid'     => 'hvnly_property_grid_page_id',
			'property_lists'    => 'hvnly_property_list_page_id',
			'property_agents'   => 'hvnly_property_agents_page_id',
			'property_agencies' => 'hvnly_property_agencies_page_id',
		);

		$option_key = $option_map[ $page_key ] ?? '';
		if ( '' === $option_key ) {
			return 0;
		}

		$page_id = absint( get_option( $option_key, 0 ) );

		return ( $page_id > 0 && get_post( $page_id ) ) ? $page_id : 0;
	}
}

if ( ! function_exists( 'hvn_realty_get_plugin_page_map' ) ) {
	/**
	 * @return array<string, string>
	 */
	function hvn_realty_get_plugin_page_map() {
		return array(
			'property_search'   => 'hvnly_property_search_page_id',
			'property_grid'     => 'hvnly_property_grid_page_id',
			'property_lists'    => 'hvnly_property_list_page_id',
			'property_agents'   => 'hvnly_property_agents_page_id',
			'property_agencies' => 'hvnly_property_agencies_page_id',
		);
	}
}

if ( ! function_exists( 'hvn_realty_get_property_taxonomies' ) ) {
	/**
	 * @return string[]
	 */
	function hvn_realty_get_property_taxonomies() {
		$taxonomies = array(
			'hvnly_prop_types',
			'hvnly_prop_depts',
			'hvnly_prop_locations',
			'hvnly_prop_status',
			'hvnly_prop_features',
			'hvnly_prop_badges',
			'hvnly_prop_tags',
		);

		return array_values(
			array_filter(
				$taxonomies,
				static function ( $taxonomy ) {
					return taxonomy_exists( $taxonomy );
				}
			)
		);
	}
}

if ( ! function_exists( 'hvn_realty_is_property_taxonomy_context' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_property_taxonomy_context() {
		if ( ! is_tax() ) {
			return false;
		}

		$term = get_queried_object();
		if ( ! $term || empty( $term->taxonomy ) ) {
			return false;
		}

		return in_array( $term->taxonomy, hvn_realty_get_property_taxonomies(), true );
	}
}

if ( ! function_exists( 'hvn_realty_should_show_realty_home' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_should_show_realty_home() {
		return false;
	}
}

if ( ! function_exists( 'hvn_realty_render_homepage_sections' ) ) {
	/**
	 * @return void
	 */
	function hvn_realty_render_homepage_sections() {
		if ( function_exists( 'hvn_realty_safe_get_template_part' ) ) {
			hvn_realty_safe_get_template_part( 'template-parts/home/plugin-inactive' );
			return;
		}

		get_template_part( 'template-parts/home/plugin-inactive' );
	}
}

if ( ! function_exists( 'hvn_realty_get_property_search_url' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_property_search_url() {
		return function_exists( 'hvn_realty_get_search_url' ) ? hvn_realty_get_search_url() : home_url( '/?s=' );
	}
}

if ( ! function_exists( 'hvn_realty_get_plugin_page_url' ) ) {
	/**
	 * @param string $page_key Page key.
	 * @return string
	 */
	function hvn_realty_get_plugin_page_url( $page_key ) {
		unset( $page_key );
		return home_url( '/' );
	}
}

if ( ! function_exists( 'hvn_realty_render_shortcode' ) ) {
	/**
	 * @param string               $tag  Shortcode tag.
	 * @param array<string, mixed> $atts Attributes.
	 * @return string
	 */
	function hvn_realty_render_shortcode( $tag, $atts = array() ) {
		if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() || empty( $tag ) ) {
			return '';
		}

		if ( function_exists( 'shortcode_exists' ) && ! shortcode_exists( sanitize_key( $tag ) ) ) {
			return '';
		}

		$parts = array();
		foreach ( $atts as $key => $value ) {
			if ( null === $value || '' === $value ) {
				continue;
			}
			$parts[] = sanitize_key( $key ) . '="' . esc_attr( (string) $value ) . '"';
		}

		$shortcode = '[' . sanitize_key( $tag );
		if ( ! empty( $parts ) ) {
			$shortcode .= ' ' . implode( ' ', $parts );
		}
		$shortcode .= ']';

		return do_shortcode( $shortcode );
	}
}

if ( ! function_exists( 'hvn_realty_is_realty_homepage' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_realty_homepage() {
		return false;
	}
}

if ( ! function_exists( 'hvn_realty_is_property_view' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_property_view() {
		return function_exists( 'hvn_realty_is_property_context' ) && hvn_realty_is_property_context();
	}
}

if ( ! function_exists( 'hvn_realty_is_agent_context' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_agent_context() {
		return post_type_exists( 'hvnly_agent' ) && ( is_singular( 'hvnly_agent' ) || is_post_type_archive( 'hvnly_agent' ) );
	}
}

if ( ! function_exists( 'hvn_realty_is_agency_context' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_agency_context() {
		return taxonomy_exists( 'hvnly_agent_agency' ) && is_tax( 'hvnly_agent_agency' );
	}
}

if ( ! function_exists( 'hvn_realty_is_havenlytics_view' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_havenlytics_view() {
		return hvn_realty_is_property_view()
			|| hvn_realty_is_agent_context()
			|| hvn_realty_is_agency_context()
			|| ( function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() );
	}
}

if ( ! function_exists( 'hvn_realty_is_plugin_shortcode_page' ) ) {
	/**
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	function hvn_realty_is_plugin_shortcode_page( $post_id = 0 ) {
		unset( $post_id );
		return false;
	}
}

if ( ! function_exists( 'hvn_realty_get_property_count' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_property_count() {
		if ( ! post_type_exists( 'hvnly_property' ) ) {
			return 0;
		}

		$counts = wp_count_posts( 'hvnly_property' );

		return absint( $counts->publish ?? 0 );
	}
}

if ( ! function_exists( 'hvn_realty_get_agent_count' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_agent_count() {
		if ( ! post_type_exists( 'hvnly_agent' ) ) {
			return 0;
		}

		$counts = wp_count_posts( 'hvnly_agent' );

		return absint( $counts->publish ?? 0 );
	}
}

if ( ! function_exists( 'hvn_realty_get_agency_count' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_agency_count() {
		if ( ! taxonomy_exists( 'hvnly_agent_agency' ) ) {
			return 0;
		}

		$count = wp_count_terms(
			array(
				'taxonomy'   => 'hvnly_agent_agency',
				'hide_empty' => false,
			)
		);

		return is_wp_error( $count ) ? 0 : absint( $count );
	}
}

if ( ! function_exists( 'hvn_realty_get_property_departments' ) ) {
	/**
	 * @return array<int, object>
	 */
	function hvn_realty_get_property_departments() {
		if ( ! taxonomy_exists( 'hvnly_prop_depts' ) ) {
			return array();
		}

		$terms = get_terms(
			array(
				'taxonomy'   => 'hvnly_prop_depts',
				'hide_empty' => false,
			)
		);

		return is_wp_error( $terms ) ? array() : $terms;
	}
}

if ( ! function_exists( 'hvn_realty_get_home_section_title' ) ) {
	/**
	 * @param string $section Section key.
	 * @param string $default Default title.
	 * @return string
	 */
	function hvn_realty_get_home_section_title( $section, $default ) {
		unset( $section );
		return (string) $default;
	}
}

if ( ! function_exists( 'hvn_realty_get_home_section_subtitle' ) ) {
	/**
	 * @param string $section Section key.
	 * @param string $default Default subtitle.
	 * @return string
	 */
	function hvn_realty_get_home_section_subtitle( $section, $default ) {
		unset( $section );
		return (string) $default;
	}
}

if ( ! function_exists( 'hvn_realty_get_home_department_button_text' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_department_button_text() {
		return __( 'View All Properties', 'havenlytics-realty' );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_department_button_url' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_department_button_url() {
		return hvn_realty_get_property_search_url();
	}
}

if ( ! function_exists( 'hvn_realty_get_grid_url_for_department' ) ) {
	/**
	 * @param string $department Department slug.
	 * @return string
	 */
	function hvn_realty_get_grid_url_for_department( $department ) {
		$url = hvn_realty_get_plugin_page_url( 'property_grid' );

		return add_query_arg( 'department', sanitize_key( $department ), $url );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_hero_title' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_hero_title() {
		return (string) get_theme_mod(
			'hvn_realty_home_hero_title',
			__( 'Find Your Perfect Property', 'havenlytics-realty' )
		);
	}
}

if ( ! function_exists( 'hvn_realty_get_home_hero_subtitle' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_hero_subtitle() {
		return (string) get_theme_mod(
			'hvn_realty_home_hero_subtitle',
			__( 'Search thousands of listings, connect with agents, and discover your next home.', 'havenlytics-realty' )
		);
	}
}

if ( ! function_exists( 'hvn_realty_is_home_auto_setup_enabled' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_is_home_auto_setup_enabled() {
		return (bool) get_theme_mod( 'hvn_realty_home_auto_setup', true );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_cta_headline' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_cta_headline() {
		return get_theme_mod( 'hvn_realty_home_cta_headline', __( 'Ready to find your dream home?', 'havenlytics-realty' ) );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_cta_subtext' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_cta_subtext() {
		return (string) get_theme_mod( 'hvn_realty_home_cta_subtext', '' );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_cta_bg_image_id' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_home_cta_bg_image_id() {
		return absint( get_theme_mod( 'hvn_realty_home_cta_bg_image', 0 ) );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_cta_overlay_opacity' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_home_cta_overlay_opacity() {
		return max( 0, min( 90, absint( get_theme_mod( 'hvn_realty_home_cta_overlay_opacity', 40 ) ) ) );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_cta_primary_text' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_cta_primary_text() {
		return get_theme_mod( 'hvn_realty_home_cta_primary_text', __( 'Browse Properties', 'havenlytics-realty' ) );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_footer_cta_text' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_footer_cta_text() {
		return get_theme_mod( 'hvn_realty_home_footer_cta_text', '' );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_cta_background_attr' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_cta_background_attr() {
		return '';
	}
}

if ( ! function_exists( 'hvn_realty_get_home_hero_height' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_home_hero_height() {
		return 70;
	}
}

if ( ! function_exists( 'hvn_realty_get_home_hero_height_mobile' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_home_hero_height_mobile() {
		return 55;
	}
}

if ( ! function_exists( 'hvn_realty_get_home_carousel_settings' ) ) {
	/**
	 * @return array<string, mixed>
	 */
	function hvn_realty_get_home_carousel_settings() {
		return array(
			'autoplay'       => true,
			'autoplay_speed' => 5000,
			'loop'           => true,
			'slides_desktop' => 3,
			'slides_tablet'  => 2,
			'slides_mobile'  => 1,
		);
	}
}

if ( ! function_exists( 'hvn_realty_get_home_map_department_slugs' ) ) {
	/**
	 * @return string[]
	 */
	function hvn_realty_get_home_map_department_slugs() {
		return array();
	}
}

if ( ! function_exists( 'hvn_realty_is_home_section_enabled' ) ) {
	/**
	 * @param string $section Section key.
	 * @return bool
	 */
	function hvn_realty_is_home_section_enabled( $section ) {
		if ( in_array( $section, array( 'property-taxonomies', 'property-locations', 'property-categories' ), true ) ) {
			if ( null !== get_theme_mod( 'hvn_realty_home_show_property_taxonomies', null ) ) {
				return (bool) get_theme_mod( 'hvn_realty_home_show_property_taxonomies' );
			}
			if ( null !== get_theme_mod( 'hvn_realty_home_show_property_locations', null ) ) {
				return (bool) get_theme_mod( 'hvn_realty_home_show_property_locations' );
			}
			return (bool) get_theme_mod( 'hvn_realty_home_show_property_categories', true );
		}

		unset( $section );
		return false;
	}
}

if ( ! function_exists( 'hvn_realty_get_theme_mod_with_legacy' ) ) {
	/**
	 * @param string       $key     Setting key.
	 * @param string|array $legacy  Legacy keys.
	 * @param mixed        $default Default.
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
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomy_sources' ) ) {
	/**
	 * @return array<string, array<string, mixed>>
	 */
	function hvn_realty_get_home_taxonomy_sources() {
		return array(
			'locations' => array(
				'taxonomy'      => 'hvnly_prop_locations',
				'label'         => __( 'Locations', 'havenlytics-realty' ),
				'media'         => 'image',
				'icon_fallback' => 'fas fa-map-marker-alt',
				'link_label'    => __( 'View location', 'havenlytics-realty' ),
			),
		);
	}
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomy_source' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_taxonomy_source() {
		return 'locations';
	}
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomies_title' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_taxonomies_title() {
		return (string) hvn_realty_get_theme_mod_with_legacy(
			'hvn_realty_home_taxonomies_title',
			array( 'hvn_realty_home_locations_title' ),
			__( 'Property Locations', 'havenlytics-realty' )
		);
	}
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomies_subtitle' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_taxonomies_subtitle() {
		return (string) hvn_realty_get_theme_mod_with_legacy(
			'hvn_realty_home_taxonomies_subtitle',
			array( 'hvn_realty_home_locations_subtitle' ),
			__( 'Explore listings by city and region.', 'havenlytics-realty' )
		);
	}
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomies_count' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_home_taxonomies_count() {
		return 8;
	}
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomies_columns' ) ) {
	/**
	 * @return int
	 */
	function hvn_realty_get_home_taxonomies_columns() {
		return 4;
	}
}

if ( ! function_exists( 'hvn_realty_show_home_taxonomy_counts' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_show_home_taxonomy_counts() {
		return true;
	}
}

if ( ! function_exists( 'hvn_realty_show_home_taxonomy_icons' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_show_home_taxonomy_icons() {
		return true;
	}
}

if ( ! function_exists( 'hvn_realty_show_home_taxonomy_images' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_show_home_taxonomy_images() {
		return true;
	}
}

if ( ! function_exists( 'hvn_realty_get_term_icon_class' ) ) {
	/**
	 * @param int $term_id Term ID.
	 * @return string
	 */
	function hvn_realty_get_term_icon_class( $term_id ) {
		unset( $term_id );
		return '';
	}
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomy_card_media' ) ) {
	/**
	 * @param WP_Term $term       Term.
	 * @param string  $source_key Source.
	 * @return array<string, string>
	 */
	function hvn_realty_get_home_taxonomy_card_media( $term, $source_key ) {
		unset( $term, $source_key );
		return array( 'type' => 'none' );
	}
}

if ( ! function_exists( 'hvn_realty_get_home_taxonomy_terms' ) ) {
	/**
	 * @param int         $limit      Limit.
	 * @param string|null $source_key Source.
	 * @return array<int, WP_Term>
	 */
	function hvn_realty_get_home_taxonomy_terms( $limit = 0, $source_key = null ) {
		unset( $limit, $source_key );
		return array();
	}
}

if ( ! function_exists( 'hvn_realty_get_property_locations' ) ) {
	/**
	 * @param int $limit Limit.
	 * @return array<int, WP_Term>
	 */
	function hvn_realty_get_property_locations( $limit = 0 ) {
		if ( function_exists( 'hvn_realty_get_home_taxonomy_terms' ) ) {
			return hvn_realty_get_home_taxonomy_terms( $limit, 'locations' );
		}

		unset( $limit );
		return array();
	}
}

if ( ! function_exists( 'hvn_realty_get_term_image_url' ) ) {
	/**
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
}

if ( ! function_exists( 'hvn_realty_get_property_count_for_map' ) ) {
	/**
	 * @param string[] $department_slugs Department slugs.
	 * @return int
	 */
	function hvn_realty_get_property_count_for_map( $department_slugs = array() ) {
		unset( $department_slugs );
		return hvn_realty_get_property_count();
	}
}

if ( ! function_exists( 'hvn_realty_get_home_sections' ) ) {
	/**
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
}

if ( ! function_exists( 'hvn_realty_get_home_testimonials' ) ) {
	/**
	 * @return array<int, array<string, mixed>>
	 */
	function hvn_realty_get_home_testimonials() {
		return array();
	}
}

if ( ! function_exists( 'hvn_realty_get_home_testimonials_source' ) ) {
	/**
	 * @return string
	 */
	function hvn_realty_get_home_testimonials_source() {
		return 'none';
	}
}

if ( ! function_exists( 'hvn_realty_get_design_token' ) ) {
	/**
	 * @param string $token Token key.
	 * @param string $fallback Fallback hex.
	 * @return string
	 */
	function hvn_realty_get_design_token( $token, $fallback = '' ) {
		$map = array(
			'primary'   => 'hvn_realty_primary_color',
			'secondary' => 'hvn_realty_secondary_color',
			'accent'    => 'hvn_realty_accent_color',
		);

		$defaults = array(
			'primary'   => '#6C60FE',
			'secondary' => '#764ba2',
			'accent'    => '#FF9AA2',
		);

		$key = $map[ $token ] ?? '';
		if ( '' === $key ) {
			$fallback = sanitize_hex_color( $fallback );

			return $fallback ? $fallback : '';
		}

		$value = sanitize_hex_color( get_theme_mod( $key, $defaults[ $token ] ?? $fallback ) );

		return $value ? $value : ( $defaults[ $token ] ?? $fallback );
	}
}

if ( ! function_exists( 'hvn_realty_get_design_token_css_value' ) ) {
	/**
	 * @param string $token Token key.
	 * @return string
	 */
	function hvn_realty_get_design_token_css_value( $token ) {
		return hvn_realty_get_design_token( $token );
	}
}

if ( ! function_exists( 'hvn_realty_uses_plugin_design_tokens' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_uses_plugin_design_tokens() {
		return false;
	}
}

if ( ! function_exists( 'hvn_realty_render_home_hero_map' ) ) {
	/**
	 * @param int $posts_per_page Posts per page.
	 * @return string
	 */
	function hvn_realty_render_home_hero_map( $posts_per_page = 500 ) {
		unset( $posts_per_page );
		return '';
	}
}

if ( ! function_exists( 'hvn_realty_render_similar_property_carousel' ) ) {
	/**
	 * @param array<string, mixed> $args Carousel args.
	 * @return void
	 */
	function hvn_realty_render_similar_property_carousel( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'empty_message' => __( 'No properties found at the moment.', 'havenlytics-realty' ),
			)
		);

		if ( empty( $args['empty_message'] ) ) {
			return;
		}

		printf(
			'<p class="hvn-realty-carousel-empty">%s</p>',
			esc_html( (string) $args['empty_message'] )
		);
	}
}

if ( ! function_exists( 'hvn_realty_show_theme_breadcrumbs_on_plugin_view' ) ) {
	/**
	 * @return bool
	 */
	function hvn_realty_show_theme_breadcrumbs_on_plugin_view() {
		return true;
	}
}

if ( ! function_exists( 'hvn_realty_home_section_heading' ) ) {
	/**
	 * @param string $title     Title.
	 * @param string $subtitle  Subtitle.
	 * @param string $class     CSS class.
	 * @param string $title_id  Title element ID.
	 * @return void
	 */
	function hvn_realty_home_section_heading( $title, $subtitle = '', $class = '', $title_id = '' ) {
		if ( '' === $title ) {
			return;
		}

		$class_attr = $class ? ' class="' . esc_attr( $class ) . '"' : '';
		$id_attr    = $title_id ? ' id="' . esc_attr( $title_id ) . '"' : '';

		echo '<div' . $class_attr . '>';
		echo '<h2' . $id_attr . '>' . esc_html( $title ) . '</h2>';
		if ( '' !== $subtitle ) {
			echo '<p>' . esc_html( $subtitle ) . '</p>';
		}
		echo '</div>';
	}
}
