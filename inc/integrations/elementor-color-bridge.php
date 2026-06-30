<?php
/**
 * Elementor color bridge — theme branding for HVN: Property Archive widgets.
 *
 * The plugin Elementor widget sets wrapper-scoped --hvnly-brand-* variables via
 * Style controls. When those controls are still at plugin defaults (or unset),
 * re-point the wrapper variables at theme tokens so Customizer colors apply.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin widget Style-tab defaults (detection only — not used as output colors).
 *
 * @return array{brand_color: string, secondary_color: string}
 */
function hvn_realty_elementor_property_archive_plugin_defaults() {
	return array(
		'brand_color'     => '#6C60FE',
		'secondary_color' => '#764ba2',
	);
}

/**
 * Normalize a hex color for comparison.
 *
 * @param string $color Raw color value.
 * @return string Uppercase #RRGGBB or empty string when invalid.
 */
function hvn_realty_elementor_normalize_hex_color( $color ) {
	$color = is_string( $color ) ? trim( $color ) : '';

	if ( '' === $color ) {
		return '';
	}

	$sanitized = sanitize_hex_color( $color );

	if ( ! $sanitized ) {
		return '';
	}

	$hex = strtoupper( ltrim( $sanitized, '#' ) );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	return '#' . $hex;
}

/**
 * Whether a saved color value matches the plugin widget control default.
 *
 * @param string $value   Saved widget color.
 * @param string $default Plugin control default hex.
 * @return bool
 */
function hvn_realty_elementor_color_matches_plugin_default( $value, $default ) {
	$normalized_value   = hvn_realty_elementor_normalize_hex_color( $value );
	$normalized_default = hvn_realty_elementor_normalize_hex_color( $default );

	if ( '' === $normalized_value ) {
		return true;
	}

	return $normalized_value === $normalized_default;
}

/**
 * Whether an Elementor color control was explicitly customized by the editor.
 *
 * @param string               $control  Control name: brand_color or secondary_color.
 * @param array<string, mixed> $settings Raw widget settings from Elementor.
 * @param array<string, mixed> $globals  __globals__ map from widget settings.
 * @return bool True when the user chose a custom or global color.
 */
function hvn_realty_elementor_property_archive_color_is_customized( $control, $settings, $globals ) {
	if ( ! empty( $globals[ $control ] ) ) {
		return true;
	}

	if ( ! array_key_exists( $control, $settings ) ) {
		return false;
	}

	$value = $settings[ $control ];

	if ( null === $value || '' === $value ) {
		return false;
	}

	$defaults = hvn_realty_elementor_property_archive_plugin_defaults();
	$default  = $defaults[ $control ] ?? '';

	return ! hvn_realty_elementor_color_matches_plugin_default( $value, $default );
}

/**
 * Build wrapper CSS custom-property bridge declarations for default widget colors.
 *
 * @param bool $bridge_primary   Whether to bridge brand color.
 * @param bool $bridge_secondary Whether to bridge secondary color.
 * @return string Inline style declarations or empty string.
 */
function hvn_realty_elementor_property_archive_bridge_css( $bridge_primary, $bridge_secondary ) {
	$declarations = array();

	if ( $bridge_primary ) {
		$declarations[] = '--hvnly-brand-primary:var(--hvn-primary)';
	}

	if ( $bridge_secondary ) {
		$declarations[] = '--hvnly-brand-secondary:var(--hvn-secondary)';
	}

	if ( empty( $declarations ) ) {
		return '';
	}

	return implode( ';', $declarations ) . ';';
}

/**
 * Apply theme color bridge to HVN: Property Archive widgets at default colors.
 *
 * @param \Elementor\Widget_Base $widget Elementor widget instance.
 * @return void
 */
function hvn_realty_elementor_bridge_property_archive_colors( $widget ) {
	if ( ! is_object( $widget ) || ! method_exists( $widget, 'get_name' ) || ! method_exists( $widget, 'get_settings' ) ) {
		return;
	}

	if ( 'hvnly_all_properties' !== $widget->get_name() ) {
		return;
	}

	if ( ! function_exists( 'hvn_realty_uses_plugin_design_tokens' ) || ! hvn_realty_uses_plugin_design_tokens() ) {
		return;
	}

	$settings = $widget->get_settings();
	$globals  = ( isset( $settings['__globals__'] ) && is_array( $settings['__globals__'] ) ) ? $settings['__globals__'] : array();

	$bridge_primary   = ! hvn_realty_elementor_property_archive_color_is_customized( 'brand_color', $settings, $globals );
	$bridge_secondary = ! hvn_realty_elementor_property_archive_color_is_customized( 'secondary_color', $settings, $globals );

	$css = hvn_realty_elementor_property_archive_bridge_css( $bridge_primary, $bridge_secondary );

	if ( '' === $css || ! method_exists( $widget, 'add_render_attribute' ) ) {
		return;
	}

	$widget->add_render_attribute( '_wrapper', 'style', $css );
}
add_action( 'elementor/frontend/widget/before_render', 'hvn_realty_elementor_bridge_property_archive_colors', 10, 1 );
