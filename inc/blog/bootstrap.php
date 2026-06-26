<?php
/**
 * Blog module bootstrap — isolated from real estate / plugin features.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$blog_files = array(
	'template-tags.php',
	'layout.php',
	'shell.php',
	'templates.php',
	'a11y.php',
	'pagination.php',
	'assets.php',
);

foreach ( $blog_files as $blog_file ) {
	if ( function_exists( 'hvn_realty_load_theme_file' ) ) {
		hvn_realty_load_theme_file( 'inc/blog/' . $blog_file, false );
	} else {
		$file = get_template_directory() . '/inc/blog/' . $blog_file;
		if ( file_exists( $file ) ) {
			require_once $file;
		} elseif ( function_exists( 'hvn_realty_record_missing_theme_file' ) ) {
			hvn_realty_record_missing_theme_file( 'inc/blog/' . $blog_file, true );
			if ( function_exists( 'add_action' ) && ! has_action( 'admin_notices', 'hvn_realty_missing_theme_files_notice' ) ) {
				add_action( 'admin_notices', 'hvn_realty_missing_theme_files_notice' );
			}
		}
	}
}
