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
				'hvn_realty_home_hero_eyebrow',
				'hvn_realty_home_hero_title_before',
				'hvn_realty_home_hero_title_highlight',
				'hvn_realty_home_hero_title_after',
				'hvn_realty_home_hero_subtitle',
				'hvn_realty_home_hero_stat1_value',
				'hvn_realty_home_hero_stat1_label',
				'hvn_realty_home_hero_stat1_suffix',
				'hvn_realty_home_hero_stat2_value',
				'hvn_realty_home_hero_stat2_label',
				'hvn_realty_home_hero_stat2_suffix',
				'hvn_realty_home_hero_stat3_value',
				'hvn_realty_home_hero_stat3_label',
				'hvn_realty_home_hero_stat3_suffix',
			),
		),
		'search'       => array(
			'selector' => '#hvn-theme-home-search',
			'settings' => array(
				'hvn_realty_home_search_fields',
			),
		),
		'why'          => array(
			'selector' => '#hvn-theme-home-why',
			'settings' => array(
				'hvn_realty_home_why_items',
				'hvn_realty_home_why_eyebrow',
				'hvn_realty_home_why_title',
				'hvn_realty_home_why_subtitle',
			),
		),
		'properties'   => array(
			'selector' => '#hvn-theme-home-properties',
			'settings' => array(
				'hvn_realty_home_featured_count',
				'hvn_realty_home_featured_title',
				'hvn_realty_home_featured_subtitle',
				'hvn_realty_home_featured_description',
				'hvn_realty_home_featured_columns_desktop',
				'hvn_realty_home_featured_columns_tablet',
				'hvn_realty_home_featured_columns_mobile',
				'hvn_realty_home_featured_view_all_text',
				'hvn_realty_home_featured_view_all_url',
			),
		),
		'types'        => array(
			'selector' => '#hvn-theme-home-types',
			'settings' => array(
				'hvn_realty_home_property_types_title',
				'hvn_realty_home_property_types_subtitle',
			),
		),
		'locations'    => array(
			'selector' => '#hvn-theme-home-locations',
			'settings' => array(
				'hvn_realty_home_locations_title',
				'hvn_realty_home_locations_subtitle',
				'hvn_realty_home_locations_text',
			),
		),
		'map'          => array(
			'selector' => '#hvn-theme-home-map',
			'settings' => array(
				'hvn_realty_home_map_limit',
				'hvn_realty_home_map_title',
				'hvn_realty_home_map_subtitle',
				'hvn_realty_home_map_text',
			),
		),
		'collections'  => array(
			'selector' => '#hvn-theme-home-collections',
			'settings' => array(
				'hvn_realty_home_collections_title',
				'hvn_realty_home_collections_subtitle',
			),
		),
		'process'      => array(
			'selector' => '#hvn-theme-home-process',
			'settings' => array(
				'hvn_realty_home_process_eyebrow',
				'hvn_realty_home_process_title',
				'hvn_realty_home_process_subtitle',
				'hvn_realty_home_process_step1_title',
				'hvn_realty_home_process_step1_text',
				'hvn_realty_home_process_step1_url',
				'hvn_realty_home_process_step2_title',
				'hvn_realty_home_process_step2_text',
				'hvn_realty_home_process_step2_url',
				'hvn_realty_home_process_step3_title',
				'hvn_realty_home_process_step3_text',
				'hvn_realty_home_process_step3_url',
				'hvn_realty_home_process_step4_title',
				'hvn_realty_home_process_step4_text',
				'hvn_realty_home_process_step4_url',
			),
		),
		'agents'       => array(
			'selector' => '#hvn-theme-home-agents',
			'settings' => array(
				'hvn_realty_home_agents_count',
				'hvn_realty_home_agents_title',
				'hvn_realty_home_agents_subtitle',
			),
		),
		'testimonials' => array(
			'selector' => '#hvn-theme-home-testimonials',
			'settings' => array(
				'hvn_realty_home_testimonials',
				'hvn_realty_home_testimonials_title',
				'hvn_realty_home_testimonials_subtitle',
				'hvn_realty_home_show_testimonial_stars',
				'hvn_realty_home_testimonials_autoplay',
				'hvn_realty_home_testimonials_speed',
			),
		),
		'blog'         => array(
			'selector' => '#hvn-theme-home-blog',
			'settings' => array(
				'hvn_realty_home_blog_count',
				'hvn_realty_home_blog_title',
				'hvn_realty_home_blog_subtitle',
			),
		),
		'cta'          => array(
			'selector' => '#hvn-theme-home-cta',
			'settings' => array(
				'hvn_realty_home_cta_title',
				'hvn_realty_home_cta_subtitle',
				'hvn_realty_home_cta_primary_label',
				'hvn_realty_home_cta_primary_url',
				'hvn_realty_home_cta_secondary_label',
				'hvn_realty_home_cta_secondary_url',
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
