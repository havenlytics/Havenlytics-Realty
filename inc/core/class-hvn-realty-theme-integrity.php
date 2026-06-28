<?php
/**
 * Theme package integrity scanner.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'HVN_REALTY_MANIFEST_CLI' ) ) {
	exit;
}

/**
 * Validates theme files, classes, and runtime load state.
 */
class HVN_Realty_Theme_Integrity {

	/**
	 * Required PHP classes after bootstrap.
	 *
	 * @return array<string, string> Class name => source file (informational).
	 */
	public static function get_required_classes() {
		return array(
			'HVN_Realty_Walker_Nav_Menu'     => 'inc/class-hvn-realty-walker-nav-menu.php',
			'HVN_Realty_Migrations'          => 'inc/core/class-hvn-realty-migrations.php',
			'HVN_Realty_Upgrade_Manager'     => 'inc/core/class-hvn-realty-upgrade-manager.php',
		);
	}

	/**
	 * Optional Customizer control classes (warning only when absent).
	 *
	 * @return array<string, string> Class name => source file.
	 */
	public static function get_optional_customizer_classes() {
		return array(
			'HVN_Realty_Customize_Testimonials_Control'   => 'inc/customizer/class-hvn-realty-testimonials-control.php',
			'HVN_Realty_Customize_Section_Order_Control' => 'inc/customizer/class-hvn-realty-section-order-control.php',
			'HVN_Realty_Customize_Why_Control'           => 'inc/customizer/class-hvn-realty-why-control.php',
		);
	}

	/**
	 * Theme-relative paths treated as optional Customizer assets.
	 *
	 * @return string[]
	 */
	public static function get_optional_customizer_paths() {
		if ( function_exists( 'hvn_realty_get_optional_customizer_control_paths' ) ) {
			return hvn_realty_get_optional_customizer_control_paths();
		}

		return array(
			'inc/customizer/class-hvn-realty-testimonials-control.php',
			'inc/customizer/class-hvn-realty-section-order-control.php',
			'inc/customizer/class-hvn-realty-why-control.php',
		);
	}

	/**
	 * Whether a manifest path is an optional Customizer control file.
	 *
	 * @param string $relative_path Theme-relative path.
	 * @return bool
	 */
	public static function is_optional_customizer_path( $relative_path ) {
		return in_array( ltrim( $relative_path, '/' ), self::get_optional_customizer_paths(), true );
	}

	/**
	 * Scan for missing optional Customizer control files on disk.
	 *
	 * @return array<string, string> Class => expected file.
	 */
	public static function scan_missing_optional_customizer_classes() {
		$missing = array();
		$root    = function_exists( 'get_template_directory' ) ? get_template_directory() : '';

		foreach ( self::get_optional_customizer_classes() as $class => $file ) {
			$absolute = $root . '/' . ltrim( $file, '/' );
			if ( ! file_exists( $absolute ) ) {
				$missing[ $class ] = $file;
			}
		}

		return $missing;
	}

	/**
	 * Scan manifest for missing files on disk.
	 *
	 * @return array<string, string> Relative path => category.
	 */
	public static function scan_missing_files() {
		if ( ! function_exists( 'hvn_realty_get_release_manifest' ) ) {
			return array();
		}

		$missing = array();
		$root    = function_exists( 'get_template_directory' ) ? get_template_directory() : '';

		foreach ( hvn_realty_get_release_manifest() as $relative_path => $category ) {
			$file = $root . '/' . ltrim( $relative_path, '/' );
			if ( ! file_exists( $file ) ) {
				$missing[ $relative_path ] = $category;
			}
		}

		return $missing;
	}

	/**
	 * Files recorded as missing during runtime loading.
	 *
	 * @return string[]
	 */
	public static function get_runtime_missing_files() {
		$missing = isset( $GLOBALS['hvn_realty_missing_theme_files'] )
			? (array) $GLOBALS['hvn_realty_missing_theme_files']
			: array();

		return array_values( array_unique( array_filter( array_map( 'strval', $missing ) ) ) );
	}

	/**
	 * Combined missing file list (manifest + runtime).
	 *
	 * @return array<string, string>
	 */
	public static function get_all_missing_files() {
		$missing = self::scan_missing_files();

		foreach ( self::get_runtime_missing_files() as $path ) {
			if ( ! isset( $missing[ $path ] ) ) {
				$missing[ $path ] = 'runtime';
			}
		}

		return $missing;
	}

	/**
	 * Scan for missing required classes.
	 *
	 * @return array<string, string> Class => expected file.
	 */
	public static function scan_missing_classes() {
		$missing = array();

		foreach ( self::get_required_classes() as $class => $file ) {
			if ( ! class_exists( $class, false ) ) {
				$missing[ $class ] = $file;
			}
		}

		return $missing;
	}

	/**
	 * Whether a missing category is optional.
	 *
	 * @param string $category Manifest category.
	 * @return bool
	 */
	public static function is_optional_category( $category ) {
		if ( ! function_exists( 'hvn_realty_get_optional_release_categories' ) ) {
			return false;
		}

		return in_array( $category, hvn_realty_get_optional_release_categories(), true );
	}

	/**
	 * Split missing files into critical vs optional.
	 *
	 * @param array<string, string> $missing Missing files.
	 * @return array{critical: array<string, string>, optional: array<string, string>}
	 */
	public static function partition_missing_files( $missing ) {
		$critical = array();
		$optional = array();

		foreach ( $missing as $path => $category ) {
			if ( self::is_optional_customizer_path( $path ) ) {
				$optional[ $path ] = $category;
			} elseif ( self::is_optional_category( $category ) ) {
				$optional[ $path ] = $category;
			} else {
				$critical[ $path ] = $category;
			}
		}

		return array(
			'critical' => $critical,
			'optional' => $optional,
		);
	}

	/**
 * Full integrity report for System Status and admin notices.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_report() {
		$missing_all = self::get_all_missing_files();
		$partition   = self::partition_missing_files( $missing_all );
		$classes     = self::scan_missing_classes();
		$optional_cx = self::scan_missing_optional_customizer_classes();

		$status = 'success';
		if ( ! empty( $partition['critical'] ) || ! empty( $classes ) ) {
			$status = 'error';
		} elseif ( ! empty( $partition['optional'] ) || ! empty( $optional_cx ) ) {
			$status = 'warning';
		}

		return array(
			'status'                          => $status,
			'missing_files'                   => $missing_all,
			'missing_critical'                => $partition['critical'],
			'missing_optional'                => $partition['optional'],
			'missing_classes'                 => $classes,
			'missing_optional_customizer'     => $optional_cx,
			'runtime_missing'  => self::get_runtime_missing_files(),
			'total_manifest'   => function_exists( 'hvn_realty_get_release_manifest' ) ? count( hvn_realty_get_release_manifest() ) : 0,
			'present_count'    => function_exists( 'hvn_realty_get_release_manifest' )
				? count( hvn_realty_get_release_manifest() ) - count( $missing_all )
				: 0,
		);
	}

	/**
	 * Summary row for System Status checks table.
	 *
	 * @return array{key: string, label: string, value: string, status: string, detail: string}
	 */
	public static function get_status_check_row() {
		$report = self::get_report();

		if ( 'success' === $report['status'] ) {
			return array(
				'key'    => 'theme_integrity',
				'label'  => __( 'Theme integrity', 'havenlytics-realty' ),
				'value'  => __( 'All files found', 'havenlytics-realty' ),
				'status' => 'success',
				'detail' => '',
			);
		}

		$count = count( $report['missing_critical'] ) + count( $report['missing_classes'] );
		if ( 0 === $count && ( ! empty( $report['missing_optional'] ) || ! empty( $report['missing_optional_customizer'] ) ) ) {
			return array(
				'key'    => 'theme_integrity',
				'label'  => __( 'Theme integrity', 'havenlytics-realty' ),
				'value'  => __( 'Optional controls missing', 'havenlytics-realty' ),
				'status' => 'warning',
				'detail' => __( 'See Theme Integrity panel below', 'havenlytics-realty' ),
			);
		}

		return array(
			'key'    => 'theme_integrity',
			'label'  => __( 'Theme integrity', 'havenlytics-realty' ),
			'value'  => sprintf(
				/* translators: %d: number of missing items */
				_n( '%d issue', '%d issues', $count, 'havenlytics-realty' ),
				$count
			),
			'status' => $report['status'],
			'detail' => __( 'See Theme Integrity panel below', 'havenlytics-realty' ),
		);
	}

	/**
	 * Register admin notice when integrity fails.
	 *
	 * @return void
	 */
	public static function boot() {
		add_action( 'admin_notices', array( __CLASS__, 'render_admin_notice' ) );
	}

	/**
	 * Admin notice for incomplete theme packages.
	 *
	 * @return void
	 */
	public static function render_admin_notice() {
		if ( ! current_user_can( 'switch_themes' ) ) {
			return;
		}

		$report = self::get_report();

		if ( empty( $report['missing_critical'] ) && empty( $report['missing_classes'] ) ) {
			return;
		}

		if ( ! empty( $GLOBALS['hvn_realty_missing_theme_files'] ) ) {
			return;
		}

		$paths = array_keys( $report['missing_critical'] );
		if ( empty( $paths ) && ! empty( $report['missing_classes'] ) ) {
			$paths = array_values( $report['missing_classes'] );
		}

		if ( empty( $paths ) ) {
			return;
		}

		$url = function_exists( 'hvn_realty_get_system_status_url' ) ? hvn_realty_get_system_status_url() : '';

		echo '<div class="notice notice-error"><p>';
		echo esc_html__( 'Havenlytics Realty is missing required theme files. Reinstall the complete theme package.', 'havenlytics-realty' );
		echo ' <code>' . esc_html( implode( '</code>, <code>', array_slice( $paths, 0, 8 ) ) ) . '</code>';
		if ( count( $paths ) > 8 ) {
			echo ' …';
		}
		if ( $url ) {
			echo ' <a href="' . esc_url( $url ) . '">' . esc_html__( 'View System Status', 'havenlytics-realty' ) . '</a>';
		}
		echo '</p></div>';
	}
}

if ( class_exists( 'HVN_Realty_Theme_Integrity' ) ) {
	HVN_Realty_Theme_Integrity::boot();
}
