<?php
/**
 * Havenlytics Realty Theme Customizer — core WordPress settings only.
 *
 * All theme options live under Appearance → Customize → Havenlytics Theme Settings.
 *
 * @package Havenlytics_Realty
 */

/**
 * PostMessage support for site identity fields.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function hvn_realty_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( $wp_customize->get_setting( 'custom_logo' ) ) {
		$wp_customize->get_setting( 'custom_logo' )->transport = 'postMessage';
	}

	if ( $wp_customize->get_setting( 'site_icon' ) ) {
		$wp_customize->get_setting( 'site_icon' )->transport = 'postMessage';
	}

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.hvn-theme-site-title a',
				'render_callback' => 'hvn_realty_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.hvn-theme-site-description',
				'render_callback' => 'hvn_realty_customize_partial_blogdescription',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'custom_logo',
			array(
				'selector'            => '.hvn-theme-logo',
				'container_inclusive' => true,
				'render_callback'     => 'hvn_realty_customize_partial_logo',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'custom_logo_mobile_branding',
			array(
				'settings'            => array( 'custom_logo' ),
				'selector'            => '.hvn-theme-mobile-menu-branding',
				'container_inclusive' => true,
				'render_callback'     => 'hvn_realty_customize_partial_logo',
			)
		);
	}
}
add_action( 'customize_register', 'hvn_realty_customize_register' );

/**
 * Sanitize sidebar position.
 *
 * @param string $input Value to sanitize.
 * @return string
 */
function hvn_realty_sanitize_sidebar_position( $input ) {
	$valid = array( 'right', 'left', 'none' );
	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}
	return 'none';
}
/**
 * Sanitize blog layout.
 *
 * @param string $input Value to sanitize.
 * @return string
 */
function hvn_realty_sanitize_blog_layout( $input ) {
	$valid = array( 'grid', 'list' );
	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}
	return 'grid';
}

/**
 * Sanitize blog columns.
 *
 * @param int|string $input Value to sanitize.
 * @return int
 */
function hvn_realty_sanitize_blog_columns( $input ) {
	$input = intval( $input );
	$valid = array( 1, 2, 3, 4 );
	if ( in_array( $input, $valid, true ) ) {
		return $input;
	}
	return 3;
}

/**
 * Sanitize posts per page.
 *
 * @param int $input Value to sanitize.
 * @return int|string
 */
function hvn_realty_sanitize_posts_per_page( $input ) {
	if ( empty( $input ) ) {
		return '';
	}
	$input = absint( $input );
	if ( $input < 1 ) {
		return '';
	}
	if ( $input > 50 ) {
		return 50;
	}
	return $input;
}

/**
 * Sanitize checkbox.
 *
 * @param bool $input Value to sanitize.
 * @return bool
 */
function hvn_realty_sanitize_checkbox( $input ) {
	return (bool) $input;
}

/**
 * Sanitize hero map department mode.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_map_department_mode( $input ) {
	return in_array( $input, array( 'all', 'selected' ), true ) ? $input : 'all';
}

/**
 * Sanitize float number.
 *
 * @param float $input Value to sanitize.
 * @return float
 */
function hvn_realty_sanitize_float( $input ) {
	return floatval( $input );
}

/**
 * Render the site title for selective refresh.
 */
function hvn_realty_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for selective refresh.
 */
function hvn_realty_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Render site logo / branding for selective refresh.
 */
function hvn_realty_customize_partial_logo() {
	get_template_part( 'template-parts/header/branding' );
}

/**
 * Customizer preview scripts.
 */
function hvn_realty_customize_preview_js() {
	if ( ! hvn_realty_enqueue_script_safe(
		'hvn-realty-customizer',
		'assets/js/customizer.js',
		array( 'customize-preview', 'jquery' ),
		false,
		true,
		true
	) ) {
		return;
	}

	$blog_url = home_url( '/' );
	$page_for_posts = (int) get_option( 'page_for_posts' );
	if ( $page_for_posts ) {
		$permalink = get_permalink( $page_for_posts );
		if ( is_string( $permalink ) && $permalink !== '' ) {
			$blog_url = $permalink;
		}
	}

	$localize = array(
		'fontStacks'       => function_exists( 'hvn_realty_get_font_stack_map' ) ? hvn_realty_get_font_stack_map() : array(),
		'googleFonts'      => function_exists( 'hvn_realty_get_google_fonts_api_map' ) ? hvn_realty_get_google_fonts_api_map() : array(),
		'defaultCopyright' => sprintf(
			/* translators: 1: Year, 2: Site name */
			esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'havenlytics-realty' ),
			date_i18n( 'Y' ),
			get_bloginfo( 'name' )
		),
		'blogPreviewUrl'   => esc_url( $blog_url ),
	);

	if ( function_exists( 'hvn_realty_get_property_search_url' ) ) {
		$localize['propertySearchUrl'] = esc_url( hvn_realty_get_property_search_url() );
	}

	$localize['homeUrl'] = esc_url( home_url( '/' ) );

	if ( function_exists( 'hvn_realty_get_customizer_home_section_selectors' ) ) {
		$localize['homeSections'] = hvn_realty_get_customizer_home_section_selectors();
	}

	if ( function_exists( 'hvn_realty_get_customizer_home_section_visibility_map' ) ) {
		$localize['homeSectionVisibility'] = hvn_realty_get_customizer_home_section_visibility_map();
	}

	if ( function_exists( 'hvn_realty_get_home_section_registry' ) ) {
		$localize['homeSectionSlugs'] = array_keys( hvn_realty_get_home_section_registry() );
		$selectors = array();
		foreach ( array_keys( hvn_realty_get_home_section_registry() ) as $slug ) {
			$selectors[ $slug ] = hvn_realty_get_home_section_css_selector( $slug );
		}
		$localize['homeSectionSelectors'] = $selectors;
	}

	if ( function_exists( 'hvn_realty_get_customizer_text_preview_bindings' ) ) {
		$localize['textPreviewBindings'] = hvn_realty_get_customizer_text_preview_bindings();
	}

	if ( function_exists( 'hvn_realty_get_font_weight_preview_map' ) ) {
		$localize['fontWeightTokens'] = hvn_realty_get_font_weight_preview_map();
	}

	if ( function_exists( 'hvn_realty_get_mobile_search_drawer_preview_setting_ids' ) ) {
		$localize['msdPreviewSettings'] = hvn_realty_get_mobile_search_drawer_preview_setting_ids();
	}

	if ( function_exists( 'hvn_realty_get_mobile_search_drawer_customizer_defaults' ) ) {
		$localize['msdDefaults'] = hvn_realty_get_mobile_search_drawer_customizer_defaults();
	}

	wp_localize_script(
		'hvn-realty-customizer',
		'hvnRealtyCustomizer',
		$localize
	);
}
add_action( 'customize_preview_init', 'hvn_realty_customize_preview_js' );
