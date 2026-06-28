<?php
/**
 * Homepage section styling — Customizer helpers and dynamic CSS.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registry of homepage section slugs => human labels.
 *
 * @return array<string, string>
 */
function hvn_realty_get_home_section_registry() {
	$registry = array(
		'hero'          => __( 'Hero', 'havenlytics-realty' ),
		'search'        => __( 'Search Panel', 'havenlytics-realty' ),
		'why'           => __( 'Why Choose Us', 'havenlytics-realty' ),
		'properties'    => __( 'Featured Properties', 'havenlytics-realty' ),
		'types'         => __( 'Property Types', 'havenlytics-realty' ),
		'locations'     => __( 'Locations', 'havenlytics-realty' ),
		'agents'        => __( 'Agents', 'havenlytics-realty' ),
		'testimonials'  => __( 'Testimonials', 'havenlytics-realty' ),
		'blog'          => __( 'Latest Blog', 'havenlytics-realty' ),
		'cta'           => __( 'Call to Action', 'havenlytics-realty' ),
	);

	/**
	 * Filter homepage section registry used by the section-order control.
	 *
	 * @param array<string, string> $registry slug => label.
	 */
	return apply_filters( 'hvn_realty_home_section_registry', $registry );
}

/**
 * Default homepage section order.
 *
 * @return string[]
 */
function hvn_realty_get_default_home_section_order() {
	return array_keys( hvn_realty_get_home_section_registry() );
}

/**
 * Sanitize homepage section order JSON / array.
 *
 * @param mixed $value Raw value.
 * @return string JSON array of slugs.
 */
function hvn_realty_sanitize_home_section_order( $value ) {
	$allowed = array_keys( hvn_realty_get_home_section_registry() );
	$decoded = array();

	if ( is_string( $value ) ) {
		$parsed = json_decode( $value, true );
		if ( is_array( $parsed ) ) {
			$decoded = $parsed;
		}
	} elseif ( is_array( $value ) ) {
		$decoded = $value;
	}

	$order = array();
	foreach ( $decoded as $slug ) {
		$slug = sanitize_key( (string) $slug );
		if ( in_array( $slug, $allowed, true ) && ! in_array( $slug, $order, true ) ) {
			$order[] = $slug;
		}
	}

	foreach ( $allowed as $slug ) {
		if ( ! in_array( $slug, $order, true ) ) {
			$order[] = $slug;
		}
	}

	return wp_json_encode( array_values( $order ) );
}

/**
 * Map section slug to a frontend CSS selector.
 *
 * @param string $slug Section slug.
 * @return string
 */
function hvn_realty_get_home_section_css_selector( $slug ) {
	$map = array(
		'hero'         => '#hvn-theme-home-hero',
		'search'       => '#hvn-theme-home-search-form',
		'why'          => '.hvn-theme-home-why',
		'properties'   => '#hvn-theme-home-properties',
		'types'        => '#hvn-theme-home-types',
		'locations'    => '#hvn-theme-home-locations',
		'agents'       => '#hvn-theme-home-agents',
		'testimonials' => '#hvn-theme-home-testimonials',
		'blog'         => '#hvn-theme-home-blog',
		'cta'          => '.hvn-theme-home-cta',
	);

	return isset( $map[ $slug ] ) ? $map[ $slug ] : '';
}

/**
 * Visibility theme_mod key for a section slug.
 *
 * @param string $slug Section slug.
 * @return string
 */
function hvn_realty_get_home_section_visibility_mod( $slug ) {
	$map = array(
		'properties' => 'hvn_realty_home_show_properties',
		'types'      => 'hvn_realty_home_show_types',
	);

	if ( isset( $map[ $slug ] ) ) {
		return $map[ $slug ];
	}

	return 'hvn_realty_home_show_' . sanitize_key( $slug );
}

/**
 * Sanitize optional section padding (0–200 px).
 *
 * @param mixed $value Raw value.
 * @return int
 */
function hvn_realty_sanitize_home_section_spacing( $value ) {
	$value = absint( $value );
	return min( 200, $value );
}

/**
 * Register per-section style controls (colors, spacing, animation).
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 * @param string               $slug         Section slug.
 * @param string               $section_id   Customizer section ID.
 * @return void
 */
function hvn_realty_home_register_section_style_controls( $wp_customize, $slug, $section_id ) {
	$prefix = 'hvn_realty_home_style_' . sanitize_key( $slug );

	// The Hero uses a gradient background control (three colour stops) instead of
	// a single flat background colour. Defaults match the shipped hero gradient.
	if ( 'hero' === $slug ) {
		$hero_gradient = array(
			$prefix . '_grad_top'    => array(
				'label'   => esc_html__( 'Background gradient — top colour', 'havenlytics-realty' ),
				'default' => '#151a1f',
			),
			$prefix . '_grad_mid'    => array(
				'label'   => esc_html__( 'Background gradient — middle colour', 'havenlytics-realty' ),
				'default' => '#1f3a3a',
			),
			$prefix . '_grad_bottom' => array(
				'label'   => esc_html__( 'Background gradient — bottom colour', 'havenlytics-realty' ),
				'default' => '#2a4c4a',
			),
		);

		foreach ( $hero_gradient as $grad_id => $grad_config ) {
			$wp_customize->add_setting(
				$grad_id,
				array(
					'default'           => $grad_config['default'],
					'sanitize_callback' => 'sanitize_hex_color',
					'transport'         => 'postMessage',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$grad_id,
					array(
						'label'   => $grad_config['label'],
						'section' => $section_id,
					)
				)
			);
		}
	}

	$controls = array();

	// Every section except the Hero exposes a flat background colour.
	if ( 'hero' !== $slug ) {
		$controls[ $prefix . '_bg' ] = array(
			'label'   => esc_html__( 'Background Color', 'havenlytics-realty' ),
			'type'    => 'color',
			'default' => '',
		);
	}

	$controls += array(
		$prefix . '_text' => array(
			'label'   => esc_html__( 'Text Color', 'havenlytics-realty' ),
			'type'    => 'color',
			'default' => '',
		),
		$prefix . '_pad_top' => array(
			'label'   => esc_html__( 'Spacing Top (px)', 'havenlytics-realty' ),
			'type'    => 'number',
			'default' => '',
			'attrs'   => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
		$prefix . '_pad_bottom' => array(
			'label'   => esc_html__( 'Spacing Bottom (px)', 'havenlytics-realty' ),
			'type'    => 'number',
			'default' => '',
			'attrs'   => array( 'min' => 0, 'max' => 200, 'step' => 4 ),
		),
	);

	foreach ( $controls as $id => $config ) {
		$sanitize = 'color' === $config['type'] ? 'sanitize_hex_color' : 'hvn_realty_sanitize_home_section_spacing';

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => $sanitize,
				'transport'         => 'postMessage',
			)
		);

		if ( 'color' === $config['type'] ) {
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$id,
					array(
						'label'   => $config['label'],
						'section' => $section_id,
					)
				)
			);
		} else {
			$wp_customize->add_control(
				$id,
				array(
					'label'       => $config['label'],
					'section'     => $section_id,
					'type'        => 'number',
					'input_attrs' => $config['attrs'],
				)
			);
		}
	}

	$wp_customize->add_setting(
		$prefix . '_animate',
		array(
			'default'           => true,
			'sanitize_callback' => 'hvn_realty_sanitize_checkbox',
			'transport'         => 'postMessage',
		)
	);
	$wp_customize->add_control(
		$prefix . '_animate',
		array(
			'label'   => esc_html__( 'Enable scroll animation', 'havenlytics-realty' ),
			'section' => $section_id,
			'type'    => 'checkbox',
		)
	);
}

/**
 * Build dynamic CSS for homepage section Customizer styles.
 *
 * @return string
 */
function hvn_realty_get_homepage_sections_custom_css() {
	$rules = array();

	foreach ( array_keys( hvn_realty_get_home_section_registry() ) as $slug ) {
		$selector = hvn_realty_get_home_section_css_selector( $slug );
		if ( '' === $selector ) {
			continue;
		}

		$prefix = 'hvn_realty_home_style_' . $slug;
		$text   = sanitize_hex_color( get_theme_mod( $prefix . '_text', '' ) );
		$top    = absint( get_theme_mod( $prefix . '_pad_top', 0 ) );
		$bottom = absint( get_theme_mod( $prefix . '_pad_bottom', 0 ) );
		$animate = (bool) get_theme_mod( $prefix . '_animate', true );

		$declarations = array();

		if ( 'hero' === $slug ) {
			$grad_top    = sanitize_hex_color( get_theme_mod( $prefix . '_grad_top', '#151a1f' ) );
			$grad_mid    = sanitize_hex_color( get_theme_mod( $prefix . '_grad_mid', '#1f3a3a' ) );
			$grad_bottom = sanitize_hex_color( get_theme_mod( $prefix . '_grad_bottom', '#2a4c4a' ) );

			if ( $grad_top && $grad_mid && $grad_bottom ) {
				$declarations[] = sprintf(
					'background:linear-gradient(180deg,%1$s 0%%,%2$s 64%%,%3$s 100%%)',
					$grad_top,
					$grad_mid,
					$grad_bottom
				);
			}
		} else {
			$bg = sanitize_hex_color( get_theme_mod( $prefix . '_bg', '' ) );
			if ( $bg ) {
				// background-image:none guarantees the chosen colour wins even on
				// sections whose default paint is a gradient (e.g. the CTA).
				$declarations[] = 'background-color:' . $bg;
				$declarations[] = 'background-image:none';
			}
		}

		if ( $text ) {
			$declarations[] = 'color:' . $text;
		}
		if ( $top > 0 ) {
			$declarations[] = 'padding-top:' . $top . 'px';
		}
		if ( $bottom > 0 ) {
			$declarations[] = 'padding-bottom:' . $bottom . 'px';
		}

		if ( ! empty( $declarations ) ) {
			$rules[] = 'body.hvn-theme-home ' . $selector . '{' . implode( ';', $declarations ) . '}';
		}

		if ( ! $animate ) {
			$rules[] = 'body.hvn-theme-home ' . $selector . ' .hvn-theme-home-reveal{opacity:1;transform:none}';
		}
	}

	return implode( "\n", $rules );
}

/**
 * Output homepage section custom CSS on the frontend and in the Customizer preview.
 *
 * @return void
 */
function hvn_realty_output_homepage_sections_custom_css() {
	if ( ! function_exists( 'hvn_realty_is_home_design' ) || ! hvn_realty_is_home_design() ) {
		return;
	}

	$css = hvn_realty_get_homepage_sections_custom_css();
	if ( '' === $css ) {
		return;
	}

	wp_add_inline_style( 'hvn-realty-home', $css );
}
// Priority 30 ensures this runs AFTER the hvn-realty-home stylesheet is enqueued
// (hvn_realty_enqueue_havenlytics_assets, priority 25). wp_add_inline_style()
// silently drops CSS when its target handle is not yet registered, which is why
// homepage section background colours previously never rendered on the frontend.
add_action( 'wp_enqueue_scripts', 'hvn_realty_output_homepage_sections_custom_css', 30 );

/**
 * Output homepage section CSS in the Customizer preview iframe.
 *
 * @return void
 */
function hvn_realty_customizer_preview_homepage_section_css() {
	$css = hvn_realty_get_homepage_sections_custom_css();
	if ( '' === $css ) {
		return;
	}

	wp_add_inline_style( 'hvn-realty-home', $css );
}
add_action( 'customize_preview_init', 'hvn_realty_customizer_preview_homepage_section_css', 25 );

/* =====================================================================
 * Why Choose Us — repeater data layer (2.0.2)
 * ===================================================================== */

/**
 * Icon choices for the Why Choose Us repeater (key => label).
 *
 * @return array<string, string>
 */
function hvn_realty_get_why_icon_choices() {
	return array(
		'shield' => __( 'Shield / Verified', 'havenlytics-realty' ),
		'chart'  => __( 'Chart / Data', 'havenlytics-realty' ),
		'users'  => __( 'People / Agents', 'havenlytics-realty' ),
		'scale'  => __( 'Scale / Transparency', 'havenlytics-realty' ),
		'home'   => __( 'Home', 'havenlytics-realty' ),
		'key'    => __( 'Key', 'havenlytics-realty' ),
		'check'  => __( 'Check', 'havenlytics-realty' ),
		'star'   => __( 'Star', 'havenlytics-realty' ),
		'pin'    => __( 'Map Pin', 'havenlytics-realty' ),
		'clock'  => __( 'Clock', 'havenlytics-realty' ),
	);
}

/**
 * SVG markup for a Why Choose Us icon key (stroke inherits via CSS).
 *
 * @param string $key Icon key.
 * @return string
 */
function hvn_realty_get_why_icon_svg( $key ) {
	$icons = array(
		'shield' => '<path d="M11 2L20 7V20H2V7L11 2Z" stroke-width="1.6" stroke-linejoin="round"/>',
		'chart'  => '<path d="M3 18V10M11 18V4M19 18V13" stroke-width="1.6" stroke-linecap="round"/>',
		'users'  => '<circle cx="11" cy="8" r="4" stroke-width="1.6"/><path d="M3 20C3 15.5 6.5 13 11 13C15.5 13 19 15.5 19 20" stroke-width="1.6" stroke-linecap="round"/>',
		'scale'  => '<path d="M11 2V20M4 7H18M4 15H18" stroke-width="1.6" stroke-linecap="round"/>',
		'home'   => '<path d="M3 9L11 3L19 9V19H13V14H9V19H3V9Z" stroke-width="1.6" stroke-linejoin="round"/>',
		'key'    => '<circle cx="7" cy="7" r="4" stroke-width="1.6"/><path d="M10 10L19 19M16 16L18 14M14 14L16 12" stroke-width="1.6" stroke-linecap="round"/>',
		'check'  => '<circle cx="11" cy="11" r="9" stroke-width="1.6"/><path d="M7 11L10 14L15 8" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>',
		'star'   => '<path d="M11 2L13.5 8L20 8.5L15 13L16.5 19.5L11 16L5.5 19.5L7 13L2 8.5L8.5 8Z" stroke-width="1.6" stroke-linejoin="round"/>',
		'pin'    => '<path d="M11 21C11 21 18 14.5 18 9A7 7 0 0 0 4 9C4 14.5 11 21 11 21Z" stroke-width="1.6" stroke-linejoin="round"/><circle cx="11" cy="9" r="2.5" stroke-width="1.6"/>',
		'clock'  => '<circle cx="11" cy="11" r="9" stroke-width="1.6"/><path d="M11 6V11L14.5 13" stroke-width="1.6" stroke-linecap="round"/>',
	);

	$key  = is_string( $key ) ? $key : '';
	$path = isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['shield'];

	return '<svg width="22" height="22" viewBox="0 0 22 22" fill="none" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Default Why Choose Us items for new installs.
 *
 * @return array<int, array<string, string>>
 */
function hvn_realty_get_default_home_why_items() {
	return array(
		array(
			'icon'  => 'shield',
			'title' => __( 'Verified Listings', 'havenlytics-realty' ),
			'text'  => __( "Every property is inspected and cross-checked against public records before it's published.", 'havenlytics-realty' ),
			'url'   => '',
		),
		array(
			'icon'  => 'chart',
			'title' => __( 'Live Market Data', 'havenlytics-realty' ),
			'text'  => __( 'Price trends, days-on-market, and comparable sales refresh daily across every neighborhood.', 'havenlytics-realty' ),
			'url'   => '',
		),
		array(
			'icon'  => 'users',
			'title' => __( 'Vetted Agents', 'havenlytics-realty' ),
			'text'  => __( 'Our agent network is screened on closing rates, client reviews, and local specialization.', 'havenlytics-realty' ),
			'url'   => '',
		),
		array(
			'icon'  => 'scale',
			'title' => __( 'Transparent Fees', 'havenlytics-realty' ),
			'text'  => __( 'No hidden commissions. Every cost is itemized before you sign anything.', 'havenlytics-realty' ),
			'url'   => '',
		),
	);
}

/**
 * Sanitize Why Choose Us repeater JSON for theme_mod storage.
 *
 * @param mixed $input Raw value.
 * @return string JSON string.
 */
function hvn_realty_sanitize_home_why_items( $input ) {
	$items = array();

	if ( is_string( $input ) ) {
		$decoded = json_decode( wp_unslash( $input ), true );
		if ( is_array( $decoded ) ) {
			$items = $decoded;
		}
	} elseif ( is_array( $input ) ) {
		$items = $input;
	}

	$allowed_icons = array_keys( hvn_realty_get_why_icon_choices() );
	$sanitized     = array();

	foreach ( $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$title = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
		$text  = isset( $item['text'] ) ? sanitize_textarea_field( $item['text'] ) : '';

		if ( '' === $title && '' === $text ) {
			continue;
		}

		$icon = isset( $item['icon'] ) ? sanitize_key( $item['icon'] ) : 'shield';
		if ( ! in_array( $icon, $allowed_icons, true ) ) {
			$icon = 'shield';
		}

		$sanitized[] = array(
			'icon'  => $icon,
			'title' => $title,
			'text'  => $text,
			'url'   => isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '',
		);
	}

	return wp_json_encode( array_values( $sanitized ) );
}

/**
 * Resolve Why Choose Us items for templates.
 *
 * Order of precedence: repeater theme_mod → legacy per-card theme_mods → defaults.
 * Legacy values are read (never deleted) so existing sites keep their content.
 *
 * @return array<int, array<string, string>>
 */
function hvn_realty_get_home_why_items() {
	$raw = get_theme_mod( 'hvn_realty_home_why_items', '' );

	if ( is_string( $raw ) && '' !== $raw ) {
		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) && ! empty( $decoded ) ) {
			$items = array();
			foreach ( $decoded as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				$items[] = array(
					'icon'  => isset( $item['icon'] ) ? (string) $item['icon'] : 'shield',
					'title' => isset( $item['title'] ) ? (string) $item['title'] : '',
					'text'  => isset( $item['text'] ) ? (string) $item['text'] : '',
					'url'   => isset( $item['url'] ) ? (string) $item['url'] : '',
				);
			}
			if ( ! empty( $items ) ) {
				return $items;
			}
		}
	}

	// Legacy fallback: migrate the original four card_* settings if present.
	$defaults    = hvn_realty_get_default_home_why_items();
	$legacy_icons = array( 'shield', 'chart', 'users', 'scale' );
	$legacy       = array();

	for ( $i = 1; $i <= 4; $i++ ) {
		$title = get_theme_mod( 'hvn_realty_home_why_card' . $i . '_title', null );
		$text  = get_theme_mod( 'hvn_realty_home_why_card' . $i . '_text', null );

		if ( null === $title && null === $text ) {
			continue;
		}

		$title = (string) $title;
		$text  = (string) $text;

		if ( '' === $title && '' === $text ) {
			continue;
		}

		$legacy[] = array(
			'icon'  => $legacy_icons[ $i - 1 ],
			'title' => $title,
			'text'  => $text,
			'url'   => '',
		);
	}

	return ! empty( $legacy ) ? $legacy : $defaults;
}
