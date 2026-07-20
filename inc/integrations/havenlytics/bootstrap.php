<?php
/**
 * Havenlytics integration bootstrap.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_load_theme_file' ) ) {
	return;
}

$integration_files = array(
	'helpers.php',
	'header-auth.php',
	'carousel.php',
	'homepage-settings.php',
	'homepage-property-types.php',
	'homepage-testimonials.php',
	'hero-search.php',
	'mobile-search-drawer.php',
	'homepage.php',
	'homepage-assets.php',
	'breadcrumbs.php',
	'body-classes.php',
	'plugin-shell.php',
	'assets.php',
);

foreach ( $integration_files as $integration_file ) {
	hvn_realty_load_theme_file( 'inc/integrations/havenlytics/' . $integration_file, true );
}
