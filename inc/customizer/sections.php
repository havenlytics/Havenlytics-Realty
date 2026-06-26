<?php
/**
 * Customizer section and control registration.
 *
 * @package Havenlytics_Realty
 */

/**
 * Register Havenlytics Theme Settings panel and all sections.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_register_sections( $wp_customize ) {
	$wp_customize->add_panel(
		HVN_REALTY_CUSTOMIZER_PANEL,
		array(
			'title'       => esc_html__( 'Havenlytics Theme Settings', 'havenlytics-realty' ),
			'description' => esc_html__( 'Global design, header, footer, layout, typography, and real estate homepage.', 'havenlytics-realty' ),
			'priority'    => 10,
		)
	);

	$sections = array(
		'hvn_realty_global_design'       => array(
			'title'    => esc_html__( 'Global Design System', 'havenlytics-realty' ),
			'priority' => 10,
		),
		'hvn_realty_header_settings'     => array(
			'title'    => esc_html__( 'Header Settings', 'havenlytics-realty' ),
			'priority' => 30,
		),
		'hvn_realty_typography_settings' => array(
			'title'    => esc_html__( 'Typography Settings', 'havenlytics-realty' ),
			'priority' => 40,
		),
		'hvn_realty_layout_settings'     => array(
			'title'    => esc_html__( 'Layout Settings', 'havenlytics-realty' ),
			'priority' => 50,
		),
		'hvn_realty_footer_settings'     => array(
			'title'    => esc_html__( 'Footer Settings', 'havenlytics-realty' ),
			'priority' => 60,
		),
	);

	foreach ( $sections as $id => $args ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $args['title'],
				'panel'    => HVN_REALTY_CUSTOMIZER_PANEL,
				'priority' => $args['priority'],
			)
		);
	}

	hvn_realty_customizer_register_global_design( $wp_customize );
	hvn_realty_customizer_register_header( $wp_customize );
	hvn_realty_customizer_register_typography( $wp_customize );
	hvn_realty_customizer_register_layout( $wp_customize );
	hvn_realty_customizer_register_footer( $wp_customize );
}
add_action( 'customize_register', 'hvn_realty_customizer_register_sections', 15 );

/**
 * Global Design System controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_register_global_design( $wp_customize ) {
	$section = 'hvn_realty_global_design';

	if ( function_exists( 'hvn_realty_uses_plugin_design_tokens' ) && hvn_realty_uses_plugin_design_tokens() ) {
		$global_design_section = $wp_customize->get_section( $section );
		if ( $global_design_section ) {
			$global_design_section->description = esc_html__( 'Global colors are managed from: Havenlytics → Settings → Global Colors', 'havenlytics-realty' );
		}
	}

	$colors = array(
		'hvn_realty_primary_color'     => array( '#6C60FE', esc_html__( 'Primary Color', 'havenlytics-realty' ) ),
		'hvn_realty_secondary_color'   => array( '#764ba2', esc_html__( 'Secondary Color', 'havenlytics-realty' ) ),
		'hvn_realty_accent_color'      => array( '#FF9AA2', esc_html__( 'Accent Color', 'havenlytics-realty' ) ),
		'hvn_realty_text_color'        => array( '#1E1E2F', esc_html__( 'Text Color', 'havenlytics-realty' ) ),
		'hvn_realty_background_color'  => array( '#F8F8F8', esc_html__( 'Background Color', 'havenlytics-realty' ) ),
		'hvn_realty_border_color'      => array( '#E4E4ED', esc_html__( 'Border Color', 'havenlytics-realty' ) ),
	);

	foreach ( $colors as $id => $config ) {
		$is_legacy_color = function_exists( 'hvn_realty_is_legacy_color_theme_mod' ) && hvn_realty_is_legacy_color_theme_mod( $id );

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $config[0],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'       => $config[1],
					'section'     => $section,
					'settings'    => $id,
					'description' => $is_legacy_color && function_exists( 'hvn_realty_uses_plugin_design_tokens' ) && hvn_realty_uses_plugin_design_tokens()
						? esc_html__( 'Legacy theme color. The live site uses Havenlytics global colors when the plugin is active.', 'havenlytics-realty' )
						: '',
				)
			)
		);
	}

	hvn_realty_customizer_add_number(
		$wp_customize,
		'hvn_realty_container_width',
		$section,
		esc_html__( 'Container Width (px)', 'havenlytics-realty' ),
		1280,
		array( 'min' => 960, 'max' => 1920, 'step' => 10 ),
		'hvn_realty_sanitize_px'
	);

	hvn_realty_customizer_add_number(
		$wp_customize,
		'hvn_realty_border_radius',
		$section,
		esc_html__( 'Border Radius (px)', 'havenlytics-realty' ),
		8,
		array( 'min' => 0, 'max' => 50, 'step' => 1 ),
		'hvn_realty_sanitize_px'
	);
}

/**
 * Header Settings controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_register_header( $wp_customize ) {
	$section = 'hvn_realty_header_settings';

	$wp_customize->add_setting(
		'hvn_realty_header_layout',
		array(
			'default'           => '1',
			'sanitize_callback' => 'hvn_realty_sanitize_header_layout',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_header_layout',
		array(
			'label'   => esc_html__( 'Header Layout', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'1' => esc_html__( 'Logo Left / Menu Center / Actions Right', 'havenlytics-realty' ),
				'2' => esc_html__( 'Center Logo', 'havenlytics-realty' ),
				'3' => esc_html__( 'Logo Left / Menu Right / CTA Right', 'havenlytics-realty' ),
			),
		)
	);

	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_sticky_header', $section, esc_html__( 'Enable Sticky Header', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_show_header_search', $section, esc_html__( 'Show Search Icon', 'havenlytics-realty' ), true, 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_show_header_cta', $section, esc_html__( 'Show CTA Button', 'havenlytics-realty' ), false, 'postMessage' );

	$wp_customize->add_setting(
		'hvn_realty_header_cta_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_header_cta_text',
		array(
			'label'   => esc_html__( 'CTA Button Text', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'text',
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_header_cta_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'hvn_realty_sanitize_url',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_header_cta_url',
		array(
			'label'       => esc_html__( 'CTA Button Link', 'havenlytics-realty' ),
			'description' => esc_html__( 'Leave empty to use the site home URL.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'url',
		)
	);
}

/**
 * Footer Settings controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_register_footer( $wp_customize ) {
	$section = 'hvn_realty_footer_settings';

	$wp_customize->add_setting(
		'hvn_realty_footer_columns',
		array(
			'default'           => 4,
			'sanitize_callback' => 'hvn_realty_sanitize_footer_columns',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_footer_columns',
		array(
			'label'   => esc_html__( 'Footer Columns', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'1' => esc_html__( '1 Column', 'havenlytics-realty' ),
				'2' => esc_html__( '2 Columns', 'havenlytics-realty' ),
				'3' => esc_html__( '3 Columns', 'havenlytics-realty' ),
				'4' => esc_html__( '4 Columns', 'havenlytics-realty' ),
			),
		)
	);

	foreach ( array(
		'hvn_realty_footer_bg_color'   => array( '#212529', esc_html__( 'Footer Background Color', 'havenlytics-realty' ) ),
		'hvn_realty_footer_text_color' => array( '#adb5bd', esc_html__( 'Footer Text Color', 'havenlytics-realty' ) ),
		'hvn_realty_footer_link_color' => array( '#dee2e6', esc_html__( 'Footer Link Color', 'havenlytics-realty' ) ),
	) as $id => $config ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $config[0],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'    => $config[1],
					'section'  => $section,
					'settings' => $id,
				)
			)
		);
	}

	$wp_customize->add_setting(
		'hvn_realty_copyright_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_copyright_text',
		array(
			'label'       => esc_html__( 'Copyright Text', 'havenlytics-realty' ),
			'description' => esc_html__( 'Leave empty for the default copyright line.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'textarea',
		)
	);

	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_show_back_to_top', $section, esc_html__( 'Show Back To Top Button', 'havenlytics-realty' ), false, 'postMessage' );
}

/**
 * Layout Settings controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_register_layout( $wp_customize ) {
	$section = 'hvn_realty_layout_settings';

	$wp_customize->add_setting(
		'hvn_realty_blog_layout',
		array(
			'default'           => 'grid',
			'sanitize_callback' => 'hvn_realty_sanitize_blog_layout',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_blog_layout',
		array(
			'label'   => esc_html__( 'Blog Layout', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'grid' => esc_html__( 'Grid', 'havenlytics-realty' ),
				'list' => esc_html__( 'List', 'havenlytics-realty' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_sidebar_position',
		array(
			'default'           => 'none',
			'sanitize_callback' => 'hvn_realty_sanitize_sidebar_position',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_sidebar_position',
		array(
			'label'   => esc_html__( 'Sidebar Position', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'right' => esc_html__( 'Right', 'havenlytics-realty' ),
				'left'  => esc_html__( 'Left', 'havenlytics-realty' ),
				'none'  => esc_html__( 'None', 'havenlytics-realty' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_container_mode',
		array(
			'default'           => 'boxed',
			'sanitize_callback' => 'hvn_realty_sanitize_container_mode',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_container_mode',
		array(
			'label'   => esc_html__( 'Container Width Mode', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'boxed' => esc_html__( 'Boxed', 'havenlytics-realty' ),
				'full'  => esc_html__( 'Full Width', 'havenlytics-realty' ),
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_blog_columns',
		array(
			'default'           => 3,
			'sanitize_callback' => 'hvn_realty_sanitize_blog_columns',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_blog_columns',
		array(
			'label'   => esc_html__( 'Posts Per Row', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
			),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_posts_per_page',
		array(
			'default'           => '',
			'sanitize_callback' => 'hvn_realty_sanitize_posts_per_page',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_posts_per_page',
		array(
			'label'       => esc_html__( 'Posts Per Page', 'havenlytics-realty' ),
			'description' => esc_html__( 'Leave empty to use WordPress Reading settings.', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 1, 'max' => 50, 'step' => 1 ),
		)
	);

	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_ignore_sticky_posts', $section, esc_html__( 'Ignore Sticky Posts', 'havenlytics-realty' ), false );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_show_preloader', $section, esc_html__( 'Show page preloader', 'havenlytics-realty' ), true, 'refresh' );
}

/**
 * Typography Settings controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_register_typography( $wp_customize ) {
	$section = 'hvn_realty_typography_settings';
	$fonts   = hvn_realty_get_font_choices();

	foreach ( array(
		'hvn_realty_body_font_family'    => esc_html__( 'Body Font Family', 'havenlytics-realty' ),
		'hvn_realty_heading_font_family' => esc_html__( 'Heading Font Family', 'havenlytics-realty' ),
	) as $id => $label ) {
		$default = ( 'hvn_realty_body_font_family' === $id ) ? 'inter' : 'poppins';
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $default,
				'sanitize_callback' => 'hvn_realty_sanitize_font_choice',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => 'select',
				'choices' => $fonts,
			)
		);
	}

	hvn_realty_customizer_add_number(
		$wp_customize,
		'hvn_realty_body_font_size',
		$section,
		esc_html__( 'Base Font Size (px)', 'havenlytics-realty' ),
		16,
		array( 'min' => 12, 'max' => 24, 'step' => 1 ),
		'absint'
	);

	$wp_customize->add_setting(
		'hvn_realty_line_height',
		array(
			'default'           => '1.5',
			'sanitize_callback' => 'hvn_realty_sanitize_line_height',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_line_height',
		array(
			'label'       => esc_html__( 'Line Height', 'havenlytics-realty' ),
			'section'     => $section,
			'type'        => 'number',
			'input_attrs' => array( 'min' => 1, 'max' => 2.5, 'step' => 0.1 ),
		)
	);

	$wp_customize->add_setting(
		'hvn_realty_heading_scale',
		array(
			'default'           => 'medium',
			'sanitize_callback' => 'hvn_realty_sanitize_heading_scale',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		'hvn_realty_heading_scale',
		array(
			'label'   => esc_html__( 'Heading Scale (H1–H6)', 'havenlytics-realty' ),
			'section' => $section,
			'type'    => 'select',
			'choices' => array(
				'small'  => esc_html__( 'Small', 'havenlytics-realty' ),
				'medium' => esc_html__( 'Medium', 'havenlytics-realty' ),
				'large'  => esc_html__( 'Large', 'havenlytics-realty' ),
			),
		)
	);

	$weight_choices = hvn_realty_get_font_weight_choices();
	$weight_labels  = array(
		'h1' => esc_html__( 'H1 Weight', 'havenlytics-realty' ),
		'h2' => esc_html__( 'H2 Weight', 'havenlytics-realty' ),
		'h3' => esc_html__( 'H3 Weight', 'havenlytics-realty' ),
		'h4' => esc_html__( 'H4 Weight', 'havenlytics-realty' ),
		'h5' => esc_html__( 'H5 Weight', 'havenlytics-realty' ),
		'h6' => esc_html__( 'H6 Weight', 'havenlytics-realty' ),
	);

	foreach ( hvn_realty_get_heading_weight_defaults() as $level => $default ) {
		$setting_id = 'hvn_realty_' . $level . '_weight';

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $default,
				'sanitize_callback' => 'hvn_realty_sanitize_font_weight_choice',
				'transport'         => 'postMessage',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $weight_labels[ $level ],
				'section' => $section,
				'type'    => 'select',
				'choices' => $weight_choices,
			)
		);
	}
}

/**
 * Register a number control.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $id          Setting ID.
 * @param string               $section     Section ID.
 * @param string               $label       Label.
 * @param mixed                $default     Default.
 * @param array                $attrs       Input attributes.
 * @param callable|string      $sanitize    Sanitize callback.
 */
function hvn_realty_customizer_add_number( $wp_customize, $id, $section, $label, $default, $attrs, $sanitize ) {
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
			'input_attrs' => $attrs,
		)
	);
}

/**
 * Register a checkbox control.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $id          Setting ID.
 * @param string               $section     Section ID.
 * @param string               $label       Label.
 * @param bool                 $default     Default.
 * @param string               $transport   Transport mode.
 */
function hvn_realty_customizer_add_checkbox( $wp_customize, $id, $section, $label, $default, $transport = 'refresh' ) {
	$wp_customize->add_setting(
		$id,
		array(
			'default'           => $default,
			'sanitize_callback' => 'hvn_realty_sanitize_checkbox',
			'transport'         => $transport,
		)
	);
	$wp_customize->add_control(
		$id,
		array(
			'label'   => $label,
			'section' => $section,
			'type'    => 'checkbox',
		)
	);
}
