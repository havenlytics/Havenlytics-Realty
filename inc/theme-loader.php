<?php
/**
 * Safe theme file loader — prevents fatal errors when files are missing from a release package.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Record a missing theme file path for integrity reporting.
 *
 * @param string $relative_path Theme-relative path.
 * @param bool   $required      Whether the file is required.
 * @return void
 */
function hvn_realty_record_missing_theme_file( $relative_path, $required = true ) {
	$relative_path = ltrim( str_replace( '\\', '/', (string) $relative_path ), '/' );

	if ( '' === $relative_path ) {
		return;
	}

	if ( $required ) {
		if ( ! isset( $GLOBALS['hvn_realty_missing_theme_files'] ) || ! is_array( $GLOBALS['hvn_realty_missing_theme_files'] ) ) {
			$GLOBALS['hvn_realty_missing_theme_files'] = array();
		}

		if ( ! in_array( $relative_path, $GLOBALS['hvn_realty_missing_theme_files'], true ) ) {
			$GLOBALS['hvn_realty_missing_theme_files'][] = $relative_path;
		}
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		error_log( '[Havenlytics Realty] Missing theme file: ' . $relative_path ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}

/**
 * Safe require alias — load a theme PHP file when it exists.
 *
 * @param string $relative_path Path relative to the theme root.
 * @param bool   $optional      When false, registers an admin notice if missing.
 * @return bool True when the file was loaded.
 */
function hvn_realty_safe_require( $relative_path, $optional = true ) {
	return hvn_realty_load_theme_file( $relative_path, $optional );
}

/**
 * Load a theme PHP file when it exists on disk.
 *
 * @param string $relative_path Path relative to the theme root (e.g. inc/foo.php).
 * @param bool   $optional      When false, registers an admin notice if the file is missing.
 * @return bool True when the file was loaded.
 */
function hvn_realty_load_theme_file( $relative_path, $optional = true ) {
	$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $file ) ) {
		require_once $file;
		return true;
	}

	if ( ! $optional ) {
		hvn_realty_record_missing_theme_file( $relative_path, true );

		if ( ! has_action( 'admin_notices', 'hvn_realty_missing_theme_files_notice' ) ) {
			add_action( 'admin_notices', 'hvn_realty_missing_theme_files_notice' );
		}
	}

	return false;
}

/**
 * Load a template part only when the template file exists.
 *
 * @param string               $slug Template slug without .php.
 * @param string|null          $name Optional template name.
 * @param array<string, mixed> $args Optional arguments.
 * @return bool True when a template was loaded.
 */
function hvn_realty_safe_get_template_part( $slug, $name = null, $args = array() ) {
	$templates = array();

	if ( null !== $name && '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
	}

	$templates[] = "{$slug}.php";

	if ( ! locate_template( $templates, false, false ) ) {
		if ( class_exists( 'HVN_Realty_Asset_Loader', false ) ) {
			HVN_Realty_Asset_Loader::log_missing_template_part( $slug, $name );
		}

		return false;
	}

	get_template_part( $slug, $name, $args );

	return true;
}

/**
 * Enqueue a theme stylesheet when the file exists.
 *
 * @param string       $handle       Style handle.
 * @param string       $relative_path Path relative to theme root.
 * @param string[]     $deps         Dependencies.
 * @param string|false $version      Version string.
 * @return bool
 */
function hvn_realty_enqueue_theme_style( $handle, $relative_path, $deps = array(), $version = false, $media = 'all', $required = false ) {
	if ( function_exists( 'hvn_realty_enqueue_style_safe' ) ) {
		return hvn_realty_enqueue_style_safe( $handle, $relative_path, $deps, $version, $media, $required );
	}

	$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	if ( ! file_exists( $file ) ) {
		return false;
	}

	wp_enqueue_style(
		$handle,
		get_template_directory_uri() . '/' . ltrim( $relative_path, '/' ),
		$deps,
		$version ? $version : ( defined( 'HVN_REALTY_VERSION' ) ? HVN_REALTY_VERSION : false ),
		$media
	);

	return true;
}

/**
 * Enqueue a theme script when the file exists.
 *
 * @param string       $handle       Script handle.
 * @param string       $relative_path Path relative to theme root.
 * @param string[]     $deps         Dependencies.
 * @param string|false $version      Version string.
 * @param bool         $in_footer    Load in footer.
 * @return bool
 */
function hvn_realty_enqueue_theme_script( $handle, $relative_path, $deps = array(), $version = false, $in_footer = true, $required = false ) {
	if ( function_exists( 'hvn_realty_enqueue_script_safe' ) ) {
		return hvn_realty_enqueue_script_safe( $handle, $relative_path, $deps, $version, $in_footer, $required );
	}

	$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );

	if ( ! file_exists( $file ) ) {
		return false;
	}

	wp_enqueue_script(
		$handle,
		get_template_directory_uri() . '/' . ltrim( $relative_path, '/' ),
		$deps,
		$version ? $version : ( defined( 'HVN_REALTY_VERSION' ) ? HVN_REALTY_VERSION : false ),
		$in_footer
	);

	return true;
}

/**
 * Nav menu walker instance when the theme walker class is available.
 *
 * @return HVN_Realty_Walker_Nav_Menu|null
 */
function hvn_realty_get_nav_menu_walker() {
	if ( ! class_exists( 'HVN_Realty_Walker_Nav_Menu' ) ) {
		return null;
	}

	return new HVN_Realty_Walker_Nav_Menu();
}

/**
 * Optional Customizer control file paths (missing = warning only).
 *
 * @return string[] Theme-relative paths.
 */
function hvn_realty_get_optional_customizer_control_paths() {
	return array(
		'inc/customizer/class-hvn-realty-testimonials-control.php',
		'inc/customizer/class-hvn-realty-section-order-control.php',
		'inc/customizer/class-hvn-realty-why-control.php',
	);
}

/**
 * Register admin warning when optional Customizer control files are absent.
 *
 * @return void
 */
function hvn_realty_register_optional_customizer_controls_notice() {
	$missing = array();

	foreach ( hvn_realty_get_optional_customizer_control_paths() as $relative_path ) {
		$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );
		if ( ! file_exists( $file ) ) {
			$missing[] = $relative_path;
		}
	}

	if ( empty( $missing ) ) {
		return;
	}

	$GLOBALS['hvn_realty_missing_optional_customizer_controls'] = $missing;

	if ( ! has_action( 'admin_notices', 'hvn_realty_optional_customizer_controls_notice' ) ) {
		add_action( 'admin_notices', 'hvn_realty_optional_customizer_controls_notice' );
	}
}

/**
 * Admin notice when optional Customizer controls are unavailable.
 *
 * @return void
 */
function hvn_realty_optional_customizer_controls_notice() {
	if ( ! current_user_can( 'customize' ) && ! current_user_can( 'switch_themes' ) ) {
		return;
	}

	$missing = isset( $GLOBALS['hvn_realty_missing_optional_customizer_controls'] )
		? (array) $GLOBALS['hvn_realty_missing_optional_customizer_controls']
		: array();

	if ( empty( $missing ) ) {
		return;
	}

	echo '<div class="notice notice-warning is-dismissible"><p>';
	echo esc_html__( 'Optional Customizer controls unavailable. The testimonials drag-and-drop control is disabled until the complete theme package is installed.', 'havenlytics-realty' );
	echo ' <code>' . esc_html( implode( '</code>, <code>', $missing ) ) . '</code>';
	echo '</p></div>';
}

/**
 * Admin notice for missing required theme files.
 *
 * @return void
 */
function hvn_realty_missing_theme_files_notice() {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}

	$missing = isset( $GLOBALS['hvn_realty_missing_theme_files'] ) ? (array) $GLOBALS['hvn_realty_missing_theme_files'] : array();

	if ( empty( $missing ) ) {
		return;
	}

	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'Havenlytics Realty is missing required theme files. Some features may not work until you reinstall the complete theme package:', 'havenlytics-realty' );
	echo ' <code>' . esc_html( implode( '</code>, <code>', $missing ) ) . '</code>';
	echo '</p></div>';
}

/**
 * Admin notice when Havenlytics integration bootstrap is absent.
 *
 * @return void
 */
function hvn_realty_missing_integration_notice() {
	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}

	echo '<div class="notice notice-warning is-dismissible"><p>';
	echo esc_html__( 'Havenlytics Realty integration files are missing (inc/integrations/havenlytics/). The theme will run in blog mode; real estate homepage features are disabled until the complete theme is installed.', 'havenlytics-realty' );
	echo '</p></div>';
}
