<?php
/**
 * Default logo and site icon import on fresh theme activation.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option: imported default custom logo attachment ID. */
define( 'HVN_REALTY_DEFAULT_LOGO_ID_OPTION', 'hvn_realty_default_logo_id' );

/** Option: imported default site icon attachment ID. */
define( 'HVN_REALTY_DEFAULT_SITE_ICON_ID_OPTION', 'hvn_realty_default_site_icon_id' );

/** Option: default branding import has run once for this site. */
define( 'HVN_REALTY_DEFAULT_BRANDING_DONE_OPTION', 'hvn_realty_default_branding_done' );

/**
 * Theme-relative default branding assets.
 *
 * @return array<string, array<string, string>>
 */
function hvn_realty_get_default_branding_assets() {
	return array(
		'logo' => array(
			'path'         => 'assets/admin/img/havenlytics-realty.png',
			'option'       => HVN_REALTY_DEFAULT_LOGO_ID_OPTION,
			'meta_type'    => 'logo',
			'post_title'   => 'Havenlytics Realty Logo',
		),
		'site_icon' => array(
			'path'         => 'assets/admin/img/havenlytics-favicon.png',
			'option'       => HVN_REALTY_DEFAULT_SITE_ICON_ID_OPTION,
			'meta_type'    => 'site_icon',
			'post_title'   => 'Havenlytics Realty Site Icon',
		),
	);
}

/**
 * Import default logo and site icon once on fresh activation.
 *
 * @return void
 */
function hvn_realty_maybe_import_default_branding() {
	if ( 'havenlytics-realty' !== get_template() ) {
		return;
	}

	if ( get_option( HVN_REALTY_DEFAULT_BRANDING_DONE_OPTION, false ) ) {
		return;
	}

	$needs_logo = ! hvn_realty_site_has_custom_logo();
	$needs_icon = ! hvn_realty_site_has_site_icon();

	if ( ! $needs_logo && ! $needs_icon ) {
		update_option( HVN_REALTY_DEFAULT_BRANDING_DONE_OPTION, true, false );
		return;
	}

	if ( $needs_logo ) {
		$logo_id = hvn_realty_get_or_import_default_branding_attachment( 'logo' );
		if ( $logo_id > 0 ) {
			set_theme_mod( 'custom_logo', $logo_id );
		}
	}

	if ( ! hvn_realty_site_has_site_icon() ) {
		$icon_id = hvn_realty_get_or_import_default_branding_attachment( 'site_icon' );
		if ( $icon_id > 0 ) {
			update_option( 'site_icon', $icon_id );
		}
	}

	update_option( HVN_REALTY_DEFAULT_BRANDING_DONE_OPTION, true, false );
}
add_action( 'after_switch_theme', 'hvn_realty_maybe_import_default_branding', 7 );

/**
 * Whether the site already has a custom logo assigned.
 *
 * @return bool
 */
function hvn_realty_site_has_custom_logo() {
	if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) {
		return true;
	}

	return (int) get_theme_mod( 'custom_logo', 0 ) > 0;
}

/**
 * Whether the site already has a site icon assigned.
 *
 * @return bool
 */
function hvn_realty_site_has_site_icon() {
	return (int) get_option( 'site_icon', 0 ) > 0;
}

/**
 * Get or import a default branding attachment.
 *
 * @param string $type logo|site_icon.
 * @return int Attachment ID or 0.
 */
function hvn_realty_get_or_import_default_branding_attachment( $type ) {
	$assets = hvn_realty_get_default_branding_assets();

	if ( ! isset( $assets[ $type ] ) ) {
		return 0;
	}

	$config = $assets[ $type ];
	$stored = (int) get_option( $config['option'], 0 );

	if ( $stored > 0 && get_post( $stored ) && 'attachment' === get_post_type( $stored ) ) {
		return $stored;
	}

	$existing = hvn_realty_find_default_branding_attachment( $config['path'] );
	if ( $existing > 0 ) {
		update_option( $config['option'], $existing, false );
		return $existing;
	}

	$attachment_id = hvn_realty_import_default_branding_file( $config );

	if ( $attachment_id > 0 ) {
		update_option( $config['option'], $attachment_id, false );
	}

	return $attachment_id;
}

/**
 * Find a previously imported default branding attachment by source path.
 *
 * @param string $relative_path Theme-relative file path.
 * @return int
 */
function hvn_realty_find_default_branding_attachment( $relative_path ) {
	$relative_path = ltrim( str_replace( '\\', '/', (string) $relative_path ), '/' );

	$query = new WP_Query(
		array(
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => array(
				array(
					'key'   => '_hvn_realty_default_branding_source',
					'value' => $relative_path,
				),
			),
		)
	);

	if ( empty( $query->posts[0] ) ) {
		return 0;
	}

	return (int) $query->posts[0];
}

/**
 * Import a bundled branding file into the Media Library.
 *
 * @param array<string, string> $config Asset config.
 * @return int Attachment ID or 0.
 */
function hvn_realty_import_default_branding_file( $config ) {
	$relative_path = ltrim( $config['path'], '/' );
	$file          = get_template_directory() . '/' . $relative_path;

	if ( ! file_exists( $file ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$filename = basename( $file );
	$contents = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	if ( false === $contents ) {
		return 0;
	}

	$upload = wp_upload_bits( $filename, null, $contents );

	if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
		return 0;
	}

	$filetype = wp_check_filetype( $filename, null );
	$title    = ! empty( $config['post_title'] ) ? $config['post_title'] : sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) );

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return 0;
	}

	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	update_post_meta( $attachment_id, '_hvn_realty_default_branding', $config['meta_type'] );
	update_post_meta( $attachment_id, '_hvn_realty_default_branding_source', $relative_path );

	return (int) $attachment_id;
}
