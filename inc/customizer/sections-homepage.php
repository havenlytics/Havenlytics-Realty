<?php
/**
 * Homepage 2.0.0 Customizer panel and sections.
 *
 * Rebuilt to match the new homepage design. Obsolete homepage controls
 * (hero map, department tabs, hero search, agencies, global carousel,
 * section manager) are intentionally no longer registered. Their stored
 * theme_mod values are NOT deleted — they remain in the database for data
 * safety and simply stop being rendered.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Customizer homepage panel ID. */
define( 'HVN_REALTY_HOMEPAGE_PANEL', 'hvn_realty_homepage_panel' );

/** @deprecated Retained for backward compatibility with stored values. */
define( 'HVN_REALTY_HOMEPAGE_SECTION', 'hvn_realty_home_general' );

/**
 * Whether homepage Customizer controls should display.
 *
 * @return bool
 */
function hvn_realty_customizer_homepage_is_active() {
	return function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home();
}

/**
 * Sanitize homepage property grid count (3–12).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_property_count( $input ) {
	$input = absint( $input );
	return max( 3, min( 12, $input ) );
}

/**
 * Sanitize homepage agents count (3–8).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_agents_count( $input ) {
	$input = absint( $input );
	return max( 3, min( 8, $input ) );
}

/**
 * Sanitize homepage blog post count (3–6).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_posts_count( $input ) {
	$input = absint( $input );
	return max( 3, min( 6, $input ) );
}

/**
 * Sanitize hero background mode.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_home_hero_bg_mode( $input ) {
	return in_array( $input, array( 'static', 'carousel' ), true ) ? $input : 'static';
}

/**
 * Sanitize hero carousel slide count (3–8).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_hero_carousel_count( $input ) {
	$input = absint( $input );
	return max( 3, min( 8, $input ) );
}

/**
 * Sanitize hero carousel autoplay interval (2000–15000 ms).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_hero_carousel_autoplay( $input ) {
	$input = absint( $input );
	return max( 2000, min( 15000, $input ) );
}

/**
 * Sanitize hero carousel transition duration (400–2000 ms).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_hero_carousel_transition( $input ) {
	$input = absint( $input );
	return max( 400, min( 2000, $input ) );
}

/**
 * Sanitize hero Ken Burns zoom toggle.
 *
 * @param mixed $input Raw value.
 * @return bool
 */
function hvn_realty_sanitize_home_hero_zoom( $input ) {
	return (bool) $input;
}

/**
 * Sanitize hero carousel transition effect.
 *
 * @param mixed $input Raw value.
 * @return string
 */
function hvn_realty_sanitize_home_hero_transition_effect( $input ) {
	$allowed = array( 'fade', 'fade_zoom', 'ken_burns', 'soft_scale' );
	$input   = sanitize_key( (string) $input );
	return in_array( $input, $allowed, true ) ? $input : 'fade_zoom';
}

/**
 * Whether hero background carousel Customizer controls should display.
 *
 * @return bool
 */
function hvn_realty_customizer_hero_bg_is_carousel() {
	return 'carousel' === get_theme_mod( 'hvn_realty_home_hero_bg_mode', 'static' );
}

/**
 * Register a homepage text control + setting.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $id           Setting ID.
 * @param string               $section      Section ID.
 * @param string               $label        Label.
 * @param string               $default      Default value.
 * @param string               $type         Control type (text|url|textarea|email).
 * @param string               $transport    Transport mode.
 * @return void
 */
function hvn_realty_home_add_text( $wp_customize, $id, $section, $label, $default = '', $type = 'text', $transport = 'postMessage' ) {
	$sanitize = 'url' === $type ? 'esc_url_raw' : ( 'textarea' === $type ? 'sanitize_textarea_field' : 'sanitize_text_field' );

	$wp_customize->add_setting(
		$id,
		array(
			'default'           => $default,
			'sanitize_callback' => $sanitize,
			'transport'         => $transport,
		)
	);
	$wp_customize->add_control(
		$id,
		array(
			'label'   => $label,
			'section' => $section,
			'type'    => $type,
		)
	);
}

/**
 * Register a homepage number control + setting.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $id           Setting ID.
 * @param string               $section      Section ID.
 * @param string               $label        Label.
 * @param int                  $default      Default.
 * @param string               $sanitize     Sanitize callback.
 * @param array                $input_attrs  Input attributes.
 * @return void
 */
function hvn_realty_home_add_number( $wp_customize, $id, $section, $label, $default, $sanitize, $input_attrs = array() ) {
	$wp_customize->add_setting(
		$id,
		array(
			'default'           => $default,
			'sanitize_callback' => $sanitize,
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		$id,
		array(
			'label'       => $label,
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => $input_attrs,
		)
	);
}

/**
 * Register a homepage media (image) control + setting.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $id           Setting ID.
 * @param string               $section      Section ID.
 * @param string               $label        Label.
 * @return void
 */
function hvn_realty_home_add_image( $wp_customize, $id, $section, $label ) {
	$wp_customize->add_setting(
		$id,
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			$id,
			array(
				'label'     => $label,
				'section'   => $section,
				'mime_type' => 'image',
			)
		)
	);
}

/**
 * Register the Homepage Customizer panel and section controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function hvn_realty_customizer_register_homepage( $wp_customize ) {
	$home_id  = (int) get_option( 'page_on_front', 0 );
	$home_url = $home_id > 0 ? get_permalink( $home_id ) : home_url( '/' );

	$description = esc_html__( 'Edit every section of the homepage: hero, search, featured properties, types, locations, map, why-choose, collections, agents, testimonials, blog and call to action.', 'havenlytics-realty' );
	if ( $home_url ) {
		$description .= ' ' . sprintf(
			/* translators: %s: homepage URL */
			__( '<a href="%s" target="_blank" rel="noopener noreferrer">View homepage</a>', 'havenlytics-realty' ),
			esc_url( $home_url )
		);
	}

	$wp_customize->add_panel(
		HVN_REALTY_HOMEPAGE_PANEL,
		array(
			'title'           => esc_html__( 'Homepage', 'havenlytics-realty' ),
			'description'     => $description,
			'priority'        => 20,
			'active_callback' => 'hvn_realty_customizer_homepage_is_active',
		)
	);

	$sections = array(
		'hvn_realty_home_hero'           => array( 'title' => esc_html__( 'Hero', 'havenlytics-realty' ), 'priority' => 10 ),
		'hvn_realty_home_search'         => array( 'title' => esc_html__( 'Search Panel', 'havenlytics-realty' ), 'priority' => 15 ),
		'hvn_realty_home_featured'       => array( 'title' => esc_html__( 'Featured Properties', 'havenlytics-realty' ), 'priority' => 18 ),
		'hvn_realty_home_property_types' => array( 'title' => esc_html__( 'Property Types', 'havenlytics-realty' ), 'priority' => 25 ),
		'hvn_realty_home_locations'      => array( 'title' => esc_html__( 'Locations', 'havenlytics-realty' ), 'priority' => 30 ),
		'hvn_realty_home_map'            => array( 'title' => esc_html__( 'Property Map', 'havenlytics-realty' ), 'priority' => 33 ),
		'hvn_realty_home_why'            => array( 'title' => esc_html__( 'Why Choose Us', 'havenlytics-realty' ), 'priority' => 35 ),
		'hvn_realty_home_collections'    => array( 'title' => esc_html__( 'Curated Collections', 'havenlytics-realty' ), 'priority' => 36 ),
		'hvn_realty_home_process'        => array( 'title' => esc_html__( 'How It Works', 'havenlytics-realty' ), 'priority' => 37 ),
		'hvn_realty_home_agents'         => array( 'title' => esc_html__( 'Agents', 'havenlytics-realty' ), 'priority' => 40 ),
		'hvn_realty_home_testimonials'   => array( 'title' => esc_html__( 'Testimonials', 'havenlytics-realty' ), 'priority' => 45 ),
		'hvn_realty_home_blog'           => array( 'title' => esc_html__( 'Latest Blog', 'havenlytics-realty' ), 'priority' => 50 ),
		'hvn_realty_home_cta'            => array( 'title' => esc_html__( 'Call to Action', 'havenlytics-realty' ), 'priority' => 55 ),
	);

	foreach ( $sections as $id => $args ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'           => $args['title'],
				'panel'           => HVN_REALTY_HOMEPAGE_PANEL,
				'priority'        => $args['priority'],
				'active_callback' => 'hvn_realty_customizer_homepage_is_active',
			)
		);
	}

	// --- Section order manager ----------------------------------------------
	$wp_customize->add_section(
		'hvn_realty_home_section_order',
		array(
			'title'           => esc_html__( 'Section Order', 'havenlytics-realty' ),
			'description'     => esc_html__( 'Drag sections to reorder the homepage. Use Show/Hide to toggle visibility without opening each section panel.', 'havenlytics-realty' ),
			'panel'           => HVN_REALTY_HOMEPAGE_PANEL,
			'priority'        => 5,
			'active_callback' => 'hvn_realty_customizer_homepage_is_active',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_section_order',
		array(
			'default'           => wp_json_encode( hvn_realty_get_default_home_section_order() ),
			'sanitize_callback' => 'hvn_realty_sanitize_home_section_order',
			'transport'         => 'refresh',
		)
	);

	if ( class_exists( 'HVN_Realty_Customize_Section_Order_Control' ) ) {
		$wp_customize->add_control(
			new HVN_Realty_Customize_Section_Order_Control(
				$wp_customize,
				'hvn_realty_home_section_order',
				array(
					'label'       => esc_html__( 'Homepage Sections', 'havenlytics-realty' ),
					'description' => esc_html__( 'Drag to reorder. Changes apply after the preview refreshes.', 'havenlytics-realty' ),
					'section'     => 'hvn_realty_home_section_order',
					'settings'    => 'hvn_realty_home_section_order',
				)
			)
		);
	}

	// --- Hero ---------------------------------------------------------------
	$s = 'hvn_realty_home_hero';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_hero', $s, esc_html__( 'Show Hero', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_eyebrow', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Data-Backed Real Estate', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_title_before', $s, esc_html__( 'Title (before highlight)', 'havenlytics-realty' ), __( 'Find a home that', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_title_highlight', $s, esc_html__( 'Title (highlighted)', 'havenlytics-realty' ), __( 'holds its value', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_title_after', $s, esc_html__( 'Title (after highlight)', 'havenlytics-realty' ), __( ', not just your attention.', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_subtitle', $s, esc_html__( 'Subtitle', 'havenlytics-realty' ), __( 'Havenlytics pairs licensed local agents with transparent market data, so every offer you make is grounded in evidence — not guesswork.', 'havenlytics-realty' ), 'textarea' );
	// Legacy hero CTAs / float badge — settings preserved for BC; not rendered in Homepage 2.3 layout.
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_primary_label', $s, esc_html__( 'Primary button label (legacy)', 'havenlytics-realty' ), __( 'Browse Properties', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_primary_url', $s, esc_html__( 'Primary button URL (legacy)', 'havenlytics-realty' ), '#hvn-theme-home-search', 'url' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_ghost_label', $s, esc_html__( 'Secondary button label (legacy)', 'havenlytics-realty' ), __( 'Meet an Agent', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_ghost_url', $s, esc_html__( 'Secondary button URL (legacy)', 'havenlytics-realty' ), '#hvn-theme-home-agents', 'url' );
	hvn_realty_home_add_image( $wp_customize, 'hvn_realty_home_hero_image_a', $s, esc_html__( 'Hero image (large)', 'havenlytics-realty' ) );
	// image_b kept registered for backward compatibility; unused by Homepage 2.3 hero layout.
	hvn_realty_home_add_image( $wp_customize, 'hvn_realty_home_hero_image_b', $s, esc_html__( 'Hero image (inset, unused)', 'havenlytics-realty' ) );

	$wp_customize->add_setting(
		'hvn_realty_home_hero_bg_mode',
		array(
			'default'           => 'static',
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_bg_mode',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_bg_mode',
		array(
			'label'       => esc_html__( 'Hero background mode', 'havenlytics-realty' ),
			'description' => esc_html__( 'Static uses the hero image above. Carousel pulls recent property photos.', 'havenlytics-realty' ),
			'section'     => $s,
			'type'        => 'select',
			'choices'     => array(
				'static'   => esc_html__( 'Static image', 'havenlytics-realty' ),
				'carousel' => esc_html__( 'Property photo carousel', 'havenlytics-realty' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_carousel_count',
		array(
			'default'           => 5,
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_carousel_count',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_carousel_count',
		array(
			'label'           => esc_html__( 'Carousel slide count', 'havenlytics-realty' ),
			'section'         => $s,
			'type'            => 'number',
			'input_attrs'     => array(
				'min'  => 3,
				'max'  => 8,
				'step' => 1,
			),
			'active_callback' => 'hvn_realty_customizer_hero_bg_is_carousel',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_carousel_autoplay',
		array(
			'default'           => 5000,
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_carousel_autoplay',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_carousel_autoplay',
		array(
			'label'           => esc_html__( 'Slide duration (ms)', 'havenlytics-realty' ),
			'description'     => esc_html__( 'How long each slide stays visible before advancing.', 'havenlytics-realty' ),
			'section'         => $s,
			'type'            => 'number',
			'input_attrs'     => array(
				'min'  => 2000,
				'max'  => 15000,
				'step' => 100,
			),
			'active_callback' => 'hvn_realty_customizer_hero_bg_is_carousel',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_carousel_autoplay_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'hvn_realty_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_carousel_autoplay_enabled',
		array(
			'label'           => esc_html__( 'Autoplay', 'havenlytics-realty' ),
			'section'         => $s,
			'type'            => 'checkbox',
			'active_callback' => 'hvn_realty_customizer_hero_bg_is_carousel',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_carousel_transition',
		array(
			'default'           => 900,
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_carousel_transition',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_carousel_transition',
		array(
			'label'           => esc_html__( 'Transition speed (ms)', 'havenlytics-realty' ),
			'description'     => esc_html__( 'Duration of the fade between slides.', 'havenlytics-realty' ),
			'section'         => $s,
			'type'            => 'number',
			'input_attrs'     => array(
				'min'  => 400,
				'max'  => 2000,
				'step' => 50,
			),
			'active_callback' => 'hvn_realty_customizer_hero_bg_is_carousel',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_carousel_loop',
		array(
			'default'           => true,
			'sanitize_callback' => 'hvn_realty_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_carousel_loop',
		array(
			'label'           => esc_html__( 'Loop carousel', 'havenlytics-realty' ),
			'section'         => $s,
			'type'            => 'checkbox',
			'active_callback' => 'hvn_realty_customizer_hero_bg_is_carousel',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_carousel_pause_hover',
		array(
			'default'           => true,
			'sanitize_callback' => 'hvn_realty_sanitize_checkbox',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_carousel_pause_hover',
		array(
			'label'           => esc_html__( 'Pause carousel on hover', 'havenlytics-realty' ),
			'section'         => $s,
			'type'            => 'checkbox',
			'active_callback' => 'hvn_realty_customizer_hero_bg_is_carousel',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_zoom',
		array(
			'default'           => true,
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_zoom',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_zoom',
		array(
			'label'       => esc_html__( 'Zoom effect', 'havenlytics-realty' ),
			'description' => esc_html__( 'Gentle cinematic zoom on the active hero background.', 'havenlytics-realty' ),
			'section'     => $s,
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_transition_effect',
		array(
			'default'           => 'fade_zoom',
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_transition_effect',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_transition_effect',
		array(
			'label'           => esc_html__( 'Transition effect', 'havenlytics-realty' ),
			'section'         => $s,
			'type'            => 'select',
			'choices'         => array(
				'fade'       => esc_html__( 'Fade', 'havenlytics-realty' ),
				'fade_zoom'  => esc_html__( 'Fade + Zoom', 'havenlytics-realty' ),
				'ken_burns'  => esc_html__( 'Ken Burns', 'havenlytics-realty' ),
				'soft_scale' => esc_html__( 'Soft Scale', 'havenlytics-realty' ),
			),
			'active_callback' => 'hvn_realty_customizer_hero_bg_is_carousel',
		)
	);

	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_float_title', $s, esc_html__( 'Floating badge title', 'havenlytics-realty' ), __( 'Valuation Verified', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_float_subtitle', $s, esc_html__( 'Floating badge subtitle', 'havenlytics-realty' ), __( 'Data-backed every listing', 'havenlytics-realty' ) );
	$hero_stat_defaults = array(
		1 => array( 2400, '', __( 'Homes Sold', 'havenlytics-realty' ) ),
		2 => array( 98, '%', __( 'Client Satisfaction', 'havenlytics-realty' ) ),
		3 => array( 17, '', __( 'Years of Data', 'havenlytics-realty' ) ),
	);
	foreach ( $hero_stat_defaults as $n => $sd ) {
		hvn_realty_home_add_number( $wp_customize, 'hvn_realty_home_hero_stat' . $n . '_value', $s, sprintf( /* translators: %d: stat number */ esc_html__( 'Stat %d value', 'havenlytics-realty' ), $n ), $sd[0], 'absint', array( 'min' => 0 ) );
		hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_stat' . $n . '_suffix', $s, sprintf( /* translators: %d: stat number */ esc_html__( 'Stat %d suffix', 'havenlytics-realty' ), $n ), $sd[1] );
		hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_hero_stat' . $n . '_label', $s, sprintf( /* translators: %d: stat number */ esc_html__( 'Stat %d label', 'havenlytics-realty' ), $n ), $sd[2] );
	}

	// --- Search -------------------------------------------------------------
	$s = 'hvn_realty_home_search';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_search', $s, esc_html__( 'Show Search Panel', 'havenlytics-realty' ), true, 'postMessage' );

	$search_fields_default = function_exists( 'hvn_realty_get_default_home_search_fields_config' )
		? wp_json_encode( hvn_realty_get_default_home_search_fields_config() )
		: '[]';

	$wp_customize->add_setting(
		'hvn_realty_home_search_fields',
		array(
			'default'           => $search_fields_default,
			'sanitize_callback' => 'hvn_realty_sanitize_home_search_fields',
			'transport'         => 'postMessage',
		)
	);

	if ( class_exists( 'HVN_Realty_Customize_Search_Builder_Control' ) ) {
		$wp_customize->add_control(
			new HVN_Realty_Customize_Search_Builder_Control(
				$wp_customize,
				'hvn_realty_home_search_fields',
				array(
					'label'       => esc_html__( 'Search Builder', 'havenlytics-realty' ),
					'description' => esc_html__( 'Drag fields to reorder. Enable or disable fields without removing them. Assign fields to the main row or More Filters panel.', 'havenlytics-realty' ),
					'section'     => $s,
					'settings'    => 'hvn_realty_home_search_fields',
				)
			)
		);
	}

	// --- Why choose ---------------------------------------------------------
	$s = 'hvn_realty_home_why';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_why', $s, esc_html__( 'Show Why-Choose Section', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_why_eyebrow', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Why Havenlytics', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_why_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Real estate, grounded in evidence', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_why_subtitle', $s, esc_html__( 'Subtitle', 'havenlytics-realty' ), __( 'We combine licensed local expertise with continuously updated market data, so you always know what a home is actually worth.', 'havenlytics-realty' ), 'textarea' );

	// Default mirrors hvn_realty_get_home_why_items(): repeater → legacy cards → built-in defaults,
	// so existing sites see their previously saved Why-Choose content in the repeater.
	$why_default = function_exists( 'hvn_realty_get_home_why_items' )
		? wp_json_encode( hvn_realty_get_home_why_items() )
		: ( function_exists( 'hvn_realty_get_default_home_why_items' ) ? wp_json_encode( hvn_realty_get_default_home_why_items() ) : '[]' );

	$wp_customize->add_setting(
		'hvn_realty_home_why_items',
		array(
			'default'           => $why_default,
			'sanitize_callback' => 'hvn_realty_sanitize_home_why_items',
			'transport'         => 'postMessage',
		)
	);

	if ( class_exists( 'HVN_Realty_Customize_Why_Control' ) ) {
		$wp_customize->add_control(
			new HVN_Realty_Customize_Why_Control(
				$wp_customize,
				'hvn_realty_home_why_items',
				array(
					'label'       => esc_html__( 'Feature items', 'havenlytics-realty' ),
					'description' => esc_html__( 'Drag to reorder. Add unlimited items with an icon, title, description, and optional link.', 'havenlytics-realty' ),
					'section'     => $s,
					'settings'    => 'hvn_realty_home_why_items',
				)
			)
		);
	}

	// --- Featured properties ------------------------------------------------
	$s = 'hvn_realty_home_featured';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_properties', $s, esc_html__( 'Show Featured Properties', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_featured_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Featured Properties', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_featured_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Handpicked homes worth a closer look', 'havenlytics-realty' ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_home_featured_count', $s, esc_html__( 'Number of properties', 'havenlytics-realty' ), 6, 'hvn_realty_sanitize_home_property_count', array( 'min' => 3, 'max' => 12 ) );

	// --- Property types -----------------------------------------------------
	$s = 'hvn_realty_home_property_types';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_types', $s, esc_html__( 'Show Property Types', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_property_types_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Browse by Type', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_property_types_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Whatever shape home takes for you', 'havenlytics-realty' ) );

	// --- Locations ----------------------------------------------------------
	$s = 'hvn_realty_home_locations';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_locations', $s, esc_html__( 'Show Locations', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_locations_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Featured Locations', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_locations_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Neighborhoods our agents know by heart', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_locations_text', $s, esc_html__( 'Description', 'havenlytics-realty' ), __( 'Every market we serve gets walked, photographed, and tracked by a local agent — not just listed.', 'havenlytics-realty' ), 'textarea' );

	// --- Interactive Property Map -------------------------------------------
	$s = 'hvn_realty_home_map';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_map', $s, esc_html__( 'Show Property Map', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_map_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Explore The Map', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_map_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Every listing, precisely placed', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_map_text', $s, esc_html__( 'Description', 'havenlytics-realty' ), __( 'Pan the map, zoom into a block, and preview a home without leaving the page.', 'havenlytics-realty' ), 'textarea' );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_home_map_limit', $s, esc_html__( 'Max map markers', 'havenlytics-realty' ), 40, 'absint', array( 'min' => 5, 'max' => 100 ) );

	// --- Curated Collections ------------------------------------------------
	$s = 'hvn_realty_home_collections';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_collections', $s, esc_html__( 'Show Curated Collections', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_collections_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Curated Collections', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_collections_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Portfolios for the particular buyer', 'havenlytics-realty' ) );

	// --- How It Works / Process ---------------------------------------------
	$s = 'hvn_realty_home_process';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_process', $s, esc_html__( 'Show How It Works', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_process_eyebrow', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'How It Works', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_process_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'A clearer path from search to keys', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_process_subtitle', $s, esc_html__( 'Subtitle', 'havenlytics-realty' ), __( 'Whether you are buying or selling, every step is guided by local expertise and transparent market data.', 'havenlytics-realty' ), 'textarea' );

	$hvn_process_defaults = function_exists( 'hvn_realty_get_default_home_process_steps' )
		? hvn_realty_get_default_home_process_steps()
		: array();

	for ( $hvn_pi = 1; $hvn_pi <= 4; $hvn_pi++ ) {
		$hvn_pd = isset( $hvn_process_defaults[ $hvn_pi - 1 ] ) ? $hvn_process_defaults[ $hvn_pi - 1 ] : array( 'title' => '', 'text' => '', 'url' => '' );
		/* translators: %d: step number */
		hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_process_step' . $hvn_pi . '_title', $s, sprintf( esc_html__( 'Step %d title', 'havenlytics-realty' ), $hvn_pi ), $hvn_pd['title'] );
		/* translators: %d: step number */
		hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_process_step' . $hvn_pi . '_text', $s, sprintf( esc_html__( 'Step %d text', 'havenlytics-realty' ), $hvn_pi ), $hvn_pd['text'], 'textarea' );
		/* translators: %d: step number */
		hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_process_step' . $hvn_pi . '_url', $s, sprintf( esc_html__( 'Step %d URL', 'havenlytics-realty' ), $hvn_pi ), $hvn_pd['url'], 'url' );
	}

	// --- Agents -------------------------------------------------------------
	$s = 'hvn_realty_home_agents';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_agents', $s, esc_html__( 'Show Agents', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_agents_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Meet Our Agents', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_agents_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Local experts, vetted and ranked on results', 'havenlytics-realty' ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_home_agents_count', $s, esc_html__( 'Number of agents', 'havenlytics-realty' ), 4, 'hvn_realty_sanitize_home_agents_count', array( 'min' => 3, 'max' => 8 ) );

	// --- Testimonials -------------------------------------------------------
	$s = 'hvn_realty_home_testimonials';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_testimonials', $s, esc_html__( 'Show Testimonials', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_testimonials_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Client Stories', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_testimonials_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Trusted by buyers and sellers alike', 'havenlytics-realty' ) );

	$wp_customize->add_setting(
		'hvn_realty_home_testimonials',
		array(
			'default'           => function_exists( 'hvn_realty_get_default_home_testimonials' )
				? wp_json_encode( hvn_realty_get_default_home_testimonials() )
				: '[]',
			'sanitize_callback' => 'hvn_realty_sanitize_home_testimonials',
			'transport'         => 'postMessage',
		)
	);

	if ( class_exists( 'HVN_Realty_Customize_Testimonials_Control' ) ) {
		$wp_customize->add_control(
			new HVN_Realty_Customize_Testimonials_Control(
				$wp_customize,
				'hvn_realty_home_testimonials',
				array(
					'label'       => esc_html__( 'Testimonial items', 'havenlytics-realty' ),
					'description' => esc_html__( 'Drag to reorder. Add unlimited testimonials with photo, rating, and review text.', 'havenlytics-realty' ),
					'section'     => $s,
					'settings'    => 'hvn_realty_home_testimonials',
				)
			)
		);
	}

	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_testimonial_stars', $s, esc_html__( 'Show star ratings', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_testimonials_autoplay', $s, esc_html__( 'Autoplay slider', 'havenlytics-realty' ), false, 'postMessage' );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_home_testimonials_speed', $s, esc_html__( 'Autoplay speed (ms)', 'havenlytics-realty' ), 5000, 'absint', array( 'min' => 2000, 'max' => 15000, 'step' => 500 ) );

	// --- Blog ---------------------------------------------------------------
	$s = 'hvn_realty_home_blog';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_blog', $s, esc_html__( 'Show Latest Blog', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_blog_subtitle', $s, esc_html__( 'Eyebrow', 'havenlytics-realty' ), __( 'Latest Insights', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_blog_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Market notes from our research desk', 'havenlytics-realty' ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_home_blog_count', $s, esc_html__( 'Number of posts', 'havenlytics-realty' ), 3, 'hvn_realty_sanitize_home_posts_count', array( 'min' => 3, 'max' => 6 ) );

	// --- CTA ----------------------------------------------------------------
	$s = 'hvn_realty_home_cta';
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_home_show_cta', $s, esc_html__( 'Show Call to Action', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_cta_title', $s, esc_html__( 'Title', 'havenlytics-realty' ), __( 'Ready to see what your home is really worth?', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_cta_subtitle', $s, esc_html__( 'Subtitle', 'havenlytics-realty' ), __( 'Get a free, data-backed valuation from a local Havenlytics agent within 24 hours.', 'havenlytics-realty' ), 'textarea' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_cta_primary_label', $s, esc_html__( 'Primary button label', 'havenlytics-realty' ), __( 'Get a Free Valuation', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_cta_primary_url', $s, esc_html__( 'Primary button URL', 'havenlytics-realty' ), '#hvn-theme-home-agents', 'url' );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_cta_secondary_label', $s, esc_html__( 'Secondary button label', 'havenlytics-realty' ), __( 'Talk to an Agent', 'havenlytics-realty' ) );
	hvn_realty_home_add_text( $wp_customize, 'hvn_realty_home_cta_secondary_url', $s, esc_html__( 'Secondary button URL', 'havenlytics-realty' ), '#hvn-theme-home-agents', 'url' );

	$style_section_map = array(
		'hero'         => 'hvn_realty_home_hero',
		'search'       => 'hvn_realty_home_search',
		'why'          => 'hvn_realty_home_why',
		'properties'   => 'hvn_realty_home_featured',
		'types'        => 'hvn_realty_home_property_types',
		'locations'    => 'hvn_realty_home_locations',
		'map'          => 'hvn_realty_home_map',
		'collections'  => 'hvn_realty_home_collections',
		'process'      => 'hvn_realty_home_process',
		'agents'       => 'hvn_realty_home_agents',
		'testimonials' => 'hvn_realty_home_testimonials',
		'blog'         => 'hvn_realty_home_blog',
		'cta'          => 'hvn_realty_home_cta',
	);

	foreach ( $style_section_map as $slug => $section_id ) {
		if ( function_exists( 'hvn_realty_home_register_section_style_controls' ) ) {
			hvn_realty_home_register_section_style_controls( $wp_customize, $slug, $section_id );
		}
	}
}
add_action( 'customize_register', 'hvn_realty_customizer_register_homepage', 16 );
