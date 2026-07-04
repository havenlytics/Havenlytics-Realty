<?php
/**
 * Centralized theme asset loader with runtime fallback and diagnostics.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves, enqueues, and recovers theme CSS/JS/image assets at runtime.
 */
class HVN_Realty_Asset_Loader {

	/**
	 * Global diagnostics key.
	 */
	const DIAGNOSTICS_GLOBAL = 'hvn_realty_asset_diagnostics';

	/**
	 * Tracks enqueued handles to prevent duplicates.
	 *
	 * @var array<string, bool>
	 */
	private static $enqueued_handles = array();

	/**
	 * Normalize a theme-relative path.
	 *
	 * @param string $relative_path Theme-relative path.
	 * @return string
	 */
	public static function normalize_path( $relative_path ) {
		return ltrim( str_replace( '\\', '/', (string) $relative_path ), '/' );
	}

	/**
	 * Absolute filesystem path for a theme-relative asset.
	 *
	 * @param string $relative_path Theme-relative path.
	 * @return string
	 */
	public static function get_absolute_path( $relative_path ) {
		$relative_path = self::normalize_path( $relative_path );

		if ( '' === $relative_path ) {
			return '';
		}

		return get_template_directory() . '/' . $relative_path;
	}

	/**
	 * Public URI for a theme-relative asset.
	 *
	 * @param string $relative_path Theme-relative path.
	 * @return string
	 */
	public static function get_uri( $relative_path ) {
		$relative_path = self::normalize_path( $relative_path );

		if ( '' === $relative_path ) {
			return '';
		}

		return get_template_directory_uri() . '/' . $relative_path;
	}

	/**
	 * Whether a theme asset exists and is readable.
	 *
	 * @param string $relative_path Theme-relative path.
	 * @return bool
	 */
	public static function asset_exists( $relative_path ) {
		$file = self::get_absolute_path( $relative_path );

		return '' !== $file && is_file( $file ) && is_readable( $file );
	}

	/**
	 * Resolve the first available path from a candidate list.
	 *
	 * @param string   $requested_path Requested theme-relative path.
	 * @param string[] $candidates     Fallback paths in priority order.
	 * @return array{path: string, requested: string, fallback: string, recovered: bool}
	 */
	public static function resolve_path( $requested_path, $candidates = array() ) {
		$requested_path = self::normalize_path( $requested_path );

		if ( self::asset_exists( $requested_path ) ) {
			return array(
				'path'      => $requested_path,
				'requested' => $requested_path,
				'fallback'  => '',
				'recovered' => false,
			);
		}

		foreach ( $candidates as $candidate ) {
			$candidate = self::normalize_path( $candidate );
			if ( self::asset_exists( $candidate ) ) {
				return array(
					'path'      => $candidate,
					'requested' => $requested_path,
					'fallback'  => $candidate,
					'recovered' => true,
				);
			}
		}

		return array(
			'path'      => '',
			'requested' => $requested_path,
			'fallback'  => '',
			'recovered' => false,
		);
	}

	/**
	 * CSS fallback chain for a requested stylesheet.
	 *
	 * @param string $relative_path Theme-relative CSS path.
	 * @return string[]
	 */
	public static function get_css_fallback_chain( $relative_path ) {
		$relative_path = self::normalize_path( $relative_path );

		$explicit = array(
			'style.css'                                => array(),
			'assets/css/base/forms.css'                => array( 'assets/css/theme.css' ),
			'assets/css/polish.css'                    => array( 'assets/css/theme.css' ),
			'assets/css/layouts.css'                   => array( 'assets/css/polish.css', 'assets/css/theme.css' ),
			'assets/css/page.css'                      => array( 'assets/css/layouts.css', 'assets/css/theme.css' ),
			'assets/css/single.css'                    => array( 'assets/css/layouts.css', 'assets/css/theme.css' ),
			'assets/css/home.css'                      => array( 'assets/css/theme.css' ),
			'assets/css/home/mobile-search-drawer.css' => array( 'assets/css/home.css', 'assets/css/theme.css' ),
			'assets/css/havenlytics-compat.css'        => array( 'assets/css/theme.css' ),
			'assets/css/customizer-controls.css'     => array( 'assets/css/theme.css' ),
			'assets/css/admin-realty.css'              => array( 'assets/css/theme.css' ),
			'assets/css/editor.css'                    => array( 'assets/css/theme.css' ),
			'assets/css/blog.css'                      => array( 'assets/css/theme.css' ),
			'assets/css/blog/blog-base.css'            => array( 'assets/css/blog.css', 'assets/css/theme.css' ),
			'assets/css/blog/blog-grid.css'            => array( 'assets/css/blog/blog-base.css', 'assets/css/blog.css' ),
			'assets/css/blog/blog-list.css'            => array( 'assets/css/blog/blog-base.css', 'assets/css/blog.css' ),
			'assets/css/blog/blog-sidebar.css'         => array( 'assets/css/blog/blog-base.css', 'assets/css/blog.css' ),
			'assets/css/blog/blog-pagination.css'      => array( 'assets/css/blog/blog-base.css', 'assets/css/blog.css' ),
			'assets/css/blog/blog-single.css'          => array( 'assets/css/blog/blog-base.css', 'assets/css/blog.css' ),
			'assets/css/blog/blog-404.css'             => array( 'assets/css/blog/blog-base.css', 'assets/css/theme.css' ),
		);

		if ( isset( $explicit[ $relative_path ] ) ) {
			return $explicit[ $relative_path ];
		}

		if ( 0 === strpos( $relative_path, 'assets/css/blog/' ) ) {
			return array( 'assets/css/blog.css', 'assets/css/theme.css' );
		}

		if ( 'assets/css/theme.css' === $relative_path || 'style.css' === $relative_path ) {
			return array();
		}

		if ( 0 === strpos( $relative_path, 'assets/css/' ) ) {
			return array( 'assets/css/theme.css' );
		}

		return array();
	}

	/**
	 * JS fallback chain for a requested script.
	 *
	 * @param string $relative_path Theme-relative JS path.
	 * @return string[]
	 */
	public static function get_js_fallback_chain( $relative_path ) {
		$relative_path = self::normalize_path( $relative_path );

		$explicit = array(
			'assets/js/home/mobile-search-drawer.js' => array( 'assets/js/home.js', 'assets/js/theme.js' ),
			'assets/js/home.js'                      => array( 'assets/js/theme.js' ),
			'assets/js/customizer-controls.js'       => array( 'assets/js/customizer-controls-framework.js' ),
			'assets/js/customizer.js'                => array(),
		);

		if ( isset( $explicit[ $relative_path ] ) ) {
			return $explicit[ $relative_path ];
		}

		if ( 0 === strpos( $relative_path, 'assets/js/customizer-' ) ) {
			return array( 'assets/js/customizer-controls-framework.js', 'assets/js/theme.js' );
		}

		if ( 'assets/js/theme.js' === $relative_path ) {
			return array();
		}

		if ( 0 === strpos( $relative_path, 'assets/js/' ) ) {
			return array( 'assets/js/theme.js' );
		}

		return array();
	}

	/**
	 * Image/SVG fallback chain.
	 *
	 * @param string $relative_path Theme-relative image path.
	 * @return string[]
	 */
	public static function get_image_fallback_chain( $relative_path ) {
		$relative_path = self::normalize_path( $relative_path );

		$explicit = array(
			'assets/admin/img/realty-icon.svg'         => array( 'assets/admin/img/realty-icon.png', 'assets/admin/img/havenlytics-realty.png' ),
			'assets/admin/img/realty-icon.png'         => array( 'assets/admin/img/havenlytics-realty.png' ),
			'assets/admin/img/havenlytics-favicon.png' => array( 'assets/admin/img/havenlytics-realty.png' ),
			'assets/images/default-header.jpg'         => array(),
		);

		if ( isset( $explicit[ $relative_path ] ) ) {
			return $explicit[ $relative_path ];
		}

		if ( preg_match( '/\.svg$/i', $relative_path ) ) {
			$png = preg_replace( '/\.svg$/i', '.png', $relative_path );
			if ( $png !== $relative_path ) {
				return array( $png );
			}
		}

		return array();
	}

	/**
	 * Record a diagnostic event.
	 *
	 * @param array<string, mixed> $entry Event data.
	 * @return void
	 */
	public static function log_event( array $entry ) {
		if ( ! isset( $GLOBALS[ self::DIAGNOSTICS_GLOBAL ] ) || ! is_array( $GLOBALS[ self::DIAGNOSTICS_GLOBAL ] ) ) {
			$GLOBALS[ self::DIAGNOSTICS_GLOBAL ] = array();
		}

		$defaults = array(
			'timestamp'      => time(),
			'type'           => 'asset',
			'handle'         => '',
			'requested_path' => '',
			'resolved_path'  => '',
			'fallback_path'  => '',
			'status'         => 'skipped',
			'reason'         => '',
		);

		$GLOBALS[ self::DIAGNOSTICS_GLOBAL ][] = array_merge( $defaults, $entry );
	}

	/**
	 * All recorded asset diagnostics.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_diagnostics() {
		if ( ! isset( $GLOBALS[ self::DIAGNOSTICS_GLOBAL ] ) || ! is_array( $GLOBALS[ self::DIAGNOSTICS_GLOBAL ] ) ) {
			return array();
		}

		return $GLOBALS[ self::DIAGNOSTICS_GLOBAL ];
	}

	/**
	 * Register a missing asset for integrity reporting.
	 *
	 * @param string $relative_path Theme-relative path.
	 * @return void
	 */
	private static function register_missing_asset( $relative_path ) {
		if ( function_exists( 'hvn_realty_record_missing_theme_file' ) ) {
			hvn_realty_record_missing_theme_file( $relative_path, true );
		}
	}

	/**
	 * Resolve theme version string.
	 *
	 * @param string|false $version Optional version override.
	 * @return string|false
	 */
	private static function resolve_version( $version ) {
		if ( $version ) {
			return $version;
		}

		return defined( 'HVN_REALTY_VERSION' ) ? HVN_REALTY_VERSION : false;
	}

	/**
	 * Enqueue a theme stylesheet with fallback recovery.
	 *
	 * @param string       $handle        Style handle.
	 * @param string       $relative_path Theme-relative path.
	 * @param string[]     $deps          Dependencies.
	 * @param string|false $version       Version.
	 * @param string       $media         Media attribute.
	 * @param bool         $required      Whether absence should be logged as required.
	 * @return bool True when a stylesheet was enqueued.
	 */
	public static function enqueue_style( $handle, $relative_path, $deps = array(), $version = false, $media = 'all', $required = false ) {
		$handle        = (string) $handle;
		$relative_path = self::normalize_path( $relative_path );

		if ( '' === $handle || '' === $relative_path ) {
			return false;
		}

		if ( isset( self::$enqueued_handles[ 'style:' . $handle ] ) ) {
			return true;
		}

		$resolved = self::resolve_path( $relative_path, self::get_css_fallback_chain( $relative_path ) );

		if ( '' === $resolved['path'] ) {
			self::register_missing_asset( $relative_path );
			self::log_event(
				array(
					'type'           => 'css',
					'handle'         => $handle,
					'requested_path' => $relative_path,
					'status'         => 'skipped',
					'reason'         => $required ? 'required_css_missing' : 'css_missing',
				)
			);

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( '[Havenlytics Realty] Missing CSS asset (skipped): ' . $relative_path ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}

			return false;
		}

		if ( $resolved['recovered'] ) {
			self::register_missing_asset( $relative_path );
			self::log_event(
				array(
					'type'           => 'css',
					'handle'         => $handle,
					'requested_path' => $relative_path,
					'resolved_path'  => $resolved['path'],
					'fallback_path'  => $resolved['fallback'],
					'status'         => 'recovered',
					'reason'         => 'css_fallback_used',
				)
			);
		} else {
			self::log_event(
				array(
					'type'           => 'css',
					'handle'         => $handle,
					'requested_path' => $relative_path,
					'resolved_path'  => $resolved['path'],
					'status'         => 'loaded',
					'reason'         => 'css_present',
				)
			);
		}

		wp_enqueue_style(
			$handle,
			self::get_uri( $resolved['path'] ),
			(array) $deps,
			self::resolve_version( $version ),
			$media
		);

		self::$enqueued_handles[ 'style:' . $handle ] = true;

		return true;
	}

	/**
	 * Enqueue a theme script with fallback recovery.
	 *
	 * @param string       $handle        Script handle.
	 * @param string       $relative_path Theme-relative path.
	 * @param string[]     $deps          Dependencies.
	 * @param string|false $version       Version.
	 * @param bool         $in_footer     Load in footer.
	 * @param bool         $required      Whether absence should be logged as required.
	 * @return bool True when a script was enqueued.
	 */
	public static function enqueue_script( $handle, $relative_path, $deps = array(), $version = false, $in_footer = true, $required = false ) {
		$handle        = (string) $handle;
		$relative_path = self::normalize_path( $relative_path );

		if ( '' === $handle || '' === $relative_path ) {
			return false;
		}

		if ( isset( self::$enqueued_handles[ 'script:' . $handle ] ) ) {
			return true;
		}

		$resolved = self::resolve_path( $relative_path, self::get_js_fallback_chain( $relative_path ) );

		if ( '' === $resolved['path'] ) {
			if ( $required ) {
				self::register_missing_asset( $relative_path );
			}

			self::log_event(
				array(
					'type'           => 'js',
					'handle'         => $handle,
					'requested_path' => $relative_path,
					'status'         => 'skipped',
					'reason'         => $required ? 'required_js_missing' : 'optional_js_missing',
				)
			);

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && $required ) {
				error_log( '[Havenlytics Realty] Missing JS asset (continuing): ' . $relative_path ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}

			return false;
		}

		if ( $resolved['recovered'] ) {
			self::register_missing_asset( $relative_path );
			self::log_event(
				array(
					'type'           => 'js',
					'handle'         => $handle,
					'requested_path' => $relative_path,
					'resolved_path'  => $resolved['path'],
					'fallback_path'  => $resolved['fallback'],
					'status'         => 'recovered',
					'reason'         => 'js_fallback_used',
				)
			);
		} else {
			self::log_event(
				array(
					'type'           => 'js',
					'handle'         => $handle,
					'requested_path' => $relative_path,
					'resolved_path'  => $resolved['path'],
					'status'         => 'loaded',
					'reason'         => 'js_present',
				)
			);
		}

		wp_enqueue_script(
			$handle,
			self::get_uri( $resolved['path'] ),
			(array) $deps,
			self::resolve_version( $version ),
			$in_footer
		);

		self::$enqueued_handles[ 'script:' . $handle ] = true;

		return true;
	}

	/**
	 * Resolve a theme image/SVG URI with optional fallbacks.
	 *
	 * @param string        $relative_path  Requested path.
	 * @param string[]|null $fallback_paths Optional explicit fallbacks.
	 * @param string        $placeholder    Placeholder when nothing resolves.
	 * @return string
	 */
	public static function get_image_uri( $relative_path, $fallback_paths = null, $placeholder = '' ) {
		$relative_path = self::normalize_path( $relative_path );
		$candidates    = is_array( $fallback_paths ) ? $fallback_paths : self::get_image_fallback_chain( $relative_path );
		$resolved      = self::resolve_path( $relative_path, $candidates );

		if ( '' === $resolved['path'] ) {
			self::register_missing_asset( $relative_path );
			self::log_event(
				array(
					'type'           => 'image',
					'requested_path' => $relative_path,
					'status'         => '' !== $placeholder ? 'recovered' : 'skipped',
					'fallback_path'  => $placeholder,
					'reason'         => 'image_missing',
				)
			);

			return $placeholder;
		}

		if ( $resolved['recovered'] ) {
			self::register_missing_asset( $relative_path );
			self::log_event(
				array(
					'type'           => 'image',
					'requested_path' => $relative_path,
					'resolved_path'  => $resolved['path'],
					'fallback_path'  => $resolved['fallback'],
					'status'         => 'recovered',
					'reason'         => 'image_fallback_used',
				)
			);
		}

		return self::get_uri( $resolved['path'] );
	}

	/**
	 * Log a missing template part (no output).
	 *
	 * @param string      $slug Template slug.
	 * @param string|null $name Template name.
	 * @return void
	 */
	public static function log_missing_template_part( $slug, $name = null ) {
		$slug = self::normalize_path( (string) $slug );
		$path = $slug;

		if ( null !== $name && '' !== $name ) {
			$path .= '-' . sanitize_file_name( (string) $name );
		}

		$path .= '.php';

		self::register_missing_asset( 'template-parts/' . $path );
		self::log_event(
			array(
				'type'           => 'template',
				'requested_path' => 'template-parts/' . $path,
				'status'         => 'skipped',
				'reason'         => 'template_part_missing',
			)
		);
	}
}

/**
 * Enqueue a theme stylesheet through the centralized asset loader.
 *
 * @param string       $handle        Style handle.
 * @param string       $relative_path Theme-relative path.
 * @param string[]     $deps          Dependencies.
 * @param string|false $version       Version.
 * @param string       $media         Media attribute.
 * @param bool         $required      Whether absence is treated as required.
 * @return bool
 */
function hvn_realty_enqueue_style_safe( $handle, $relative_path, $deps = array(), $version = false, $media = 'all', $required = false ) {
	if ( class_exists( 'HVN_Realty_Asset_Loader', false ) ) {
		return HVN_Realty_Asset_Loader::enqueue_style( $handle, $relative_path, $deps, $version, $media, $required );
	}

	return false;
}

/**
 * Enqueue a theme script through the centralized asset loader.
 *
 * @param string       $handle        Script handle.
 * @param string       $relative_path Theme-relative path.
 * @param string[]     $deps          Dependencies.
 * @param string|false $version       Version.
 * @param bool         $in_footer     Load in footer.
 * @param bool         $required      Whether absence is treated as required.
 * @return bool
 */
function hvn_realty_enqueue_script_safe( $handle, $relative_path, $deps = array(), $version = false, $in_footer = true, $required = false ) {
	if ( class_exists( 'HVN_Realty_Asset_Loader', false ) ) {
		return HVN_Realty_Asset_Loader::enqueue_script( $handle, $relative_path, $deps, $version, $in_footer, $required );
	}

	return false;
}

/**
 * Resolve a theme image/SVG URI with graceful fallback.
 *
 * @param string        $relative_path  Requested path.
 * @param string[]|null $fallback_paths Optional fallbacks.
 * @param string        $placeholder    Placeholder URI when unresolved.
 * @return string
 */
function hvn_realty_get_theme_asset_uri( $relative_path, $fallback_paths = null, $placeholder = '' ) {
	if ( class_exists( 'HVN_Realty_Asset_Loader', false ) ) {
		return HVN_Realty_Asset_Loader::get_image_uri( $relative_path, $fallback_paths, $placeholder );
	}

	$file = get_template_directory() . '/' . ltrim( (string) $relative_path, '/' );
	if ( is_file( $file ) && is_readable( $file ) ) {
		return get_template_directory_uri() . '/' . ltrim( (string) $relative_path, '/' );
	}

	return $placeholder;
}
