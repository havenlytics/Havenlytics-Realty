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
 * Official Havenlytics Realty brand color defaults (Customizer fallbacks only).
 *
 * @return array{primary: string, secondary: string, accent: string}
 */
function hvn_realty_get_brand_color_defaults() {
	return array(
		'primary'   => '#C9A36B',
		'secondary' => '#A9803F',
		'accent'    => '#FF9AA2',
	);
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
			'default'         => hvn_realty_get_brand_color_defaults()['primary'],
			'plugin_var'      => '--hvnly-brand-primary',
			'plugin_callback' => 'hvnly_get_brand_color',
		),
		'secondary' => array(
			'theme_mod'       => 'hvn_realty_secondary_color',
			'default'         => hvn_realty_get_brand_color_defaults()['secondary'],
			'plugin_var'      => '--hvnly-brand-secondary',
			'plugin_callback' => 'hvnly_get_secondary_color',
		),
		'accent'    => array(
			'theme_mod'       => 'hvn_realty_accent_color',
			'default'         => hvn_realty_get_brand_color_defaults()['accent'],
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
 * The theme is the source of truth (2.0.1): the saved Customizer value wins,
 * then the theme default. The plugin default is only a last resort when the
 * theme value is somehow empty. This keeps one branding layer controlled from
 * the Theme Customizer while remaining backward compatible — saved theme_mods
 * are never modified or removed.
 *
 * Fallback order: Theme Customizer → Theme Default → Plugin Default.
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

	// Theme Customizer value (or theme default) is authoritative.
	$theme_value = hvn_realty_get_legacy_design_token( $token );
	if ( $theme_value ) {
		return $theme_value;
	}

	// Last resort only: plugin-provided color, then registry default.
	$plugin = hvn_realty_get_plugin_design_token_value( $token, $config );

	return $plugin ? $plugin : $config['default'];
}

/**
 * CSS value for a brand token.
 *
 * The theme owns brand colors, so this returns the resolved theme hex. Plugin
 * brand variables are pointed at these theme values by the color bridge, so the
 * plugin inherits the theme palette site-wide.
 *
 * @param string $token Token key: primary, secondary, accent.
 * @return string CSS color value.
 */
function hvn_realty_get_design_token_css_value( $token ) {
	return hvn_realty_get_design_token( $token );
}

/**
 * Convert a hex color to an "r, g, b" triplet for rgba() usage.
 *
 * @param string $hex Hex color (#rgb or #rrggbb).
 * @return string "r, g, b" or empty string when invalid.
 */
function hvn_realty_hex_to_rgb_triplet( $hex ) {
	$hex = sanitize_hex_color( $hex );

	if ( ! $hex ) {
		return '';
	}

	$hex = ltrim( $hex, '#' );

	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}

	if ( 6 !== strlen( $hex ) ) {
		return '';
	}

	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );

	return $r . ', ' . $g . ', ' . $b;
}

/**
 * The complete map of plugin base design tokens the theme overrides.
 *
 * The plugin derives every component token (buttons, inputs, badges, links,
 * pagination, map markers, sliders, status colors, etc.) from this small set of
 * base `:root` variables, so overriding the base set re-colors the entire
 * plugin UI without duplicating per-component settings.
 *
 * Each entry maps a plugin CSS variable to a theme CSS variable that holds the
 * resolved value (Theme Customizer → Theme Default → Plugin Default).
 *
 * @return array<string, string> plugin_var => theme_var.
 */
function hvn_realty_get_plugin_color_bridge_var_map() {
	$map = array(
		'--hvnly-brand-primary'   => '--hvn-primary',
		'--hvnly-brand-secondary' => '--hvn-secondary',
		'--hvnly-brand-accent'    => '--hvn-accent',
		'--hvnly-primary-color'   => '--hvn-primary',
		'--hvnly-secondary-color' => '--hvn-secondary',
		'--hvnly-button-bg'       => '--hvn-primary',
		'--hvnly-button-bg-hover' => '--hvn-secondary',
		'--hvnly-input-focus'     => '--hvn-primary',
		'--hvnly-price-color'     => '--hvn-primary',
		'--hvnly-border-color'    => '--hvn-border',
		'--hvnly-text-primary'    => '--hvn-text',
		'--hvnly-brand-success'   => '--hvn-success',
		'--hvnly-brand-warning'   => '--hvn-warning',
		'--hvnly-brand-error'     => '--hvn-danger',
	);

	/**
	 * Filter the plugin color bridge variable map.
	 *
	 * @param array<string, string> $map plugin_var => theme_var.
	 */
	return apply_filters( 'hvn_realty_plugin_color_bridge_var_map', $map );
}

/**
 * Resolve the full theme palette used by the plugin color bridge.
 *
 * @return array<string, string>
 */
function hvn_realty_get_resolved_theme_palette() {
	$defaults  = hvn_realty_get_brand_color_defaults();
	$primary   = hvn_realty_get_design_token( 'primary', $defaults['primary'] );
	$secondary = hvn_realty_get_design_token( 'secondary', $defaults['secondary'] );
	$accent    = hvn_realty_get_design_token( 'accent', $defaults['accent'] );

	$border  = sanitize_hex_color( get_theme_mod( 'hvn_realty_border_color', '#E4E4ED' ) );
	$text    = sanitize_hex_color( get_theme_mod( 'hvn_realty_text_color', '#1E1E2F' ) );
	$success = sanitize_hex_color( get_theme_mod( 'hvn_realty_success_color', '#00B46A' ) );
	$warning = sanitize_hex_color( get_theme_mod( 'hvn_realty_warning_color', '#FFB507' ) );
	$danger  = sanitize_hex_color( get_theme_mod( 'hvn_realty_danger_color', '#FF4D4F' ) );

	return array(
		'primary'   => $primary,
		'secondary' => $secondary,
		'accent'    => $accent,
		'border'    => $border ? $border : '#E4E4ED',
		'text'      => $text ? $text : '#1E1E2F',
		'success'   => $success ? $success : '#00B46A',
		'warning'   => $warning ? $warning : '#FFB507',
		'danger'    => $danger ? $danger : '#FF4D4F',
	);
}

/**
 * Resolve the plugin color bridge to concrete values for an authoritative print.
 *
 * Returns plugin CSS variable => resolved hex/value pairs (not var() chains), so
 * the late override wins over the plugin DynamicStyleGenerator inline block.
 *
 * @return array<string, string> plugin_var => CSS value.
 */
function hvn_realty_get_plugin_color_bridge_values() {
	$palette = hvn_realty_get_resolved_theme_palette();

	$values = array(
		// Brand base (plugin inline sets these with literal plugin-setting hex).
		'--hvnly-brand-primary'     => $palette['primary'],
		'--hvnly-brand-secondary'   => $palette['secondary'],
		'--hvnly-brand-accent'      => $palette['accent'],
		'--hvnly-primary-color'     => $palette['primary'],
		'--hvnly-secondary-color'   => $palette['secondary'],
		// Component tokens derived from brand colors.
		'--hvnly-button-bg'         => $palette['primary'],
		'--hvnly-button-bg-hover'   => $palette['secondary'],
		'--hvnly-input-focus'       => $palette['primary'],
		'--hvnly-price-color'       => $palette['primary'],
		'--hvnly-pagination-active-bg' => $palette['primary'],
		'--hvnly-map-marker-bg'     => $palette['primary'],
		'--hvnly-slider-thumb'      => $palette['primary'],
		'--hvnly-slider-range'      => $palette['secondary'],
		'--hvnly-status-sale'       => $palette['primary'],
		'--hvnly-ribbon-bg'         => $palette['accent'],
		'--hvnly-link-color'        => $palette['primary'],
		'--hvnly-link-hover-color'  => $palette['secondary'],
		'--hvnly-title-color'       => $palette['text'],
		// Text, border, status.
		'--hvnly-border-color'      => $palette['border'],
		'--hvnly-text-primary'      => $palette['text'],
		'--hvnly-text-color'        => $palette['text'],
		'--hvnly-brand-success'     => $palette['success'],
		'--hvnly-brand-warning'     => $palette['warning'],
		'--hvnly-brand-error'       => $palette['danger'],
	);

	$primary_rgb   = hvn_realty_hex_to_rgb_triplet( $palette['primary'] );
	$secondary_rgb = hvn_realty_hex_to_rgb_triplet( $palette['secondary'] );
	if ( '' !== $primary_rgb ) {
		$values['--hvnly-primary-rgb']   = $primary_rgb;
		$values['--hvnly-focus-ring']    = '0 0 0 3px rgba(' . $primary_rgb . ', 0.22)';
	}
	if ( '' !== $secondary_rgb ) {
		$values['--hvnly-secondary-rgb'] = $secondary_rgb;
	}

	/**
	 * Filter the resolved plugin color bridge values.
	 *
	 * @param array<string, string> $values plugin_var => CSS value.
	 * @param array<string, string> $palette  Resolved theme palette.
	 */
	return apply_filters( 'hvn_realty_plugin_color_bridge_values', $values, $palette );
}

/**
 * Component-level bridge for plugin rules that hardcode hex in DynamicStyleGenerator.
 *
 * @return string CSS rules (no selector wrapper).
 */
function hvn_realty_get_plugin_color_bridge_component_css() {
	$palette = hvn_realty_get_resolved_theme_palette();
	$primary   = $palette['primary'];
	$secondary = $palette['secondary'];

	return '
.hvnly-btn-primary,.hvnly-button-primary,.hvnly-submit-btn,button.hvnly-property-single__action-btn--primary{background-color:' . $primary . ';border-color:' . $primary . '}
.hvnly-btn-primary:hover,.hvnly-button-primary:hover,.hvnly-submit-btn:hover,button.hvnly-property-single__action-btn--primary:hover{background-color:' . $secondary . ';border-color:' . $secondary . '}
a,.hvnly-link{color:' . $primary . '}
a:hover,.hvnly-link:hover{color:' . $secondary . '}
.hvnly-price,.hvnly-property-price{color:var(--hvnly-brand-primary)}
.hvnly-badge,.hvnly-featured-badge{background-color:var(--hvnly-brand-primary)}
';
}

/**
 * Full inline CSS for the authoritative plugin color bridge.
 *
 * @return string
 */
function hvn_realty_get_plugin_color_bridge_inline_css() {
	$values = hvn_realty_get_plugin_color_bridge_values();
	if ( empty( $values ) ) {
		return '';
	}

	$root = array();
	foreach ( $values as $var => $value ) {
		$root[] = $var . ':' . $value . ';';
	}

	return ':root{' . implode( '', $root ) . '}' . hvn_realty_get_plugin_color_bridge_component_css();
}

/**
 * Bootstrap plugin `--hvnly-*` variables on :root using theme CSS variables.
 *
 * The theme is the branding source of truth, so the plugin base variables are
 * pointed at the theme color variables. When the plugin is active its pages
 * inherit the theme palette; when inactive, theme CSS that references the plugin
 * variable names still resolves. Legacy theme_mods are never modified.
 *
 * @return string CSS declarations (no selector).
 */
function hvn_realty_get_color_bridge_bootstrap_css() {
	$declarations = array();

	foreach ( hvn_realty_get_plugin_color_bridge_var_map() as $plugin_var => $theme_var ) {
		$declarations[] = $plugin_var . ': var(' . $theme_var . ');';
	}

	return implode( "\n\t", $declarations );
}

/**
 * Enqueue the authoritative plugin color bridge after all plugin styles.
 *
 * The Havenlytics plugin injects inline :root colors and hardcoded button hex
 * values via DynamicStyleGenerator on the hvnly-frontend-default handle. This
 * bridge registers a dependent empty handle so its inline CSS always prints
 * after the plugin bundle — guaranteeing theme Customizer colors win.
 *
 * @return void
 */
function hvn_realty_enqueue_plugin_color_bridge() {
	if ( is_admin() || ! hvn_realty_uses_plugin_design_tokens() ) {
		return;
	}

	if ( ! wp_style_is( 'hvnly-frontend-default', 'registered' ) ) {
		return;
	}

	$css = hvn_realty_get_plugin_color_bridge_inline_css();
	if ( '' === $css ) {
		return;
	}

	if ( ! wp_style_is( 'hvn-realty-plugin-color-bridge', 'registered' ) ) {
		wp_register_style(
			'hvn-realty-plugin-color-bridge',
			false,
			array( 'hvnly-frontend-default' ),
			defined( 'HVN_REALTY_VERSION' ) ? HVN_REALTY_VERSION : '1.0.0'
		);
	}

	wp_enqueue_style( 'hvn-realty-plugin-color-bridge' );
	wp_add_inline_style( 'hvn-realty-plugin-color-bridge', $css );
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_plugin_color_bridge', 9999 );

/**
 * Full color bridge CSS block for :root injection.
 *
 * @return string CSS declarations (no selector).
 */
function hvn_realty_get_color_bridge_css() {
	$defaults     = hvn_realty_get_brand_color_defaults();
	$declarations = array(
		'--hvn-primary: ' . hvn_realty_get_design_token( 'primary', $defaults['primary'] ) . ';',
		'--hvn-secondary: ' . hvn_realty_get_design_token( 'secondary', $defaults['secondary'] ) . ';',
		'--hvn-accent: ' . hvn_realty_get_design_token( 'accent', $defaults['accent'] ) . ';',
		'--hvn-theme-brand-primary: var(--hvn-primary);',
		'--hvn-theme-brand-secondary: var(--hvn-secondary);',
		'--hvn-theme-brand-accent: var(--hvn-accent);',
	);

	// Theme is the source of truth: plugin brand variables inherit theme colors.
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
