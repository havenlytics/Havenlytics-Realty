<?php
/**
 * Mobile Search Drawer — Customizer settings and dynamic CSS.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Customizer section ID. */
define( 'HVN_REALTY_MSD_SECTION', 'hvn_realty_home_mobile_search_drawer' );

/**
 * Default values for Mobile Search Drawer Customizer settings.
 *
 * @return array<string, mixed>
 */
function hvn_realty_get_mobile_search_drawer_customizer_defaults() {
	return array(
		'hvn_realty_msd_enabled'                => true,
		'hvn_realty_msd_homepage_only'          => false,
		'hvn_realty_msd_show_on_search_results' => false,
		'hvn_realty_msd_hero_trigger_offset'    => 0,
		'hvn_realty_msd_animation_duration'     => 460,
		'hvn_realty_msd_bottom_spacing'         => 16,
		'hvn_realty_msd_max_drawer_height'      => 70,
		'hvn_realty_msd_dock_bg'                => '#ffffff',
		'hvn_realty_msd_dock_bg_opacity'        => 72,
		'hvn_realty_msd_drawer_bg'              => '#ffffff',
		'hvn_realty_msd_drawer_bg_opacity'      => 97,
		'hvn_realty_msd_button_color'           => '#1f3a3a',
		'hvn_realty_msd_button_color_secondary' => '#2a4c4a',
		'hvn_realty_msd_active_dept_color'      => '#1f3a3a',
		'hvn_realty_msd_active_dept_text_color' => '#ffffff',
		'hvn_realty_msd_border_color'           => '#e3dccd',
		'hvn_realty_msd_shadow_opacity'         => 22,
		'hvn_realty_msd_overlay_opacity'       => 40,
		'hvn_realty_msd_dept_font_size'         => 13.5,
		'hvn_realty_msd_button_font_size'       => 16,
		'hvn_realty_msd_dock_radius'            => 30,
		'hvn_realty_msd_drawer_radius'          => 28,
		'hvn_realty_msd_button_radius'          => 18,
		'hvn_realty_msd_dock_padding'           => 12,
		'hvn_realty_msd_drawer_padding'         => 20,
		'hvn_realty_msd_dept_spacing'           => 8,
		'hvn_realty_msd_edge_fade'              => true,
		'hvn_realty_msd_auto_center'            => true,
		'hvn_realty_msd_swipe_gestures'         => true,
		'hvn_realty_msd_drag_close'             => true,
		'hvn_realty_msd_backdrop_blur'          => true,
		'hvn_realty_msd_spring_animation'       => true,
	);
}

/**
 * Read a mobile search drawer theme_mod with fallback.
 *
 * @param string $key Setting key without prefix duplication.
 * @return mixed
 */
function hvn_realty_get_mobile_search_drawer_mod( $key ) {
	$defaults = hvn_realty_get_mobile_search_drawer_customizer_defaults();
	$mod_key  = 0 === strpos( $key, 'hvn_realty_msd_' ) ? $key : 'hvn_realty_msd_' . $key;

	return get_theme_mod( $mod_key, $defaults[ $mod_key ] ?? '' );
}

/**
 * Whether the mobile search drawer is enabled in the Customizer.
 *
 * @return bool
 */
function hvn_realty_mobile_search_drawer_is_enabled() {
	return (bool) hvn_realty_get_mobile_search_drawer_mod( 'enabled' );
}

/**
 * Sanitize 0–100 opacity integer.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function hvn_realty_sanitize_msd_opacity( $value ) {
	return max( 0, min( 100, absint( $value ) ) );
}

/**
 * Sanitize font size in px (10–24).
 *
 * @param mixed $value Raw value.
 * @return float
 */
function hvn_realty_sanitize_msd_font_size( $value ) {
	$size = (float) $value;

	return round( max( 10, min( 24, $size ) ), 1 );
}

/**
 * Convert hex + opacity (0–100) to rgba().
 *
 * @param string $hex     Hex color.
 * @param int    $opacity Opacity percent.
 * @return string
 */
function hvn_realty_msd_hex_to_rgba( $hex, $opacity ) {
	$hex = sanitize_hex_color( $hex );
	if ( ! $hex ) {
		return 'transparent';
	}

	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	$a = max( 0, min( 100, (int) $opacity ) ) / 100;

	return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $a . ')';
}

/**
 * Build runtime config passed to the drawer JavaScript.
 *
 * @return array<string, mixed>
 */
function hvn_realty_get_mobile_search_drawer_js_config() {
	$defaults = hvn_realty_get_mobile_search_drawer_customizer_defaults();

	return array(
		'heroSearchSelector'  => '#hvn-theme-home-search',
		'mobileMaxWidth'      => 991,
		'useHeroVisibility'   => function_exists( 'hvn_realty_mobile_search_drawer_uses_hero_visibility' ) && hvn_realty_mobile_search_drawer_uses_hero_visibility() ? 1 : 0,
		'scrollShowOffset'    => 200,
		'heroTriggerOffset'   => absint( hvn_realty_get_mobile_search_drawer_mod( 'hero_trigger_offset' ) ),
		'animationDuration'   => absint( hvn_realty_get_mobile_search_drawer_mod( 'animation_duration' ) ),
		'autoCenterPills'     => (bool) hvn_realty_get_mobile_search_drawer_mod( 'auto_center' ),
		'edgeFade'            => (bool) hvn_realty_get_mobile_search_drawer_mod( 'edge_fade' ),
		'dragClose'           => (bool) hvn_realty_get_mobile_search_drawer_mod( 'drag_close' ),
		'swipeGestures'       => (bool) hvn_realty_get_mobile_search_drawer_mod( 'swipe_gestures' ),
		'springAnimation'     => (bool) hvn_realty_get_mobile_search_drawer_mod( 'spring_animation' ),
		'labels'              => array(
			'searchPrefix' => __( 'Search', 'havenlytics-realty' ),
			'closeDrawer'  => __( 'Close search drawer', 'havenlytics-realty' ),
			'departments'  => function_exists( 'hvn_realty_get_hero_search_tabs_label' )
				? hvn_realty_get_hero_search_tabs_label()
				: __( 'Listing type', 'havenlytics-realty' ),
		),
		'defaults'            => $defaults,
	);
}

/**
 * Generate customizable CSS for the mobile search drawer.
 *
 * @return string
 */
function hvn_realty_get_mobile_search_drawer_custom_css() {
	$defaults = hvn_realty_get_mobile_search_drawer_customizer_defaults();

	$dock_bg     = sanitize_hex_color( (string) hvn_realty_get_mobile_search_drawer_mod( 'dock_bg' ) ) ?: $defaults['hvn_realty_msd_dock_bg'];
	$dock_op     = hvn_realty_sanitize_msd_opacity( hvn_realty_get_mobile_search_drawer_mod( 'dock_bg_opacity' ) );
	$drawer_bg   = sanitize_hex_color( (string) hvn_realty_get_mobile_search_drawer_mod( 'drawer_bg' ) ) ?: $defaults['hvn_realty_msd_drawer_bg'];
	$drawer_op   = hvn_realty_sanitize_msd_opacity( hvn_realty_get_mobile_search_drawer_mod( 'drawer_bg_opacity' ) );
	$button      = sanitize_hex_color( (string) hvn_realty_get_mobile_search_drawer_mod( 'button_color' ) ) ?: $defaults['hvn_realty_msd_button_color'];
	$button_sec  = sanitize_hex_color( (string) hvn_realty_get_mobile_search_drawer_mod( 'button_color_secondary' ) ) ?: $defaults['hvn_realty_msd_button_color_secondary'];
	$active      = sanitize_hex_color( (string) hvn_realty_get_mobile_search_drawer_mod( 'active_dept_color' ) ) ?: $defaults['hvn_realty_msd_active_dept_color'];
	$active_text = sanitize_hex_color( (string) hvn_realty_get_mobile_search_drawer_mod( 'active_dept_text_color' ) ) ?: $defaults['hvn_realty_msd_active_dept_text_color'];
	$border      = sanitize_hex_color( (string) hvn_realty_get_mobile_search_drawer_mod( 'border_color' ) ) ?: $defaults['hvn_realty_msd_border_color'];
	$shadow_op   = hvn_realty_sanitize_msd_opacity( hvn_realty_get_mobile_search_drawer_mod( 'shadow_opacity' ) );
	$overlay_op  = hvn_realty_sanitize_msd_opacity( hvn_realty_get_mobile_search_drawer_mod( 'overlay_opacity' ) );

	$anim_ms     = max( 120, min( 1200, absint( hvn_realty_get_mobile_search_drawer_mod( 'animation_duration' ) ) ) );
	$bottom      = max( 0, min( 80, absint( hvn_realty_get_mobile_search_drawer_mod( 'bottom_spacing' ) ) ) );
	$max_height  = max( 40, min( 90, absint( hvn_realty_get_mobile_search_drawer_mod( 'max_drawer_height' ) ) ) );
	$dept_size   = hvn_realty_sanitize_msd_font_size( hvn_realty_get_mobile_search_drawer_mod( 'dept_font_size' ) );
	$button_size = hvn_realty_sanitize_msd_font_size( hvn_realty_get_mobile_search_drawer_mod( 'button_font_size' ) );
	$dock_radius = max( 0, min( 60, absint( hvn_realty_get_mobile_search_drawer_mod( 'dock_radius' ) ) ) );
	$drawer_rad  = max( 0, min( 60, absint( hvn_realty_get_mobile_search_drawer_mod( 'drawer_radius' ) ) ) );
	$button_rad  = max( 0, min( 40, absint( hvn_realty_get_mobile_search_drawer_mod( 'button_radius' ) ) ) );
	$dock_pad    = max( 4, min( 32, absint( hvn_realty_get_mobile_search_drawer_mod( 'dock_padding' ) ) ) );
	$drawer_pad  = max( 8, min( 40, absint( hvn_realty_get_mobile_search_drawer_mod( 'drawer_padding' ) ) ) );
	$dept_gap    = max( 4, min( 24, absint( hvn_realty_get_mobile_search_drawer_mod( 'dept_spacing' ) ) ) );

	$spring      = (bool) hvn_realty_get_mobile_search_drawer_mod( 'spring_animation' );
	$blur        = (bool) hvn_realty_get_mobile_search_drawer_mod( 'backdrop_blur' );
	$edge_fade   = (bool) hvn_realty_get_mobile_search_drawer_mod( 'edge_fade' );

	$ease_dock   = $spring ? 'cubic-bezier(0.22, 1, 0.36, 1)' : 'ease';
	$ease_spring = $spring ? 'cubic-bezier(0.22, 1.12, 0.32, 1)' : 'ease';

	$shadow_a    = $shadow_op / 100;
	$overlay_a   = $overlay_op / 100;

	$vars = array(
		'--hvn-theme-home-msd-primary:' . $button,
		'--hvn-theme-home-msd-primary-light:' . $button_sec,
		'--hvn-theme-home-msd-border:' . $border,
		'--hvn-theme-home-msd-glass-bg:' . hvn_realty_msd_hex_to_rgba( $dock_bg, $dock_op ),
		'--hvn-theme-home-msd-drawer-bg:' . hvn_realty_msd_hex_to_rgba( $drawer_bg, $drawer_op ),
		'--hvn-theme-home-msd-active-bg:' . $active,
		'--hvn-theme-home-msd-active-text:' . $active_text,
		'--hvn-theme-home-msd-dock-shadow:0 18px 45px rgba(20,30,25,' . $shadow_a . '),0 2px 8px rgba(20,30,25,' . ( $shadow_a * 0.36 ) . ')',
		'--hvn-theme-home-msd-overlay:rgba(8,10,9,' . $overlay_a . ')',
		'--hvn-theme-home-msd-anim-duration:' . $anim_ms . 'ms',
		'--hvn-theme-home-msd-bottom-offset:' . $bottom . 'px',
		'--hvn-theme-home-msd-max-height:' . $max_height . 'vh',
		'--hvn-theme-home-msd-dept-font-size:' . $dept_size . 'px',
		'--hvn-theme-home-msd-button-font-size:' . $button_size . 'px',
		'--hvn-theme-home-msd-dock-radius:' . $dock_radius . 'px',
		'--hvn-theme-home-msd-drawer-radius:' . $drawer_rad . 'px',
		'--hvn-theme-home-msd-button-radius:' . $button_rad . 'px',
		'--hvn-theme-home-msd-dock-padding:' . $dock_pad . 'px',
		'--hvn-theme-home-msd-drawer-padding:' . $drawer_pad . 'px',
		'--hvn-theme-home-msd-dept-gap:' . $dept_gap . 'px',
		'--hvn-theme-home-msd-ease-dock:' . $ease_dock,
		'--hvn-theme-home-msd-ease-spring:' . $ease_spring,
		'--hvn-theme-home-msd-fade-bg:' . hvn_realty_msd_hex_to_rgba( $dock_bg, min( 100, $dock_op + 20 ) ),
	);

	$rules = array(
		'body.hvn-theme-home .hvn-theme-home-msd-root{' . implode( ';', $vars ) . '}',
		'body.hvn-theme-home .hvn-theme-home-msd-dock-wrap{bottom:calc(var(--hvn-theme-home-msd-bottom-offset, 16px) + env(safe-area-inset-bottom, 0px));top:auto}',
		'body.hvn-theme-home .hvn-theme-home-msd-dock-wrap.hvn-theme-home-msd-drawer-open .hvn-theme-home-msd-drawer{max-height:var(--hvn-theme-home-msd-max-height)}',
		'body.hvn-theme-home .hvn-theme-home-msd-dock{border-radius:var(--hvn-theme-home-msd-dock-radius);padding:var(--hvn-theme-home-msd-dock-padding);box-shadow:var(--hvn-theme-home-msd-dock-shadow)}',
		'body.hvn-theme-home .hvn-theme-home-msd-drawer{background:var(--hvn-theme-home-msd-drawer-bg);border-radius:0 0 var(--hvn-theme-home-msd-drawer-radius) var(--hvn-theme-home-msd-drawer-radius)}',
		'body.hvn-theme-home .hvn-theme-home-msd-pills{gap:var(--hvn-theme-home-msd-dept-gap)}',
		'body.hvn-theme-home .hvn-theme-home-msd-pill{font-size:var(--hvn-theme-home-msd-dept-font-size)}',
		'body.hvn-theme-home .hvn-theme-home-msd-pill.hvn-theme-home-msd-pill-active{background:var(--hvn-theme-home-msd-active-bg);border-color:var(--hvn-theme-home-msd-active-bg);color:var(--hvn-theme-home-msd-active-text)}',
		'body.hvn-theme-home .hvn-theme-home-msd-search-submit{font-size:var(--hvn-theme-home-msd-button-font-size);border-radius:var(--hvn-theme-home-msd-button-radius);background:linear-gradient(135deg,var(--hvn-theme-home-msd-primary),var(--hvn-theme-home-msd-primary-light))}',
		'body.hvn-theme-home .hvn-theme-home-msd-drawer-body{padding-left:var(--hvn-theme-home-msd-drawer-padding);padding-right:var(--hvn-theme-home-msd-drawer-padding)}',
		'body.hvn-theme-home .hvn-theme-home-msd-drawer-footer{padding-left:var(--hvn-theme-home-msd-drawer-padding);padding-right:var(--hvn-theme-home-msd-drawer-padding)}',
		'body.hvn-theme-home .hvn-theme-home-msd-scrim{background:var(--hvn-theme-home-msd-overlay)}',
	);

	if ( ! $blur ) {
		$rules[] = 'body.hvn-theme-home .hvn-theme-home-msd-dock,body.hvn-theme-home .hvn-theme-home-msd-drawer,body.hvn-theme-home .hvn-theme-home-msd-scrim{backdrop-filter:none;-webkit-backdrop-filter:none}';
	}

	if ( ! $edge_fade ) {
		$rules[] = 'body.hvn-theme-home .hvn-theme-home-msd-pills-fade{display:none}';
	}

	if ( ! hvn_realty_mobile_search_drawer_is_enabled() ) {
		$rules[] = 'body.hvn-theme-home .hvn-theme-home-msd-root{display:none!important}';
	}

	return implode( "\n", $rules );
}

/**
 * Register Mobile Search Drawer Customizer section and controls.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @return void
 */
function hvn_realty_customizer_register_mobile_search_drawer( $wp_customize ) {
	if ( ! defined( 'HVN_REALTY_HOMEPAGE_PANEL' ) ) {
		return;
	}

	$defaults = hvn_realty_get_mobile_search_drawer_customizer_defaults();

	$wp_customize->add_section(
		HVN_REALTY_MSD_SECTION,
		array(
			'title'           => esc_html__( 'Mobile Search Drawer', 'havenlytics-realty' ),
			'panel'           => HVN_REALTY_HOMEPAGE_PANEL,
			'priority'        => 16,
			'description'     => esc_html__( 'Configure the floating mobile search dock and drawer. Desktop Hero Search is not affected.', 'havenlytics-realty' ),
			'active_callback' => 'hvn_realty_customizer_homepage_is_active',
		)
	);

	$section = HVN_REALTY_MSD_SECTION;

	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_enabled', $section, esc_html__( 'Enable Mobile Search Drawer', 'havenlytics-realty' ), $defaults['hvn_realty_msd_enabled'], 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_homepage_only', $section, esc_html__( 'Show only on Homepage', 'havenlytics-realty' ), $defaults['hvn_realty_msd_homepage_only'], 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_show_on_search_results', $section, esc_html__( 'Show on Search Result Page', 'havenlytics-realty' ), $defaults['hvn_realty_msd_show_on_search_results'], 'postMessage' );

	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_hero_trigger_offset', $section, esc_html__( 'Hero trigger offset (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_hero_trigger_offset'], 'absint', array( 'min' => 0, 'max' => 400, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_animation_duration', $section, esc_html__( 'Animation duration (ms)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_animation_duration'], 'absint', array( 'min' => 120, 'max' => 1200, 'step' => 10 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_bottom_spacing', $section, esc_html__( 'Bottom spacing (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_bottom_spacing'], 'absint', array( 'min' => 0, 'max' => 80, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_max_drawer_height', $section, esc_html__( 'Maximum drawer height (vh)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_max_drawer_height'], 'absint', array( 'min' => 40, 'max' => 90, 'step' => 1 ) );

	$color_settings = array(
		'hvn_realty_msd_dock_bg'                => esc_html__( 'Dock background', 'havenlytics-realty' ),
		'hvn_realty_msd_drawer_bg'              => esc_html__( 'Drawer background', 'havenlytics-realty' ),
		'hvn_realty_msd_button_color'           => esc_html__( 'Primary button color', 'havenlytics-realty' ),
		'hvn_realty_msd_button_color_secondary' => esc_html__( 'Primary button gradient end', 'havenlytics-realty' ),
		'hvn_realty_msd_active_dept_color'      => esc_html__( 'Active department color', 'havenlytics-realty' ),
		'hvn_realty_msd_active_dept_text_color' => esc_html__( 'Active department text color', 'havenlytics-realty' ),
		'hvn_realty_msd_border_color'           => esc_html__( 'Border color', 'havenlytics-realty' ),
	);

	foreach ( $color_settings as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $defaults[ $id ],
				'sanitize_callback' => 'sanitize_hex_color',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				$id,
				array(
					'label'   => $label,
					'section' => $section,
				)
			)
		);
	}

	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_dock_bg_opacity', $section, esc_html__( 'Dock background opacity (%)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_dock_bg_opacity'], 'hvn_realty_sanitize_msd_opacity', array( 'min' => 0, 'max' => 100, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_drawer_bg_opacity', $section, esc_html__( 'Drawer background opacity (%)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_drawer_bg_opacity'], 'hvn_realty_sanitize_msd_opacity', array( 'min' => 0, 'max' => 100, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_shadow_opacity', $section, esc_html__( 'Shadow opacity (%)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_shadow_opacity'], 'hvn_realty_sanitize_msd_opacity', array( 'min' => 0, 'max' => 100, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_overlay_opacity', $section, esc_html__( 'Overlay opacity (%)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_overlay_opacity'], 'hvn_realty_sanitize_msd_opacity', array( 'min' => 0, 'max' => 100, 'step' => 1 ) );

	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_dept_font_size', $section, esc_html__( 'Department font size (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_dept_font_size'], 'hvn_realty_sanitize_msd_font_size', array( 'min' => 10, 'max' => 24, 'step' => 0.5 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_button_font_size', $section, esc_html__( 'Search button font size (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_button_font_size'], 'hvn_realty_sanitize_msd_font_size', array( 'min' => 10, 'max' => 24, 'step' => 0.5 ) );

	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_dock_radius', $section, esc_html__( 'Dock border radius (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_dock_radius'], 'absint', array( 'min' => 0, 'max' => 60, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_drawer_radius', $section, esc_html__( 'Drawer border radius (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_drawer_radius'], 'absint', array( 'min' => 0, 'max' => 60, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_button_radius', $section, esc_html__( 'Button border radius (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_button_radius'], 'absint', array( 'min' => 0, 'max' => 40, 'step' => 1 ) );

	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_dock_padding', $section, esc_html__( 'Dock padding (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_dock_padding'], 'absint', array( 'min' => 4, 'max' => 32, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_drawer_padding', $section, esc_html__( 'Drawer padding (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_drawer_padding'], 'absint', array( 'min' => 8, 'max' => 40, 'step' => 1 ) );
	hvn_realty_home_add_number( $wp_customize, 'hvn_realty_msd_dept_spacing', $section, esc_html__( 'Department spacing (px)', 'havenlytics-realty' ), $defaults['hvn_realty_msd_dept_spacing'], 'absint', array( 'min' => 4, 'max' => 24, 'step' => 1 ) );

	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_edge_fade', $section, esc_html__( 'Enable edge fade', 'havenlytics-realty' ), $defaults['hvn_realty_msd_edge_fade'], 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_auto_center', $section, esc_html__( 'Enable smooth auto-centering', 'havenlytics-realty' ), $defaults['hvn_realty_msd_auto_center'], 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_swipe_gestures', $section, esc_html__( 'Enable swipe gestures', 'havenlytics-realty' ), $defaults['hvn_realty_msd_swipe_gestures'], 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_drag_close', $section, esc_html__( 'Enable drag-to-close', 'havenlytics-realty' ), $defaults['hvn_realty_msd_drag_close'], 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_backdrop_blur', $section, esc_html__( 'Enable backdrop blur', 'havenlytics-realty' ), $defaults['hvn_realty_msd_backdrop_blur'], 'postMessage' );
	hvn_realty_customizer_add_checkbox( $wp_customize, 'hvn_realty_msd_spring_animation', $section, esc_html__( 'Enable spring animation', 'havenlytics-realty' ), $defaults['hvn_realty_msd_spring_animation'], 'postMessage' );
}
add_action( 'customize_register', 'hvn_realty_customizer_register_mobile_search_drawer', 25 );

/**
 * Output mobile search drawer custom CSS on the frontend.
 *
 * @return void
 */
function hvn_realty_output_mobile_search_drawer_custom_css() {
	if ( ! function_exists( 'hvn_realty_should_render_mobile_search_drawer' ) || ! hvn_realty_should_render_mobile_search_drawer() ) {
		return;
	}

	$css = hvn_realty_get_mobile_search_drawer_custom_css();
	if ( '' === $css || ! wp_style_is( 'hvn-realty-home-mobile-search-drawer', 'enqueued' ) ) {
		return;
	}

	wp_add_inline_style( 'hvn-realty-home-mobile-search-drawer', $css );
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_output_mobile_search_drawer_custom_css', 37 );

/**
 * Output mobile search drawer CSS in the Customizer preview.
 *
 * @return void
 */
function hvn_realty_customizer_preview_mobile_search_drawer_css() {
	$css = hvn_realty_get_mobile_search_drawer_custom_css();
	if ( '' === $css ) {
		return;
	}

	wp_add_inline_style( 'hvn-realty-home-mobile-search-drawer', $css );
}
add_action( 'customize_preview_init', 'hvn_realty_customizer_preview_mobile_search_drawer_css', 26 );

/**
 * Setting IDs used for live preview bindings.
 *
 * @return string[]
 */
function hvn_realty_get_mobile_search_drawer_preview_setting_ids() {
	return array_keys( hvn_realty_get_mobile_search_drawer_customizer_defaults() );
}
