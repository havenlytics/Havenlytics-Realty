<?php
/**
 * Real Estate Homepage Customizer panel and sections.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Customizer homepage panel ID. */
define( 'HVN_REALTY_HOMEPAGE_PANEL', 'hvn_realty_homepage_panel' );

/** @deprecated Use HVN_REALTY_HOMEPAGE_PANEL sections instead. */
define( 'HVN_REALTY_HOMEPAGE_SECTION', 'hvn_realty_home_general' );

/**
 * Whether homepage Customizer controls should display.
 *
 * @return bool
 */
function hvn_realty_customizer_homepage_is_active() {
	return function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) && hvn_realty_is_havenlytics_plugin_active();
}

/**
 * Sanitize homepage property grid count (3–12).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_property_count( $input ) {
	$input = absint( $input );
	if ( $input < 3 ) {
		return 3;
	}
	if ( $input > 12 ) {
		return 12;
	}
	return $input;
}

/**
 * Sanitize homepage blog post count (1–6).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_posts_count( $input ) {
	$input = absint( $input );
	if ( $input < 1 ) {
		return 1;
	}
	if ( $input > 12 ) {
		return 12;
	}
	return $input;
}

/**
 * Sanitize hero map height (40–100 vh).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_hero_height( $input ) {
	$input = absint( $input );
	if ( $input < 40 ) {
		return 40;
	}
	if ( $input > 100 ) {
		return 100;
	}
	return $input;
}

/**
 * Sanitize hero search display mode.
 *
 * @param mixed $input Raw value.
 * @return string
 */
function hvn_realty_sanitize_hero_search_display( $input ) {
	$allowed = array( 'header', 'hero', 'both' );

	return in_array( $input, $allowed, true ) ? $input : 'header';
}

/**
 * Sanitize hero search horizontal position.
 *
 * @param mixed $input Raw value.
 * @return string
 */
function hvn_realty_sanitize_hero_search_horizontal( $input ) {
	$allowed = array( 'left', 'center', 'right' );

	return in_array( $input, $allowed, true ) ? $input : 'left';
}

/**
 * Sanitize hero search vertical position.
 *
 * @param mixed $input Raw value.
 * @return string
 */
function hvn_realty_sanitize_hero_search_vertical( $input ) {
	$allowed = array( 'top', 'center', 'bottom' );

	return in_array( $input, $allowed, true ) ? $input : 'center';
}

/**
 * Sanitize hero search offset (0–100 px).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_hero_search_offset( $input ) {
	$input = absint( $input );

	if ( $input > 100 ) {
		return 100;
	}

	return $input;
}

/**
 * Sanitize hero search panel width (400–700 px).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_hero_search_width( $input ) {
	$input = absint( $input );

	if ( $input < 400 ) {
		return 400;
	}

	if ( $input > 700 ) {
		return 700;
	}

	return $input;
}

/**
 * Whether hero search position controls should display in the Customizer.
 *
 * @return bool
 */
function hvn_realty_customizer_hero_search_position_is_active() {
	if ( ! hvn_realty_customizer_homepage_is_active() ) {
		return false;
	}

	return in_array( hvn_realty_get_hero_search_display(), array( 'hero', 'both' ), true );
}

/**
 * Sanitize location term count (4–12).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_locations_count( $input ) {
	$input = absint( $input );
	if ( $input < 4 ) {
		return 4;
	}
	if ( $input > 12 ) {
		return 12;
	}
	return $input;
}

/**
 * Sanitize homepage taxonomy term count (4–24).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_taxonomies_count( $input ) {
	$input = absint( $input );
	if ( $input < 4 ) {
		return 4;
	}
	if ( $input > 24 ) {
		return 24;
	}
	return $input;
}

/**
 * Sanitize homepage taxonomy grid columns (2–6).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_taxonomies_columns( $input ) {
	$input = absint( $input );
	if ( $input < 2 ) {
		return 2;
	}
	if ( $input > 6 ) {
		return 6;
	}
	return $input;
}

/**
 * Sanitize homepage taxonomy source key.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_home_taxonomy_source( $input ) {
	$input   = sanitize_key( $input );
	$sources = function_exists( 'hvn_realty_get_home_taxonomy_sources' )
		? hvn_realty_get_home_taxonomy_sources()
		: array( 'locations' => array() );

	return isset( $sources[ $input ] ) ? $input : 'locations';
}

/**
 * Sanitize carousel overlay opacity.
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_overlay_opacity( $input ) {
	$input = absint( $input );
	if ( $input > 90 ) {
		return 90;
	}
	return $input;
}

/**
 * Sanitize carousel visible slide count.
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_carousel_slides( $input ) {
	$input = absint( $input );
	if ( $input < 1 ) {
		return 1;
	}
	if ( $input > 6 ) {
		return 6;
	}
	return $input;
}

/**
 * Register a text control on a homepage section.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $id          Setting ID.
 * @param string               $section     Section ID.
 * @param string               $label       Label.
 * @param string               $default     Default.
 * @param string               $transport   Transport mode.
 */
function hvn_realty_customizer_add_text( $wp_customize, $id, $section, $label, $default, $transport = 'postMessage' ) {
	$wp_customize->add_setting(
		$id,
		array(
			'default'           => $default,
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => $transport,
		)
	);
	$wp_customize->add_control(
		$id,
		array(
			'label'   => $label,
			'section' => $section,
			'type'    => 'text',
		)
	);
}

/**
 * Register Real Estate Homepage Customizer panel and section controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function hvn_realty_customizer_register_homepage( $wp_customize ) {
	$home_id  = (int) get_option( 'page_on_front', 0 );
	$home_url = $home_id > 0 ? get_permalink( $home_id ) : home_url( '/' );

	$description = esc_html__( 'Customize each homepage section: hero map, featured listings, departments, browse properties, agents, agencies, CTA, and carousel behavior.', 'havenlytics-realty' );
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
			'title'           => esc_html__( 'Real Estate Homepage', 'havenlytics-realty' ),
			'description'     => $description,
			'priority'        => 20,
			'active_callback' => 'hvn_realty_customizer_homepage_is_active',
		)
	);

	$sections = array(
		'hvn_realty_home_general'    => array(
			'title'    => esc_html__( 'General', 'havenlytics-realty' ),
			'priority' => 5,
		),
		'hvn_realty_home_section_manager' => array(
			'title'       => esc_html__( 'Section Manager', 'havenlytics-realty' ),
			'description' => esc_html__( 'Drag and drop to reorder homepage sections.', 'havenlytics-realty' ),
			'priority'    => 6,
		),
		'hvn_realty_home_hero'       => array(
			'title'    => esc_html__( 'Hero Map', 'havenlytics-realty' ),
			'priority' => 10,
		),
		'hvn_realty_home_hero_search' => array(
			'title'       => esc_html__( 'Hero Search', 'havenlytics-realty' ),
			'description' => esc_html__( 'Customize hero search copy and department tabs.', 'havenlytics-realty' ),
			'priority'    => 11,
		),
		'hvn_realty_home_hero_search_position' => array(
			'title'       => esc_html__( 'Hero Search Position', 'havenlytics-realty' ),
			'description' => esc_html__( 'Reposition the hero property search panel within the map area. Desktop and tablet only; mobile layout is unchanged.', 'havenlytics-realty' ),
			'priority'    => 12,
		),
		'hvn_realty_home_featured'   => array(
			'title'    => esc_html__( 'Featured Properties', 'havenlytics-realty' ),
			'priority' => 20,
		),
		'hvn_realty_home_department' => array(
			'title'    => esc_html__( 'Departments', 'havenlytics-realty' ),
			'priority' => 30,
		),
		'hvn_realty_home_taxonomies' => array(
			'title'    => esc_html__( 'Browse Properties', 'havenlytics-realty' ),
			'priority' => 40,
		),
		'hvn_realty_home_property_types' => array(
			'title'    => esc_html__( 'Explore by Property Type', 'havenlytics-realty' ),
			'priority' => 45,
		),
		'hvn_realty_home_agents'     => array(
			'title'    => esc_html__( 'Agents', 'havenlytics-realty' ),
			'priority' => 50,
		),
		'hvn_realty_home_agencies'   => array(
			'title'    => esc_html__( 'Agencies', 'havenlytics-realty' ),
			'priority' => 60,
		),
		'hvn_realty_home_blog'       => array(
			'title'    => esc_html__( 'Blog', 'havenlytics-realty' ),
			'priority' => 65,
		),
		'hvn_realty_home_testimonials' => array(
			'title'    => esc_html__( 'Testimonials', 'havenlytics-realty' ),
			'priority' => 67,
		),
		'hvn_realty_home_cta'        => array(
			'title'    => esc_html__( 'Call to Action', 'havenlytics-realty' ),
			'priority' => 70,
		),
		'hvn_realty_home_carousel'   => array(
			'title'       => esc_html__( 'Global Carousel', 'havenlytics-realty' ),
			'description' => esc_html__( 'Shared carousel behavior for featured properties, agents, and agencies.', 'havenlytics-realty' ),
			'priority'    => 80,
		),
	);

	foreach ( $sections as $id => $args ) {
		$active_callback = 'hvn_realty_customizer_homepage_is_active';
		if ( in_array( $id, array( 'hvn_realty_home_hero_search', 'hvn_realty_home_hero_search_position' ), true ) ) {
			$active_callback = 'hvn_realty_customizer_hero_search_position_is_active';
		}

		$wp_customize->add_section(
			$id,
			array(
				'title'           => $args['title'],
				'description'     => $args['description'] ?? '',
				'panel'           => HVN_REALTY_HOMEPAGE_PANEL,
				'priority'        => $args['priority'],
				'active_callback' => $active_callback,
			)
		);
	}

	hvn_realty_customizer_register_home_general( $wp_customize );
	hvn_realty_customizer_register_home_section_manager( $wp_customize );
	hvn_realty_customizer_register_home_hero( $wp_customize );
	hvn_realty_customizer_register_home_hero_search( $wp_customize );
	hvn_realty_customizer_register_home_hero_search_position( $wp_customize );
	hvn_realty_customizer_register_home_featured( $wp_customize );
	hvn_realty_customizer_register_home_department( $wp_customize );
	hvn_realty_customizer_register_home_taxonomies( $wp_customize );
	hvn_realty_customizer_register_home_property_types( $wp_customize );
	hvn_realty_customizer_register_home_agents( $wp_customize );
	hvn_realty_customizer_register_home_agencies( $wp_customize );
	hvn_realty_customizer_register_home_blog( $wp_customize );
	hvn_realty_customizer_register_home_testimonials( $wp_customize );
	hvn_realty_customizer_register_home_cta( $wp_customize );
	hvn_realty_customizer_register_home_carousel( $wp_customize );
}
add_action( 'customize_register', 'hvn_realty_customizer_register_homepage', 16 );

/**
 * Section Manager controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_section_manager( $wp_customize ) {
	$section = 'hvn_realty_home_section_manager';

	$wp_customize->add_setting(
		'hvn_realty_home_section_order',
		array(
			'default'           => '',
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
					'label'       => esc_html__( 'Section order', 'havenlytics-realty' ),
					'description' => esc_html__( 'Reorder homepage sections. Disabled sections still respect this order when enabled.', 'havenlytics-realty' ),
					'section'     => $section,
				)
			)
		);
	}
}

/**
 * General homepage controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_general( $wp_customize ) {
	$section = 'hvn_realty_home_general';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_auto_setup',
		$section,
		esc_html__( 'Auto-configure homepage after demo import', 'havenlytics-realty' ),
		true,
		'refresh'
	);

	foreach ( array( 'statistics', 'footer_cta' ) as $slug ) {
		hvn_realty_customizer_add_checkbox(
			$wp_customize,
			'hvn_realty_home_show_' . $slug,
			$section,
			sprintf(
				/* translators: %s: section label */
				esc_html__( 'Show %s section', 'havenlytics-realty' ),
				esc_html( hvn_realty_get_home_section_labels()[ $slug ] ?? $slug )
			),
			false,
			'postMessage'
		);
	}

	$wp_customize->add_setting(
		'hvn_realty_home_footer_cta_text',
		array(
			'default'           => __( 'Start your property search today.', 'havenlytics-realty' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_footer_cta_text',
		array(
			'label'   => esc_html__( 'Footer CTA text (optional section)', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'text',
		)
	);
}

/**
 * Hero map section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_hero( $wp_customize ) {
	$section = 'hvn_realty_home_hero';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_hero_map',
		$section,
		esc_html__( 'Show hero map section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_search_display',
		array(
			'default'           => 'header',
			'sanitize_callback' => 'hvn_realty_sanitize_hero_search_display',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_search_display',
		array(
			'label'       => esc_html__( 'Hero search display', 'havenlytics-realty' ),
			'description' => esc_html__( 'Choose where the property search form appears on the homepage.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'radio',
			'choices'     => array(
				'header' => esc_html__( 'Header search only', 'havenlytics-realty' ),
				'hero'   => esc_html__( 'Hero search panel', 'havenlytics-realty' ),
				'both'   => esc_html__( 'Both header and hero search', 'havenlytics-realty' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_map_posts',
		array(
			'default'           => 500,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_map_posts',
		array(
			'label'       => esc_html__( 'Map marker property limit', 'havenlytics-realty' ),
			'description' => esc_html__( 'Number of properties loaded on the homepage map.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 12,
				'max'  => 500,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_height',
		array(
			'default'           => 70,
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_height',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_height',
		array(
			'label'       => esc_html__( 'Map height — desktop (vh)', 'havenlytics-realty' ),
			'description' => esc_html__( 'Viewport height of the hero map on large screens (40–100).', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 40,
				'max'  => 100,
				'step' => 5,
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_hero_height_mobile',
		array(
			'default'           => 50,
			'sanitize_callback' => 'hvn_realty_sanitize_home_hero_height',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_hero_height_mobile',
		array(
			'label'       => esc_html__( 'Map height — mobile (vh)', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 40,
				'max'  => 100,
				'step' => 5,
			),
		)
	);

	hvn_realty_customizer_register_home_hero_map_departments( $wp_customize );
}

/**
 * Hero search content and department tab controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_hero_search( $wp_customize ) {
	$section = 'hvn_realty_home_hero_search';

	$wp_customize->add_setting(
		'hvn_realty_hero_search_title',
		array(
			'default'           => __( 'Find your next property', 'havenlytics-realty' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_title',
		array(
			'label'   => esc_html__( 'Hero title', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_hero_search_subtitle',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_subtitle',
		array(
			'label'       => esc_html__( 'Hero subtitle', 'havenlytics-realty' ),
			'description' => esc_html__( 'Optional text below the hero title.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_hero_search_button_text',
		array(
			'default'           => __( 'Search properties', 'havenlytics-realty' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_button_text',
		array(
			'label'   => esc_html__( 'Search button label', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_hero_search_tabs_label',
		array(
			'default'           => __( 'Browse by Department', 'havenlytics-realty' ),
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_tabs_label',
		array(
			'label'   => esc_html__( 'Department tabs label', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'text',
		)
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_show_hero_department_tabs',
		$section,
		esc_html__( 'Show department tabs', 'havenlytics-realty' ),
		true,
		'postMessage'
	);
}

/**
 * Hero search panel position controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_hero_search_position( $wp_customize ) {
	$section = 'hvn_realty_home_hero_search_position';

	$wp_customize->add_setting(
		'hvn_realty_hero_search_horizontal',
		array(
			'default'           => 'left',
			'sanitize_callback' => 'hvn_realty_sanitize_hero_search_horizontal',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_horizontal',
		array(
			'label'   => esc_html__( 'Horizontal position', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'radio',
			'choices' => array(
				'left'   => esc_html__( 'Left', 'havenlytics-realty' ),
				'center' => esc_html__( 'Center', 'havenlytics-realty' ),
				'right'  => esc_html__( 'Right', 'havenlytics-realty' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_hero_search_vertical',
		array(
			'default'           => 'center',
			'sanitize_callback' => 'hvn_realty_sanitize_hero_search_vertical',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_vertical',
		array(
			'label'   => esc_html__( 'Vertical position', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'radio',
			'choices' => array(
				'top'    => esc_html__( 'Top', 'havenlytics-realty' ),
				'center' => esc_html__( 'Center', 'havenlytics-realty' ),
				'bottom' => esc_html__( 'Bottom', 'havenlytics-realty' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_hero_search_offset_x',
		array(
			'default'           => 0,
			'sanitize_callback' => 'hvn_realty_sanitize_hero_search_offset',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_offset_x',
		array(
			'label'       => esc_html__( 'Horizontal offset (px)', 'havenlytics-realty' ),
			'description' => esc_html__( 'Fine-tune left/right placement (0–100).', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_hero_search_offset_y',
		array(
			'default'           => 0,
			'sanitize_callback' => 'hvn_realty_sanitize_hero_search_offset',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_offset_y',
		array(
			'label'       => esc_html__( 'Vertical offset (px)', 'havenlytics-realty' ),
			'description' => esc_html__( 'Fine-tune up/down placement (0–100).', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_hero_search_width',
		array(
			'default'           => 400,
			'sanitize_callback' => 'hvn_realty_sanitize_hero_search_width',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_hero_search_width',
		array(
			'label'       => esc_html__( 'Search panel width (px)', 'havenlytics-realty' ),
			'description' => esc_html__( 'Desktop only (400–700). Tablet and mobile use responsive widths.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 400,
				'max'  => 700,
				'step' => 10,
			),
		)
	);
}

/**
 * Hero map department visibility controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @return void
 */
function hvn_realty_customizer_register_home_hero_map_departments( $wp_customize ) {
	$section = 'hvn_realty_home_hero';

	$wp_customize->add_setting(
		'hvn_realty_home_map_department_mode',
		array(
			'default'           => 'all',
			'sanitize_callback' => 'hvn_realty_sanitize_map_department_mode',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_map_department_mode',
		array(
			'label'       => esc_html__( 'Map departments', 'havenlytics-realty' ),
			'description' => esc_html__( 'Choose whether the hero map shows all departments or only selected ones.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'radio',
			'choices'     => array(
				'all'      => esc_html__( 'All departments', 'havenlytics-realty' ),
				'selected' => esc_html__( 'Selected departments only', 'havenlytics-realty' ),
			),
		)
	);

	if ( ! function_exists( 'hvn_realty_get_property_departments' ) ) {
		return;
	}

	$departments = hvn_realty_get_property_departments();
	if ( empty( $departments ) ) {
		return;
	}

	foreach ( $departments as $term ) {
		$setting_id = 'hvn_realty_home_map_dept_' . $term->slug;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => false,
				'sanitize_callback' => 'hvn_realty_sanitize_checkbox',
				'transport'         => 'refresh',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				/* translators: %s: property department name */
				'label'           => sprintf( esc_html__( 'Show %s on map', 'havenlytics-realty' ), $term->name ),
				'section'         => $section,
				'type'            => 'checkbox',
				'active_callback' => 'hvn_realty_customizer_map_department_checkbox_active',
			)
		);
	}
}

/**
 * Whether individual map department checkboxes are visible.
 *
 * @return bool
 */
function hvn_realty_customizer_map_department_checkbox_active() {
	return 'selected' === get_theme_mod( 'hvn_realty_home_map_department_mode', 'all' );
}

/**
 * Featured properties section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_featured( $wp_customize ) {
	$section = 'hvn_realty_home_featured';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_featured_properties',
		$section,
		esc_html__( 'Show featured properties section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_featured_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'Featured Properties', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_featured_subtitle',
		$section,
		esc_html__( 'Section subtitle', 'havenlytics-realty' ),
		''
	);

	$wp_customize->add_setting(
		'hvn_realty_home_featured_count',
		array(
			'default'           => 12,
			'sanitize_callback' => 'hvn_realty_sanitize_home_property_count',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_featured_count',
		array(
			'label'       => esc_html__( 'Properties in carousel', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 4,
				'max'  => 24,
				'step' => 1,
			),
		)
	);
}

/**
 * Department tabs section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_department( $wp_customize ) {
	$section = 'hvn_realty_home_department';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_latest_properties',
		$section,
		esc_html__( 'Show departments section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_department_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'Browse by Department', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_department_subtitle',
		$section,
		esc_html__( 'Section subtitle', 'havenlytics-realty' ),
		__( 'Explore listings organized by property department.', 'havenlytics-realty' )
	);

	$wp_customize->add_setting(
		'hvn_realty_home_latest_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'hvn_realty_sanitize_home_property_count',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_latest_count',
		array(
			'label'       => esc_html__( 'Properties per department tab', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 6,
				'max'  => 12,
				'step' => 1,
			),
		)
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_department_button_text',
		$section,
		esc_html__( 'Footer button text', 'havenlytics-realty' ),
		__( 'View All Properties', 'havenlytics-realty' )
	);

	$wp_customize->add_setting(
		'hvn_realty_home_department_button_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'hvn_realty_sanitize_url_or_path',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_department_button_url',
		array(
			'label'       => esc_html__( 'Footer button link', 'havenlytics-realty' ),
			'description' => esc_html__( 'Full URL, path (e.g. /properties/), or page slug. Leave empty for the property search page.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'text',
		)
	);
}

/**
 * Browse Properties (Property Taxonomies) section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_taxonomies( $wp_customize ) {
	$section = 'hvn_realty_home_taxonomies';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_property_taxonomies',
		$section,
		esc_html__( 'Show Browse Properties section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_taxonomies_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'Property Locations', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_taxonomies_subtitle',
		$section,
		esc_html__( 'Section description', 'havenlytics-realty' ),
		__( 'Explore listings by city and region.', 'havenlytics-realty' )
	);

	$source_choices = array();
	if ( function_exists( 'hvn_realty_get_home_taxonomy_sources' ) ) {
		foreach ( hvn_realty_get_home_taxonomy_sources() as $key => $config ) {
			$source_choices[ $key ] = $config['label'] ?? ucfirst( $key );
		}
	} else {
		$source_choices = array( 'locations' => __( 'Locations', 'havenlytics-realty' ) );
	}

	$wp_customize->add_setting(
		'hvn_realty_home_taxonomies_source',
		array(
			'default'           => 'locations',
			'sanitize_callback' => 'hvn_realty_sanitize_home_taxonomy_source',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_taxonomies_source',
		array(
			'label'       => esc_html__( 'Taxonomy source', 'havenlytics-realty' ),
			'description' => esc_html__( 'Choose which Havenlytics taxonomy powers this section.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'select',
			'choices'     => $source_choices,
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_taxonomies_count',
		array(
			'default'           => 8,
			'sanitize_callback' => 'hvn_realty_sanitize_home_taxonomies_count',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_taxonomies_count',
		array(
			'label'       => esc_html__( 'Items per page', 'havenlytics-realty' ),
			'description' => esc_html__( 'Number of taxonomy terms to display (4–24).', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 4,
				'max'  => 24,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_taxonomies_columns',
		array(
			'default'           => 4,
			'sanitize_callback' => 'hvn_realty_sanitize_home_taxonomies_columns',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_taxonomies_columns',
		array(
			'label'       => esc_html__( 'Columns', 'havenlytics-realty' ),
			'description' => esc_html__( 'Desktop grid columns (2–6).', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 2,
				'max'  => 6,
				'step' => 1,
			),
		)
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_taxonomy_counts',
		$section,
		esc_html__( 'Show counts', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_taxonomy_icons',
		$section,
		esc_html__( 'Show icons', 'havenlytics-realty' ),
		true,
		'refresh'
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_taxonomy_images',
		$section,
		esc_html__( 'Show images', 'havenlytics-realty' ),
		true,
		'refresh'
	);
}

/**
 * @deprecated Use hvn_realty_customizer_register_home_taxonomies().
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_locations( $wp_customize ) {
	hvn_realty_customizer_register_home_taxonomies( $wp_customize );
}

/**
 * Explore by Property Type section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_property_types( $wp_customize ) {
	$section = 'hvn_realty_home_property_types';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_property_types',
		$section,
		esc_html__( 'Enable section', 'havenlytics-realty' ),
		false,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_property_types_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'Explore by Property Type', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_property_types_subtitle',
		$section,
		esc_html__( 'Section subtitle', 'havenlytics-realty' ),
		__( 'Browse listings by home style and category.', 'havenlytics-realty' )
	);

	$wp_customize->add_setting(
		'hvn_realty_home_property_types_count',
		array(
			'default'           => 8,
			'sanitize_callback' => 'hvn_realty_sanitize_home_taxonomies_count',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_property_types_count',
		array(
			'label'       => esc_html__( 'Items count', 'havenlytics-realty' ),
			'description' => esc_html__( 'Number of property types to display (4–24).', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 4,
				'max'  => 24,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_property_types_columns',
		array(
			'default'           => 4,
			'sanitize_callback' => 'hvn_realty_sanitize_home_property_types_columns',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_property_types_columns',
		array(
			'label'       => esc_html__( 'Columns', 'havenlytics-realty' ),
			'description' => esc_html__( 'Desktop grid columns (2–4).', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 2,
				'max'  => 4,
				'step' => 1,
			),
		)
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_property_type_counts',
		$section,
		esc_html__( 'Show counts', 'havenlytics-realty' ),
		true,
		'refresh'
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_property_type_icons',
		$section,
		esc_html__( 'Show icons', 'havenlytics-realty' ),
		true,
		'refresh'
	);
}

/**
 * Sanitize property type grid columns (2–4).
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_home_property_types_columns( $input ) {
	$input = absint( $input );

	if ( $input < 2 ) {
		return 2;
	}

	if ( $input > 4 ) {
		return 4;
	}

	return $input;
}

/**
 * Agents section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_agents( $wp_customize ) {
	$section = 'hvn_realty_home_agents';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_featured_agents',
		$section,
		esc_html__( 'Show agents section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_agents_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'Our Agents', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_agents_subtitle',
		$section,
		esc_html__( 'Section subtitle', 'havenlytics-realty' ),
		__( 'Connect with experienced local professionals.', 'havenlytics-realty' )
	);
}

/**
 * Agencies section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_agencies( $wp_customize ) {
	$section = 'hvn_realty_home_agencies';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_featured_agencies',
		$section,
		esc_html__( 'Show agencies section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_agencies_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'Agencies', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_agencies_subtitle',
		$section,
		esc_html__( 'Section subtitle', 'havenlytics-realty' ),
		__( 'Trusted brokerages and property firms.', 'havenlytics-realty' )
	);
}

/**
 * Blog carousel section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_blog( $wp_customize ) {
	$section = 'hvn_realty_home_blog';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_latest_posts',
		$section,
		esc_html__( 'Show blog carousel section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_blog_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'Latest from the Blog', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_blog_subtitle',
		$section,
		esc_html__( 'Section subtitle', 'havenlytics-realty' ),
		__( 'Market insights, tips, and industry news.', 'havenlytics-realty' )
	);

	$wp_customize->add_setting(
		'hvn_realty_home_posts_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'hvn_realty_sanitize_home_posts_count',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_posts_count',
		array(
			'label'       => esc_html__( 'Posts in carousel', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 1,
				'max'  => 12,
				'step' => 1,
			),
		)
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_blog_autoplay',
		$section,
		esc_html__( 'Autoplay blog carousel', 'havenlytics-realty' ),
		true,
		'refresh'
	);
}

/**
 * Testimonials section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_testimonials( $wp_customize ) {
	$section = 'hvn_realty_home_testimonials';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_testimonials',
		$section,
		esc_html__( 'Enable section', 'havenlytics-realty' ),
		false,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_testimonials_title',
		$section,
		esc_html__( 'Section title', 'havenlytics-realty' ),
		__( 'What Our Clients Say', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_testimonials_subtitle',
		$section,
		esc_html__( 'Subtitle', 'havenlytics-realty' ),
		__( 'Real stories from buyers, sellers, and renters.', 'havenlytics-realty' )
	);

	$default_testimonials = function_exists( 'hvn_realty_get_default_home_testimonials' )
		? wp_json_encode( hvn_realty_get_default_home_testimonials() )
		: '[]';

	$repeater_description = function_exists( 'hvn_realty_plugin_reviews_are_available' ) && hvn_realty_plugin_reviews_are_available()
		? esc_html__( 'Plugin reviews are active and will replace these testimonials on the homepage.', 'havenlytics-realty' )
		: esc_html__( 'Add client reviews. A minimum of three testimonials is recommended.', 'havenlytics-realty' );

	$wp_customize->add_setting(
		'hvn_realty_home_testimonials',
		array(
			'default'           => $default_testimonials,
			'sanitize_callback' => 'hvn_realty_sanitize_home_testimonials',
			'transport'         => 'refresh',
		)
	);
	if ( class_exists( 'HVN_Realty_Customize_Testimonials_Control' ) ) {
		$wp_customize->add_control(
			new HVN_Realty_Customize_Testimonials_Control(
				$wp_customize,
				'hvn_realty_home_testimonials',
				array(
					'label'       => esc_html__( 'Testimonials', 'havenlytics-realty' ),
					'description' => $repeater_description,
					'section'     => $section,
				)
			)
		);
	}

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_testimonials_autoplay',
		$section,
		esc_html__( 'Autoplay', 'havenlytics-realty' ),
		true,
		'refresh'
	);

	$wp_customize->add_setting(
		'hvn_realty_home_testimonials_speed',
		array(
			'default'           => 5000,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_testimonials_speed',
		array(
			'label'       => esc_html__( 'Autoplay speed (ms)', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 2000,
				'max'  => 15000,
				'step' => 500,
			),
		)
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_testimonial_stars',
		$section,
		esc_html__( 'Show stars', 'havenlytics-realty' ),
		true,
		'refresh'
	);
}

/**
 * CTA banner section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_cta( $wp_customize ) {
	$section = 'hvn_realty_home_cta';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_show_cta_banner',
		$section,
		esc_html__( 'Show CTA banner section', 'havenlytics-realty' ),
		true,
		'postMessage'
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_cta_headline',
		$section,
		esc_html__( 'Headline', 'havenlytics-realty' ),
		__( 'Ready to find your next property?', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_cta_subtext',
		$section,
		esc_html__( 'Supporting text', 'havenlytics-realty' ),
		__( 'Browse listings or connect with an agent today.', 'havenlytics-realty' )
	);

	hvn_realty_customizer_add_text(
		$wp_customize,
		'hvn_realty_home_cta_primary_text',
		$section,
		esc_html__( 'Primary button text', 'havenlytics-realty' ),
		__( 'Browse Listings', 'havenlytics-realty' )
	);

	$wp_customize->add_setting(
		'hvn_realty_home_cta_bg_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'hvn_realty_home_cta_bg_image',
			array(
				'label'       => esc_html__( 'Background image', 'havenlytics-realty' ),
				'description' => esc_html__( 'Upload a wide photo for a full-width CTA background.', 'havenlytics-realty' ),
				'section'     => $section,
				'mime_type'   => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_cta_overlay',
		array(
			'default'           => 65,
			'sanitize_callback' => 'hvn_realty_sanitize_home_overlay_opacity',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_cta_overlay',
		array(
			'label'       => esc_html__( 'Background overlay darkness (%)', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 90,
				'step' => 5,
			),
		)
	);
}

/**
 * Global carousel section controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function hvn_realty_customizer_register_home_carousel( $wp_customize ) {
	$section = 'hvn_realty_home_carousel';

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_carousel_autoplay',
		$section,
		esc_html__( 'Autoplay featured properties carousel', 'havenlytics-realty' ),
		true,
		'refresh'
	);

	hvn_realty_customizer_add_checkbox(
		$wp_customize,
		'hvn_realty_home_carousel_card_autoplay',
		$section,
		esc_html__( 'Autoplay agents & agencies carousels', 'havenlytics-realty' ),
		false,
		'refresh'
	);

	$wp_customize->add_setting(
		'hvn_realty_home_carousel_speed',
		array(
			'default'           => 5000,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_carousel_speed',
		array(
			'label'       => esc_html__( 'Autoplay interval (ms)', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 2000,
				'max'  => 15000,
				'step' => 500,
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_home_carousel_gap',
		array(
			'default'           => 16,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_home_carousel_gap',
		array(
			'label'       => esc_html__( 'Slide gap (px)', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 8,
				'max'  => 48,
				'step' => 2,
			),
		)
	);

	$slide_controls = array(
		'hvn_realty_home_carousel_featured_desktop' => esc_html__( 'Featured slides — desktop', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_featured_tablet'  => esc_html__( 'Featured slides — tablet', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_featured_mobile'  => esc_html__( 'Featured slides — mobile', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_cards_desktop'    => esc_html__( 'Agent/agency slides — desktop', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_cards_tablet'     => esc_html__( 'Agent/agency slides — tablet', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_cards_mobile'     => esc_html__( 'Agent/agency slides — mobile', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_blog_desktop'     => esc_html__( 'Blog slides — desktop', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_blog_tablet'      => esc_html__( 'Blog slides — tablet', 'havenlytics-realty' ),
		'hvn_realty_home_carousel_blog_mobile'      => esc_html__( 'Blog slides — mobile', 'havenlytics-realty' ),
	);

	$defaults = array(
		'hvn_realty_home_carousel_featured_desktop' => 3,
		'hvn_realty_home_carousel_featured_tablet'  => 2,
		'hvn_realty_home_carousel_featured_mobile'  => 1,
		'hvn_realty_home_carousel_cards_desktop'    => 4,
		'hvn_realty_home_carousel_cards_tablet'     => 2,
		'hvn_realty_home_carousel_cards_mobile'     => 1,
		'hvn_realty_home_carousel_blog_desktop'     => 3,
		'hvn_realty_home_carousel_blog_tablet'      => 2,
		'hvn_realty_home_carousel_blog_mobile'      => 1,
	);

	foreach ( $slide_controls as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ],
				'sanitize_callback' => 'hvn_realty_sanitize_carousel_slides',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'       => $label,
				'section'     => $section,
				'type'        => 'number',
				'input_attrs' => array(
					'min'  => 1,
					'max'  => 6,
					'step' => 1,
				),
			)
		);
	}
}

/**
 * Override section defaults when Customizer has no saved value yet.
 *
 * @param array<string, bool> $defaults Section defaults.
 * @return array<string, bool>
 */
function hvn_realty_home_section_customizer_defaults( $defaults ) {
	$map = array(
		'hero-map'            => 'hero_map',
		'featured-properties' => 'featured_properties',
		'department-tabs'     => 'latest_properties',
		'property-taxonomies' => 'property_taxonomies',
		'property-locations'  => 'property_taxonomies',
		'property-categories' => 'property_taxonomies',
		'property-types'      => 'property_types',
		'featured-agents'     => 'featured_agents',
		'featured-agencies'   => 'featured_agencies',
		'latest-posts'        => 'latest_posts',
		'testimonials'        => 'testimonials',
		'statistics'          => 'statistics',
		'cta-banner'          => 'cta_banner',
		'latest-posts'        => 'latest_posts',
		'footer-cta'          => 'footer_cta',
	);

	foreach ( $map as $section => $slug ) {
		$mod = get_theme_mod( 'hvn_realty_home_show_' . $slug, null );

		if ( null === $mod && in_array( $section, array( 'property-taxonomies', 'property-locations' ), true ) ) {
			$mod = get_theme_mod( 'hvn_realty_home_show_property_taxonomies', null );
			if ( null === $mod ) {
				$mod = get_theme_mod( 'hvn_realty_home_show_property_locations', null );
			}
			if ( null === $mod && 'property-locations' === $section ) {
				$mod = get_theme_mod( 'hvn_realty_home_show_property_categories', null );
			}
		}

		if ( null !== $mod ) {
			$defaults[ $section ] = (bool) $mod;
		}
	}

	return $defaults;
}
add_filter( 'hvn_realty_home_section_defaults', 'hvn_realty_home_section_customizer_defaults', 20 );
