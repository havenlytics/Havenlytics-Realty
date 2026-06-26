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

	$wp_customize->selective_refresh->add_partial(
		'hvn_realty_header_cta_text',
		array(
			'selector'            => '.hvn-theme-header-cta',
			'container_inclusive' => true,
			'render_callback'     => 'hvn_realty_customize_partial_header_cta',
		)
	);

	if ( function_exists( 'hvn_realty_customizer_homepage_is_active' ) && hvn_realty_customizer_homepage_is_active() ) {
		$wp_customize->selective_refresh->add_partial(
			'hvn_realty_home_cta_headline',
			array(
				'selector'            => '#hvn-realty-cta-title',
				'container_inclusive' => false,
				'render_callback'     => function () {
					if ( function_exists( 'hvn_realty_get_home_cta_headline' ) ) {
						echo esc_html( hvn_realty_get_home_cta_headline() );
					}
				},
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'hvn_realty_home_cta_subtext',
			array(
				'selector'            => '.hvn-realty-cta__text',
				'container_inclusive' => false,
				'render_callback'     => function () {
					if ( function_exists( 'hvn_realty_get_home_cta_subtext' ) ) {
						echo esc_html( hvn_realty_get_home_cta_subtext() );
					}
				},
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'hvn_realty_home_cta_primary_text',
			array(
				'selector'            => '.hvn-realty-home-cta__primary',
				'container_inclusive' => false,
				'render_callback'     => function () {
					if ( function_exists( 'hvn_realty_get_home_cta_primary_text' ) ) {
						echo esc_html( hvn_realty_get_home_cta_primary_text() );
					}
				},
			)
		);

		$hero_search_partials = array(
			'hvn_realty_hero_search_title'       => array(
				'selector'        => '#hvn-realty-hero-search-title',
				'render_callback' => 'hvn_realty_customize_partial_hero_search_title',
			),
			'hvn_realty_hero_search_subtitle'    => array(
				'selector'            => '#hvn-realty-hero-search-subtitle',
				'container_inclusive' => true,
				'render_callback'     => 'hvn_realty_customize_partial_hero_search_subtitle',
			),
			'hvn_realty_hero_search_button_text' => array(
				'selector'        => '.hvn-realty-hero-search__submit',
				'render_callback' => 'hvn_realty_customize_partial_hero_search_button_text',
			),
			'hvn_realty_hero_search_tabs_label'  => array(
				'selector'        => '#hvn-realty-hero-search-tabs-label',
				'render_callback' => 'hvn_realty_customize_partial_hero_search_tabs_label',
			),
		);

		foreach ( $hero_search_partials as $setting_id => $partial_args ) {
			$wp_customize->selective_refresh->add_partial( $setting_id, $partial_args );
		}
	}
}
add_action( 'customize_register', 'hvn_realty_customizer_selective_refresh', 25 );

/**
 * Render copyright partial.
 */
function hvn_realty_customize_partial_copyright() {
	if ( ! function_exists( 'hvn_realty_get_copyright_text' ) ) {
		return;
	}

	echo hvn_realty_get_copyright_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Render header CTA partial.
 */
function hvn_realty_customize_partial_header_cta() {
	if ( ! function_exists( 'hvn_realty_show_header_cta' ) || ! hvn_realty_show_header_cta() ) {
		return;
	}

	if ( ! function_exists( 'hvn_realty_get_header_cta_text' ) || ! function_exists( 'hvn_realty_get_header_cta_url' ) ) {
		return;
	}

	$text = hvn_realty_get_header_cta_text();
	if ( '' === $text ) {
		return;
	}

	printf(
		'<a class="hvn-theme-btn hvn-theme-header-cta" href="%1$s">%2$s</a>',
		esc_url( hvn_realty_get_header_cta_url() ),
		esc_html( $text )
	);
}

/**
 * Render hero search title partial.
 */
function hvn_realty_customize_partial_hero_search_title() {
	if ( ! function_exists( 'hvn_realty_get_hero_search_title' ) ) {
		return;
	}

	echo esc_html( hvn_realty_get_hero_search_title() );
}

/**
 * Render hero search subtitle partial.
 */
function hvn_realty_customize_partial_hero_search_subtitle() {
	if ( ! function_exists( 'hvn_realty_get_hero_search_subtitle' ) ) {
		return;
	}

	$subtitle = hvn_realty_get_hero_search_subtitle();

	printf(
		'<p class="hvn-realty-hero-search__subtitle" id="hvn-realty-hero-search-subtitle"%1$s>%2$s</p>',
		'' === $subtitle ? ' hidden' : '',
		esc_html( $subtitle )
	);
}

/**
 * Render hero search button label partial.
 */
function hvn_realty_customize_partial_hero_search_button_text() {
	if ( ! function_exists( 'hvn_realty_get_hero_search_button_text' ) ) {
		return;
	}

	echo esc_html( hvn_realty_get_hero_search_button_text() );
}

/**
 * Render hero search tabs label partial.
 */
function hvn_realty_customize_partial_hero_search_tabs_label() {
	if ( ! function_exists( 'hvn_realty_get_hero_search_tabs_label' ) ) {
		return;
	}

	$label = hvn_realty_get_hero_search_tabs_label();

	printf(
		'<p class="hvn-realty-hero-search__tabs-label" id="hvn-realty-hero-search-tabs-label"%1$s>%2$s</p>',
		'' === $label ? ' hidden' : '',
		esc_html( $label )
	);
}
