<?php
/**
 * Customizer selective refresh partials.
 *
 * @package Havenlytics_Realty
 */

/**
 * Register selective refresh partials for theme settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function hvn_realty_customizer_selective_refresh( $wp_customize ) {
	if ( ! isset( $wp_customize->selective_refresh ) ) {
		return;
	}

	$wp_customize->selective_refresh->add_partial(
		'hvn_realty_copyright_text',
		array(
			'selector'            => '.hvn-theme-copyright',
			'container_inclusive' => true,
			'render_callback'     => 'hvn_realty_customize_partial_copyright',
		)
	);

	/*
	 * Homepage sections — one selective-refresh partial per section so that
	 * every content control (text, titles, subtitles, descriptions, buttons,
	 * counts, repeaters, images, badges, stats) updates the live preview with a
	 * lightweight partial refresh instead of a full page reload. Colors,
	 * backgrounds, spacing, typography and visibility are handled instantly via
	 * postMessage in assets/js/customizer.js and are intentionally excluded here.
	 */
	foreach ( hvn_realty_get_home_section_partial_map() as $slug => $partial ) {
		if ( empty( $partial['settings'] ) ) {
			continue;
		}
		$wp_customize->selective_refresh->add_partial(
			'hvn_realty_home_section_' . $slug,
			array(
				'selector'            => $partial['selector'],
				'settings'            => $partial['settings'],
				'container_inclusive' => true,
				'render_callback'     => function () use ( $slug ) {
					get_template_part( 'template-parts/home/section', $slug );
				},
			)
		);
	}
}
add_action( 'customize_register', 'hvn_realty_customizer_selective_refresh', 25 );

/**
 * Map each homepage section to its preview selector and the content settings
 * that should trigger a partial refresh of that section.
 *
 * @return array<string, array{selector:string, settings:string[]}>
 */
function hvn_realty_get_home_section_partial_map() {
	return array(
		'hero'         => array(
			'selector' => '#hvn-theme-home-hero',
			'settings' => array(
				'hvn_realty_home_hero_image_a',
				'hvn_realty_home_hero_image_b',
				'hvn_realty_home_hero_stat1_value',
				'hvn_realty_home_hero_stat2_value',
				'hvn_realty_home_hero_stat3_value',
			),
		),
		'search'       => array(
			'selector' => '#hvn-theme-home-search',
			'settings' => array(
				'hvn_realty_home_search_fields',
			),
		),
		'why'          => array(
			'selector' => '.hvn-theme-home-why',
			'settings' => array(
				'hvn_realty_home_why_items',
			),
		),
		'properties'   => array(
			'selector' => '#hvn-theme-home-properties',
			'settings' => array(
				'hvn_realty_home_featured_count',
			),
		),
		'agents'       => array(
			'selector' => '#hvn-theme-home-agents',
			'settings' => array(
				'hvn_realty_home_agents_count',
			),
		),
		'testimonials' => array(
			'selector' => '#hvn-theme-home-testimonials',
			'settings' => array(
				'hvn_realty_home_testimonials',
				'hvn_realty_home_show_testimonial_stars',
				'hvn_realty_home_testimonials_autoplay',
				'hvn_realty_home_testimonials_speed',
			),
		),
		'blog'         => array(
			'selector' => '#hvn-theme-home-blog',
			'settings' => array(
				'hvn_realty_home_blog_count',
			),
		),
	);
}

/**
 * Render copyright partial.
 */
function hvn_realty_customize_partial_copyright() {
	if ( ! function_exists( 'hvn_realty_get_copyright_text' ) ) {
		return;
	}

	echo hvn_realty_get_copyright_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

