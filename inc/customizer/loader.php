<?php
/**
 * Customizer module loader.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_load_theme_file' ) ) {
	$theme_loader = get_template_directory() . '/inc/theme-loader.php';
	if ( file_exists( $theme_loader ) ) {
		require_once $theme_loader;
	}
}

if ( ! function_exists( 'hvn_realty_load_theme_file' ) ) {
	return;
}

/**
 * Customizer PHP files required for core Customizer operation.
 *
 * @return string[] Filenames relative to inc/customizer/.
 */
function hvn_realty_get_required_customizer_files() {
	return array(
		'helpers.php',
		'customizer-ui.php',
		'controls.php',
		'sections.php',
		'sections-homepage.php',
		'homepage-style.php',
		'panel-integration.php',
		'selective-refresh.php',
		'css-output.php',
		'homepage-css-output.php',
	);
}

/**
 * Customizer control classes — optional UI enhancements.
 *
 * @return string[] Filenames relative to inc/customizer/.
 */
function hvn_realty_get_optional_customizer_control_files() {
	return array(
		'class-hvn-realty-testimonials-control.php',
		'class-hvn-realty-section-order-control.php',
		'class-hvn-realty-search-builder-control.php',
		'class-hvn-realty-why-control.php',
	);
}

foreach ( hvn_realty_get_required_customizer_files() as $customizer_file ) {
	hvn_realty_load_theme_file( 'inc/customizer/' . $customizer_file, false );
}

/**
 * Load optional Customizer control classes when WordPress core control base exists.
 *
 * These files must not load during theme bootstrap: they extend WP_Customize_Control,
 * which is unavailable until the Customizer boots.
 *
 * @return void
 */
function hvn_realty_load_optional_customizer_control_classes() {
	if ( ! class_exists( 'WP_Customize_Control' ) ) {
		return;
	}

	foreach ( hvn_realty_get_optional_customizer_control_files() as $customizer_file ) {
		$relative_path = 'inc/customizer/' . $customizer_file;
		$file          = get_template_directory() . '/' . $relative_path;

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}
add_action( 'customize_register', 'hvn_realty_load_optional_customizer_control_classes', 1 );

if ( function_exists( 'hvn_realty_register_optional_customizer_controls_notice' ) ) {
	hvn_realty_register_optional_customizer_controls_notice();
}
