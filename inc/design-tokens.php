<?php
/**
 * Color Bridge System — theme ↔ Havenlytics plugin design tokens.
 *
 * Ownership:
 * - Plugin: brand colors (--hvnly-brand-*), buttons, inputs, status colors.
 * - Theme: typography, layout, header, hero.
 *
 * Legacy Customizer color theme_mods are never removed or overwritten.
 * When the plugin is inactive, saved Customizer values continue to drive the site.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand color token registry (legacy theme_mod ↔ plugin CSS variable bridge).
 *
 * @return array<string, array{theme_mod: string, default: string, plugin_var: string, plugin_callback: string|null}>
 */
function hvn_realty_get_legacy_color_token_registry() {
	$registry = array(
		'primary'   => array(
			'theme_mod'       => 'hvn_realty_primary_color',
			'default'         => '#6C60FE',
			'plugin_var'      => '--hvnly-brand-primary',
			'plugin_callback' => 'hvnly_get_brand_color',
		),
		'secondary' => array(
			'theme_mod'       => 'hvn_realty_secondary_color',
			'default'         => '#764ba2',
			'plugin_var'      => '--hvnly-brand-secondary',
			'plugin_callback' => 'hvnly_get_secondary_color',
		),
		'accent'    => array(
			'theme_mod'       => 'hvn_realty_accent_color',
			'default'         => '#FF9AA2',
			'plugin_var'      => '--hvnly-brand-accent',
			'plugin_callback' => 'hvnly_get_accent_color',
		),
	);

	/**
	 * Filter the legacy color token registry.
	 *
	 * @param array<string, array<string, mixed>> $registry Token registry.
	 */
	return apply_filters( 'hvn_realty_legacy_color_token_registry', $registry );
}

/**
 * Documented ownership split for the color bridge.
 *
 * @return array{plugin: string[], theme: string[]}
 */
function hvn_realty_get_color_bridge_ownership() {
	return array(
		'plugin' => array(
			'brand_colors',
			'buttons',
			'inputs',
			'status_colors',
		),
		'theme'  => array(
			'typography',
			'layout',
			'header',
			'hero',
		),
	);
}

/**
 * Whether a Customizer setting ID is a legacy theme-owned brand color.
 *
 * @param string $setting_id Customizer setting ID.
 * @return bool
 */
function hvn_realty_is_legacy_color_theme_mod( $setting_id ) {
	foreach ( hvn_realty_get_legacy_color_token_registry() as $config ) {
		if ( $config['theme_mod'] === $setting_id ) {
			return true;
		}
	}

	return false;
}

/**
 * Read a legacy theme_mod color (Customizer only — never writes).
 *
 * @param string $token Token key: primary, secondary, accent.
 * @return string Sanitized hex color.
 */
function hvn_realty_get_legacy_design_token( $token ) {
	$registry = hvn_realty_get_legacy_color_token_registry();
	$config   = $registry[ $token ] ?? null;

	if ( ! $config ) {
		return '';
	}

	$value = get_theme_mod( $config['theme_mod'], $config['default'] );
	$value = sanitize_hex_color( $value );

	return $value ? $value : $config['default'];
}

/**
 * Whether the Havenlytics plugin should supply brand colors for the theme.
 *
 * @return bool
 */
function hvn_realty_uses_plugin_design_tokens() {
	return function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) && hvn_realty_is_havenlytics_plugin_active();
}

/**
 * Resolve a plugin brand color via callback when available.
 *
 * @param string               $token  Token key.
 * @param array<string, mixed> $config Registry config.
 * @return string Empty string when unavailable.
 */
function hvn_realty_get_plugin_design_token_value( $token, $config ) {
	if ( ! hvn_realty_uses_plugin_design_tokens() || empty( $config['plugin_callback'] ) || ! is_callable( $config['plugin_callback'] ) ) {
		return '';
	}

	$plugin_color = sanitize_hex_color( (string) call_user_func( $config['plugin_callback'] ) );

	/**
	 * Filter a plugin-provided brand color before the theme consumes it.
	 *
	 * @param string $plugin_color Sanitized hex color.
	 * @param string $token        Token key.
	 */
	$plugin_color = apply_filters( 'hvn_realty_plugin_design_token_value', $plugin_color, $token );

	return $plugin_color ? $plugin_color : '';
}

/**
 * Resolve a shared design token to a concrete hex value for PHP use.
 *
 * When the plugin is active, reads plugin color helpers when available.
 * Otherwise returns the legacy Customizer theme_mod value.
 *
 * @param string $token    Token key: primary, secondary, accent.
 * @param string $fallback Optional fallback hex when token is unknown.
 * @return string Sanitized hex color.
 */
function hvn_realty_get_design_token( $token, $fallback = '' ) {
	$registry = hvn_realty_get_legacy_color_token_registry();
	$config   = $registry[ $token ] ?? null;

	if ( ! $config ) {
		$fallback = sanitize_hex_color( $fallback );

		return $fallback ? $fallback : '';
	}

	$legacy = hvn_realty_get_legacy_design_token( $token );
	$plugin = hvn_realty_get_plugin_design_token_value( $token, $config );

	if ( $plugin ) {
		return $plugin;
	}

	return $legacy;
}

/**
 * CSS value for a brand token (var bridge when plugin active, hex otherwise).
 *
 * @param string $token Token key: primary, secondary, accent.
 * @return string CSS color value.
 */
function hvn_realty_get_design_token_css_value( $token ) {
	$registry = hvn_realty_get_legacy_color_token_registry();
	$config   = $registry[ $token ] ?? null;

	if ( ! $config ) {
		return '';
	}

	$legacy = hvn_realty_get_legacy_design_token( $token );

	if ( hvn_realty_uses_plugin_design_tokens() && ! empty( $config['plugin_var'] ) ) {
		return 'var(' . $config['plugin_var'] . ', ' . $legacy . ')';
	}

	return $legacy;
}

/**
 * Bootstrap --hvnly-brand-* variables on :root when the plugin is inactive.
 *
 * Ensures theme CSS that references plugin variable names still resolves using
 * saved Customizer colors without modifying or deleting legacy settings.
 *
 * @return string CSS declarations (no selector).
 */
function hvn_realty_get_color_bridge_bootstrap_css() {
	if ( hvn_realty_uses_plugin_design_tokens() ) {
		return '';
	}

	$declarations = array();

	foreach ( hvn_realty_get_legacy_color_token_registry() as $token => $config ) {
		if ( empty( $config['plugin_var'] ) ) {
			continue;
		}

		$legacy         = hvn_realty_get_legacy_design_token( $token );
		$declarations[] = $config['plugin_var'] . ': ' . $legacy . ';';
	}

	return implode( "\n\t", $declarations );
}

/**
 * Full color bridge CSS block for :root injection.
 *
 * @return string CSS declarations (no selector).
 */
function hvn_realty_get_color_bridge_css() {
	$declarations = array(
		'--hvn-primary: ' . hvn_realty_get_design_token_css_value( 'primary' ) . ';',
		'--hvn-secondary: ' . hvn_realty_get_design_token_css_value( 'secondary' ) . ';',
		'--hvn-accent: ' . hvn_realty_get_design_token_css_value( 'accent' ) . ';',
		'--hvn-theme-brand-primary: var(--hvn-primary);',
		'--hvn-theme-brand-secondary: var(--hvn-secondary);',
		'--hvn-theme-brand-accent: var(--hvn-accent);',
	);

	$bootstrap = hvn_realty_get_color_bridge_bootstrap_css();
	if ( '' !== $bootstrap ) {
		$declarations[] = $bootstrap;
	}

	/**
	 * Filter color bridge :root declarations.
	 *
	 * @param string[] $declarations CSS declaration lines.
	 */
	$declarations = apply_filters( 'hvn_realty_color_bridge_css_declarations', $declarations );

	return implode( "\n\t", $declarations );
}
