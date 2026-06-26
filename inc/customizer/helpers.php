<?php
/**
 * Customizer helpers — getters, sanitizers, and design tokens.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'HVN_REALTY_CUSTOMIZER_PANEL' ) ) {
	define( 'HVN_REALTY_CUSTOMIZER_PANEL', 'hvn_realty_theme_settings' );
}

/**
 * Available font family choices.
 *
 * @return array<string, string>
 */
function hvn_realty_get_font_choices() {
	return apply_filters(
		'hvn_realty_font_choices',
		array(
			'inter'             => esc_html__( 'Inter', 'havenlytics-realty' ),
			'poppins'           => esc_html__( 'Poppins', 'havenlytics-realty' ),
			'plus-jakarta-sans' => esc_html__( 'Plus Jakarta Sans', 'havenlytics-realty' ),
			'roboto'            => esc_html__( 'Roboto', 'havenlytics-realty' ),
			'open-sans'         => esc_html__( 'Open Sans', 'havenlytics-realty' ),
			'montserrat'        => esc_html__( 'Montserrat', 'havenlytics-realty' ),
			'lato'              => esc_html__( 'Lato', 'havenlytics-realty' ),
			'nunito'            => esc_html__( 'Nunito', 'havenlytics-realty' ),
			'source-sans-pro'   => esc_html__( 'Source Sans Pro', 'havenlytics-realty' ),
			'work-sans'         => esc_html__( 'Work Sans', 'havenlytics-realty' ),
			'raleway'           => esc_html__( 'Raleway', 'havenlytics-realty' ),
			'dm-sans'           => esc_html__( 'DM Sans', 'havenlytics-realty' ),
			'outfit'            => esc_html__( 'Outfit', 'havenlytics-realty' ),
			'manrope'           => esc_html__( 'Manrope', 'havenlytics-realty' ),
			'playfair-display'  => esc_html__( 'Playfair Display', 'havenlytics-realty' ),
			'merriweather'      => esc_html__( 'Merriweather', 'havenlytics-realty' ),
			'system'            => esc_html__( 'System Default', 'havenlytics-realty' ),
		)
	);
}

/**
 * CSS font-family stack for a font slug.
 *
 * @param string $slug Font slug.
 * @return string
 */
function hvn_realty_get_font_stack( $slug ) {
	$stacks = hvn_realty_get_font_stack_map();

	return isset( $stacks[ $slug ] ) ? $stacks[ $slug ] : $stacks['inter'];
}

/**
 * Font stack safe for CSS output (allowlisted values only — do not use esc_attr).
 *
 * @param string $slug Font slug.
 * @return string
 */
function hvn_realty_get_font_stack_for_css( $slug ) {
	return hvn_realty_get_font_stack( hvn_realty_sanitize_font_choice( $slug ) );
}

/**
 * Font slug => CSS font-family stack map.
 *
 * @return array<string, string>
 */
function hvn_realty_get_font_stack_map() {
	return array(
		'inter'             => "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'poppins'           => "'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'plus-jakarta-sans' => "'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'roboto'            => "'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
		'open-sans'         => "'Open Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'montserrat'        => "'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'lato'              => "'Lato', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'nunito'            => "'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'source-sans-pro'   => "'Source Sans Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'work-sans'         => "'Work Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'raleway'           => "'Raleway', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'dm-sans'           => "'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'outfit'            => "'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'manrope'           => "'Manrope', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
		'playfair-display'  => "'Playfair Display', Georgia, 'Times New Roman', serif",
		'merriweather'      => "'Merriweather', Georgia, 'Times New Roman', serif",
		'system'            => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif",
	);
}

/**
 * Google Fonts API family strings keyed by font slug.
 *
 * @return array<string, string>
 */
function hvn_realty_get_google_fonts_api_map() {
	return array(
		'inter'             => 'Inter:wght@400;500;600;700',
		'poppins'           => 'Poppins:wght@500;600;700;800',
		'plus-jakarta-sans' => 'Plus+Jakarta+Sans:wght@400;500;600;700',
		'roboto'            => 'Roboto:wght@400;500;700',
		'open-sans'         => 'Open+Sans:wght@400;500;600;700',
		'montserrat'        => 'Montserrat:wght@400;500;600;700',
		'lato'              => 'Lato:wght@400;700',
		'nunito'            => 'Nunito:wght@400;600;700',
		'source-sans-pro'   => 'Source+Sans+Pro:wght@400;600;700',
		'work-sans'         => 'Work+Sans:wght@400;500;600;700',
		'raleway'           => 'Raleway:wght@400;500;600;700',
		'dm-sans'           => 'DM+Sans:wght@400;500;600;700',
		'outfit'            => 'Outfit:wght@400;500;600;700',
		'manrope'           => 'Manrope:wght@400;500;600;700',
		'playfair-display'  => 'Playfair+Display:wght@400;600;700',
		'merriweather'      => 'Merriweather:wght@400;700',
	);
}

/**
 * Google Fonts API families to load based on Customizer choices.
 *
 * @return string[] Font family names for Google Fonts.
 */
function hvn_realty_get_google_font_families() {
	$body    = hvn_realty_sanitize_font_choice( get_theme_mod( 'hvn_realty_body_font_family', 'inter' ) );
	$heading = hvn_realty_sanitize_font_choice( get_theme_mod( 'hvn_realty_heading_font_family', 'poppins' ) );

	$map = hvn_realty_get_google_fonts_api_map();

	// Always load theme default pair so typography works even before Customizer saves.
	$slugs = array_unique( array( 'inter', 'poppins', $body, $heading ) );

	$families = array();
	foreach ( $slugs as $slug ) {
		if ( isset( $map[ $slug ] ) ) {
			$families[ $slug ] = $map[ $slug ];
		}
	}

	return array_values( $families );
}

/**
 * Enqueue Google Fonts stylesheet (frontend + editor).
 *
 * @return string|null Handle name when enqueued, null when skipped.
 */
function hvn_realty_enqueue_google_fonts() {
	$families = hvn_realty_get_google_font_families();
	if ( empty( $families ) ) {
		return null;
	}

	$url = 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', $families ) . '&display=swap';

	wp_enqueue_style(
		'hvn-realty-fonts',
		$url,
		array(),
		null
	);

	return 'hvn-realty-fonts';
}

/**
 * Darken a hex color.
 *
 * @param string $hex     Hex color.
 * @param int    $percent Percentage.
 * @return string
 */
function hvn_realty_darken_color( $hex, $percent ) {
	$hex = ltrim( $hex, '#' );
	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$r = max( 0, hexdec( substr( $hex, 0, 2 ) ) - ( hexdec( substr( $hex, 0, 2 ) ) * $percent / 100 ) );
	$g = max( 0, hexdec( substr( $hex, 2, 2 ) ) - ( hexdec( substr( $hex, 2, 2 ) ) * $percent / 100 ) );
	$b = max( 0, hexdec( substr( $hex, 4, 2 ) ) - ( hexdec( substr( $hex, 4, 2 ) ) * $percent / 100 ) );
	return sprintf( '#%02x%02x%02x', $r, $g, $b );
}

/**
 * Lighten a hex color.
 *
 * @param string $hex     Hex color.
 * @param int    $percent Percentage.
 * @return string
 */
function hvn_realty_lighten_color( $hex, $percent ) {
	$hex = ltrim( $hex, '#' );
	if ( strlen( $hex ) === 3 ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$r = hexdec( substr( $hex, 0, 2 ) );
	$g = hexdec( substr( $hex, 2, 2 ) );
	$b = hexdec( substr( $hex, 4, 2 ) );
	$r = min( 255, $r + ( ( 255 - $r ) * $percent / 100 ) );
	$g = min( 255, $g + ( ( 255 - $g ) * $percent / 100 ) );
	$b = min( 255, $b + ( ( 255 - $b ) * $percent / 100 ) );
	return sprintf( '#%02x%02x%02x', $r, $g, $b );
}

/**
 * Sanitize pixel values.
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_px( $input ) {
	$input = absint( $input );
	return min( 9999, $input );
}

/**
 * Sanitize font choice.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_font_choice( $input ) {
	$choices = hvn_realty_get_font_choices();
	return array_key_exists( $input, $choices ) ? $input : 'inter';
}

/**
 * Sanitize header layout.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_header_layout( $input ) {
	$valid = array( '1', '2', '3' );
	return in_array( (string) $input, $valid, true ) ? (string) $input : '1';
}

/**
 * Sanitize footer columns.
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_footer_columns( $input ) {
	$input = absint( $input );
	if ( $input < 1 ) {
		return 1;
	}
	if ( $input > 4 ) {
		return 4;
	}
	return $input;
}

/**
 * Sanitize container layout mode.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_container_mode( $input ) {
	return in_array( $input, array( 'boxed', 'full' ), true ) ? $input : 'boxed';
}

/**
 * Sanitize heading scale slug.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_heading_scale( $input ) {
	$valid = array( 'small', 'medium', 'large' );
	return in_array( $input, $valid, true ) ? $input : 'medium';
}

/**
 * Sanitize line height.
 *
 * @param float|string $input Input.
 * @return float
 */
function hvn_realty_sanitize_line_height( $input ) {
	$input = floatval( $input );
	if ( $input < 1 ) {
		return 1.5;
	}
	if ( $input > 2.5 ) {
		return 2.5;
	}
	return $input;
}

/**
 * Sanitize URL for CTA links.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_url( $input ) {
	return esc_url_raw( $input );
}

/**
 * Sanitize a full URL, site path, or page slug for theme links.
 *
 * @param string $input Input.
 * @return string
 */
function hvn_realty_sanitize_url_or_path( $input ) {
	$input = trim( (string) $input );

	if ( '' === $input ) {
		return '';
	}

	if ( preg_match( '#^https?://#i', $input ) ) {
		return esc_url_raw( $input );
	}

	if ( 0 === strpos( $input, '/' ) ) {
		return '/' . ltrim( sanitize_text_field( wp_unslash( $input ) ), '/' );
	}

	return sanitize_title( $input );
}

/**
 * Resolve a Customizer link value to a front-end URL.
 *
 * @param string $value        URL, path, slug, or empty.
 * @param string $fallback_url Used when value is empty.
 * @return string
 */
function hvn_realty_resolve_theme_link( $value, $fallback_url ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return esc_url( $fallback_url );
	}

	if ( preg_match( '#^https?://#i', $value ) ) {
		return esc_url( $value );
	}

	if ( 0 === strpos( $value, '/' ) ) {
		return esc_url( home_url( $value ) );
	}

	return esc_url( home_url( '/' . trim( $value, '/' ) . '/' ) );
}

/**
 * Get heading scale multiplier.
 *
 * @return float
 */
function hvn_realty_get_heading_scale_multiplier() {
	$scale = hvn_realty_sanitize_heading_scale( get_theme_mod( 'hvn_realty_heading_scale', 'medium' ) );
	$map   = array(
		'small'  => 0.9,
		'medium' => 1,
		'large'  => 1.12,
	);
	return $map[ $scale ];
}

/**
 * Valid heading font-weight choices (numeric value => label).
 *
 * @return array<int, string>
 */
function hvn_realty_get_font_weight_choices() {
	return array(
		300 => esc_html__( 'Light', 'havenlytics-realty' ),
		400 => esc_html__( 'Regular', 'havenlytics-realty' ),
		500 => esc_html__( 'Medium', 'havenlytics-realty' ),
		600 => esc_html__( 'Semibold', 'havenlytics-realty' ),
		700 => esc_html__( 'Bold', 'havenlytics-realty' ),
		800 => esc_html__( 'Extra Bold', 'havenlytics-realty' ),
	);
}

/**
 * Default heading font-weight per level.
 *
 * @return array<string, int>
 */
function hvn_realty_get_heading_weight_defaults() {
	return array(
		'h1' => 700,
		'h2' => 700,
		'h3' => 600,
		'h4' => 600,
		'h5' => 500,
		'h6' => 500,
	);
}

/**
 * Map numeric font-weight to Havenlytics plugin CSS variable.
 *
 * @param int|string $weight Numeric weight.
 * @return string Plugin variable name without var() wrapper.
 */
function hvn_realty_get_font_weight_plugin_var_name( $weight ) {
	$map = array(
		300 => '--hvnly-font-weight-light',
		400 => '--hvnly-font-weight-regular',
		500 => '--hvnly-font-weight-medium',
		600 => '--hvnly-font-weight-semibold',
		700 => '--hvnly-font-weight-bold',
		800 => '--hvnly-font-weight-extrabold',
	);

	$weight = (string) absint( $weight );

	return $map[ $weight ] ?? '--hvnly-font-weight-semibold';
}

/**
 * Sanitize heading font-weight choice.
 *
 * @param int|string $input Input.
 * @return int
 */
function hvn_realty_sanitize_font_weight_choice( $input ) {
	$choices = hvn_realty_get_font_weight_choices();
	$weight  = absint( $input );

	return isset( $choices[ $weight ] ) ? $weight : 600;
}

/**
 * Resolve a heading level font-weight to a CSS value using plugin tokens.
 *
 * @param string $level Heading level: h1–h6.
 * @return string CSS font-weight value.
 */
function hvn_realty_get_heading_weight_css_value( $level ) {
	$defaults = hvn_realty_get_heading_weight_defaults();
	$default  = $defaults[ $level ] ?? 600;
	$weight   = hvn_realty_sanitize_font_weight_choice(
		get_theme_mod( 'hvn_realty_' . $level . '_weight', $default )
	);
	$var_name = hvn_realty_get_font_weight_plugin_var_name( $weight );

	return 'var(' . $var_name . ', ' . $weight . ')';
}

/**
 * CSS :root declarations for heading font-weight variables.
 *
 * @return string
 */
function hvn_realty_get_heading_weight_css_declarations() {
	$declarations = array();

	foreach ( array_keys( hvn_realty_get_heading_weight_defaults() ) as $level ) {
		$declarations[] = '--hvn-realty-' . $level . '-weight: ' . hvn_realty_get_heading_weight_css_value( $level ) . ';';
	}

	return implode( "\n\t", $declarations );
}

/**
 * Map numeric font-weight values to CSS for Customizer live preview.
 *
 * @return array<string, string>
 */
function hvn_realty_get_font_weight_preview_map() {
	$map = array();

	foreach ( array_keys( hvn_realty_get_font_weight_choices() ) as $weight ) {
		$var_name           = hvn_realty_get_font_weight_plugin_var_name( $weight );
		$map[ (string) $weight ] = 'var(' . $var_name . ', ' . $weight . ')';
	}

	return $map;
}

/**
 * Get header layout.
 *
 * @return string
 */
function hvn_realty_get_header_layout() {
	return get_theme_mod( 'hvn_realty_header_layout', '1' );
}

/**
 * Whether sticky header is enabled.
 *
 * @return bool
 */
function hvn_realty_is_sticky_header() {
	return (bool) get_theme_mod( 'hvn_realty_sticky_header', true );
}

/**
 * Whether transparent header is enabled.
 *
 * @return bool
 */
function hvn_realty_is_transparent_header() {
	return (bool) get_theme_mod( 'hvn_realty_transparent_header', false );
}

/**
 * Whether header search is shown.
 *
 * @return bool
 */
function hvn_realty_show_header_search() {
	return (bool) get_theme_mod( 'hvn_realty_show_header_search', true );
}

/**
 * Whether header CTA is enabled.
 *
 * @return bool
 */
function hvn_realty_show_header_cta() {
	return (bool) get_theme_mod( 'hvn_realty_show_header_cta', false );
}

/**
 * Get header CTA text.
 *
 * @return string
 */
function hvn_realty_get_header_cta_text() {
	return (string) get_theme_mod( 'hvn_realty_header_cta_text', '' );
}

/**
 * Get header CTA URL.
 *
 * @return string
 */
function hvn_realty_get_header_cta_url() {
	$url = get_theme_mod( 'hvn_realty_header_cta_url', '' );
	return $url ? esc_url( $url ) : esc_url( home_url( '/' ) );
}

/**
 * Get footer column count.
 *
 * @return int
 */
function hvn_realty_get_footer_columns() {
	return hvn_realty_sanitize_footer_columns( get_theme_mod( 'hvn_realty_footer_columns', 4 ) );
}

/**
 * Whether back-to-top button is enabled.
 *
 * @return bool
 */
function hvn_realty_show_back_to_top() {
	return (bool) get_theme_mod( 'hvn_realty_show_back_to_top', false );
}

/**
 * Get container layout mode.
 *
 * @return string boxed|full
 */
function hvn_realty_get_container_mode() {
	return hvn_realty_sanitize_container_mode( get_theme_mod( 'hvn_realty_container_mode', 'boxed' ) );
}

/**
 * Get base font size in px.
 *
 * @return int
 */
function hvn_realty_get_base_font_size() {
	return absint( get_theme_mod( 'hvn_realty_body_font_size', 16 ) );
}

/**
 * Text setting => preview selector bindings for Customizer postMessage.
 *
 * Extend this map to give new text settings live preview automatically.
 *
 * @return array<string, array{selector: string, toggleHidden?: bool, html?: bool}>
 */
function hvn_realty_get_customizer_text_preview_bindings() {
	return apply_filters(
		'hvn_realty_customizer_text_preview_bindings',
		array(
			'hvn_realty_hero_search_title'       => array(
				'selector' => '#hvn-realty-hero-search-title',
			),
			'hvn_realty_hero_search_subtitle'    => array(
				'selector'      => '#hvn-realty-hero-search-subtitle',
				'toggleHidden'  => true,
			),
			'hvn_realty_hero_search_button_text' => array(
				'selector' => '.hvn-realty-hero-search__submit',
			),
			'hvn_realty_hero_search_tabs_label'  => array(
				'selector'      => '#hvn-realty-hero-search-tabs-label',
				'toggleHidden'  => true,
			),
			'hvn_realty_home_property_types_title'    => array(
				'selector' => '#hvn-realty-property-types-title',
			),
			'hvn_realty_home_property_types_subtitle' => array(
				'selector' => '#hvn-realty-section-property-types .hvn-realty-section__subtitle',
			),
			'hvn_realty_home_testimonials_title'      => array(
				'selector' => '#hvn-realty-testimonials-title',
			),
			'hvn_realty_home_testimonials_subtitle'   => array(
				'selector' => '#hvn-realty-section-testimonials .hvn-realty-section__subtitle',
			),
		)
	);
}
