<?php
/**
 * Registered Havenlytics Realty theme migrations.
 *
 * Each method must be idempotent and theme-settings only.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme migration callbacks.
 */
class HVN_Realty_Migrations {

	/**
	 * Copy Property Locations Customizer values into Property Taxonomies keys.
	 *
	 * Preserves legacy theme_mod keys for read-time fallback compatibility.
	 * Only writes when the new key is unset and a legacy value exists.
	 *
	 * @return bool True on success.
	 */
	public static function migrate_1160_locations_to_taxonomies() {
		$map = array(
			'hvn_realty_home_taxonomies_source'      => array( 'hvn_realty_home_locations_source' ),
			'hvn_realty_home_taxonomies_title'         => array( 'hvn_realty_home_locations_title' ),
			'hvn_realty_home_taxonomies_subtitle'    => array( 'hvn_realty_home_locations_subtitle' ),
			'hvn_realty_home_taxonomies_count'       => array( 'hvn_realty_home_locations_count' ),
			'hvn_realty_home_show_property_taxonomies' => array(
				'hvn_realty_home_show_property_locations',
				'hvn_realty_home_show_property_categories',
			),
		);

		foreach ( $map as $new_key => $legacy_keys ) {
			if ( self::theme_mod_has_value( $new_key ) ) {
				continue;
			}

			foreach ( (array) $legacy_keys as $legacy_key ) {
				if ( ! self::theme_mod_has_value( $legacy_key ) ) {
					continue;
				}

				set_theme_mod( $new_key, get_theme_mod( $legacy_key ) );
				break;
			}
		}

		// Ensure existing Location users keep Locations as the active source when unset.
		if ( ! self::theme_mod_has_value( 'hvn_realty_home_taxonomies_source' ) ) {
			$has_legacy_locations = self::theme_mod_has_value( 'hvn_realty_home_locations_source' )
				|| self::theme_mod_has_value( 'hvn_realty_home_locations_title' )
				|| self::theme_mod_has_value( 'hvn_realty_home_locations_count' );

			if ( $has_legacy_locations ) {
				set_theme_mod( 'hvn_realty_home_taxonomies_source', 'locations' );
			}
		}

		return true;
	}

	/**
	 * Baseline starter-site options for sites upgrading to 1.22.0.
	 *
	 * Links legacy launch menus to Modern demo options without deleting data.
	 *
	 * @return bool
	 */
	public static function migrate_1220_starter_sites_baseline() {
		if ( ! defined( 'HVN_REALTY_ACTIVE_DEMO_OPTION' ) ) {
			return true;
		}

		$active_demo = (string) get_option( HVN_REALTY_ACTIVE_DEMO_OPTION, '' );

		if ( ! function_exists( 'hvn_realty_is_valid_demo_id' ) || ! hvn_realty_is_valid_demo_id( $active_demo ) ) {
			$should_default = false;

			if ( defined( 'HVN_REALTY_LAUNCH_COMPLETE_OPTION' ) && get_option( HVN_REALTY_LAUNCH_COMPLETE_OPTION, false ) ) {
				$should_default = true;
			} elseif ( defined( 'HVN_REALTY_HOME_PAGE_OPTION' ) && (int) get_option( HVN_REALTY_HOME_PAGE_OPTION, 0 ) > 0 ) {
				$should_default = true;
			}

			if ( $should_default ) {
				update_option( HVN_REALTY_ACTIVE_DEMO_OPTION, 'modern', false );
			}
		}

		if ( function_exists( 'hvn_realty_get_demo_primary_menu_option' ) && defined( 'HVN_REALTY_PRIMARY_MENU_OPTION' ) ) {
			$modern_option = hvn_realty_get_demo_primary_menu_option( 'modern' );

			if ( ! get_option( $modern_option, 0 ) ) {
				$legacy_menu = (int) get_option( HVN_REALTY_PRIMARY_MENU_OPTION, 0 );
				if ( $legacy_menu > 0 && is_nav_menu( $legacy_menu ) ) {
					update_option( $modern_option, $legacy_menu, false );
				}
			}
		}

		if ( function_exists( 'hvn_realty_get_demo_footer_menus_option' ) && defined( 'HVN_REALTY_FOOTER_MENUS_OPTION' ) ) {
			$modern_footer_option = hvn_realty_get_demo_footer_menus_option( 'modern' );
			$stored               = get_option( $modern_footer_option, array() );

			if ( ! is_array( $stored ) || empty( $stored['properties'] ) ) {
				$legacy_footer = get_option( HVN_REALTY_FOOTER_MENUS_OPTION, array() );
				if ( is_array( $legacy_footer ) && ! empty( $legacy_footer['properties'] ) && is_nav_menu( (int) $legacy_footer['properties'] ) ) {
					update_option( $modern_footer_option, $legacy_footer, false );
				}
			}
		}

		return true;
	}

	/**
	 * Recovery release — starter options are ignored; no database writes.
	 *
	 * @return bool
	 */
	public static function migrate_1230_active_starter_option() {
		return true;
	}

	/**
	 * Normalize Homepage 2.0 section order and visibility after upgrading
	 * from pre-2.0 section slugs. Preserves legacy theme_mod keys and only
	 * writes modern keys when unset.
	 *
	 * @return bool True on success.
	 */
	public static function migrate_2050_homepage_sections() {
		if ( ! self::should_run_homepage_section_migration() ) {
			return true;
		}

		self::normalize_homepage_section_order();
		self::normalize_homepage_section_visibility();

		return true;
	}

	/**
	 * One-time recovery for homepage visibility keys wrongly written by the
	 * pre-fix 2.0.5 migration (legacy defaults treated as user choices).
	 *
	 * @return bool True on success.
	 */
	public static function migrate_2060_visibility_recovery() {
		if ( ! self::should_run_visibility_recovery() ) {
			return true;
		}

		$registry   = function_exists( 'hvn_realty_get_default_home_section_order' )
			? hvn_realty_get_default_home_section_order()
			: array( 'hero', 'search', 'why', 'properties', 'types', 'locations', 'agents', 'testimonials', 'blog', 'cta' );
		$legacy_map = self::get_modern_to_legacy_visibility_map();

		foreach ( $registry as $section ) {
			$mod_key = function_exists( 'hvn_realty_get_home_section_visibility_mod' )
				? hvn_realty_get_home_section_visibility_mod( $section )
				: 'hvn_realty_home_show_' . sanitize_key( $section );

			if ( ! self::theme_mod_has_value( $mod_key ) ) {
				continue;
			}

			$legacy_slugs = $legacy_map[ $section ] ?? array();
			if ( empty( $legacy_slugs ) ) {
				continue;
			}

			if ( null !== self::derive_modern_section_visibility( $legacy_slugs ) ) {
				continue;
			}

			remove_theme_mod( $mod_key );
		}

		return true;
	}

	/**
	 * Whether the 2.0.6 visibility recovery should run.
	 *
	 * @return bool
	 */
	public static function should_run_visibility_recovery() {
		if ( ! self::migration_2050_ran_with_normalization() ) {
			return false;
		}

		return self::has_miswritten_visibility_mods();
	}

	/**
	 * Whether 2.0.5 executed normalization (not a fresh-install skip).
	 *
	 * @return bool
	 */
	private static function migration_2050_ran_with_normalization() {
		if ( ! function_exists( 'hvn_realty_has_migrated' ) || ! hvn_realty_has_migrated( '2.0.5' ) ) {
			return false;
		}

		if ( ! function_exists( 'hvn_realty_get_migration_log' ) ) {
			return false;
		}

		foreach ( hvn_realty_get_migration_log() as $entry ) {
			if ( '2.0.5' !== ( $entry['version'] ?? '' ) || 'success' !== ( $entry['status'] ?? '' ) ) {
				continue;
			}

			$message = (string) ( $entry['message'] ?? '' );

			return false === strpos( $message, 'Skipped: fresh install or already normalized.' );
		}

		return false;
	}

	/**
	 * Modern visibility keys stored without any explicit legacy theme_mod signal.
	 *
	 * @return bool
	 */
	private static function has_miswritten_visibility_mods() {
		$registry   = function_exists( 'hvn_realty_get_default_home_section_order' )
			? hvn_realty_get_default_home_section_order()
			: array( 'hero', 'search', 'why', 'properties', 'types', 'locations', 'agents', 'testimonials', 'blog', 'cta' );
		$legacy_map = self::get_modern_to_legacy_visibility_map();

		foreach ( $registry as $section ) {
			$mod_key = function_exists( 'hvn_realty_get_home_section_visibility_mod' )
				? hvn_realty_get_home_section_visibility_mod( $section )
				: 'hvn_realty_home_show_' . sanitize_key( $section );

			if ( ! self::theme_mod_has_value( $mod_key ) ) {
				continue;
			}

			$legacy_slugs = $legacy_map[ $section ] ?? array();
			if ( empty( $legacy_slugs ) ) {
				continue;
			}

			if ( null === self::derive_modern_section_visibility( $legacy_slugs ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Whether legacy homepage configuration needs normalization.
	 *
	 * @return bool
	 */
	public static function should_run_homepage_section_migration() {
		if ( self::stored_home_section_order_has_legacy_slugs() ) {
			return true;
		}

		if ( self::has_legacy_homepage_visibility_mods() ) {
			return true;
		}

		$installed = function_exists( 'hvn_realty_get_installed_version' )
			? hvn_realty_get_installed_version()
			: (string) get_option( 'hvn_realty_version', '' );

		if ( '' !== $installed && version_compare( $installed, '2.0.0', '<' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Legacy homepage section slugs from pre-2.0 releases.
	 *
	 * @return string[]
	 */
	private static function get_legacy_home_section_slugs() {
		return array(
			'hero-map',
			'hero-search',
			'features',
			'newsletter',
			'featured-properties',
			'department-tabs',
			'latest-properties',
			'property-taxonomies',
			'property-types',
			'property-locations',
			'property-categories',
			'featured-agents',
			'featured-agencies',
			'statistics',
			'cta-banner',
			'latest-posts',
			'footer-cta',
		);
	}

	/**
	 * Map legacy section slugs to Homepage 2.0 registry slugs.
	 *
	 * @return array<string, string>
	 */
	private static function get_legacy_to_modern_section_map() {
		return array(
			'hero-map'             => 'hero',
			'hero-search'            => 'search',
			'features'               => 'why',
			'featured-properties'    => 'properties',
			'latest-properties'      => 'properties',
			'department-tabs'        => 'types',
			'property-types'         => 'types',
			'property-taxonomies'    => 'locations',
			'property-locations'     => 'locations',
			'property-categories'    => 'locations',
			'featured-agents'        => 'agents',
			'featured-agencies'      => 'agents',
			'latest-posts'           => 'blog',
			'cta-banner'             => 'cta',
			'footer-cta'             => 'cta',
			'testimonials'           => 'testimonials',
		);
	}

	/**
	 * Map Homepage 2.0 slugs to legacy slugs used for visibility derivation.
	 *
	 * @return array<string, string[]>
	 */
	private static function get_modern_to_legacy_visibility_map() {
		return array(
			'hero'         => array( 'hero', 'hero-map' ),
			'search'       => array( 'hero-search' ),
			'why'          => array( 'features' ),
			'properties'   => array( 'featured-properties', 'latest-properties' ),
			'types'        => array( 'property-types', 'department-tabs' ),
			'locations'    => array( 'property-taxonomies', 'property-locations', 'property-categories' ),
			'agents'       => array( 'featured-agents', 'featured-agencies' ),
			'testimonials' => array( 'testimonials' ),
			'blog'         => array( 'latest-posts' ),
			'cta'          => array( 'cta-banner', 'footer-cta' ),
		);
	}

	/**
	 * Legacy visibility theme_mod keys that indicate a pre-2.0 homepage.
	 *
	 * @return string[]
	 */
	private static function get_legacy_home_visibility_mod_keys() {
		return array(
			'hvn_realty_home_show_hero_map',
			'hvn_realty_home_show_hero_search',
			'hvn_realty_home_show_features',
			'hvn_realty_home_show_newsletter',
			'hvn_realty_home_show_featured_properties',
			'hvn_realty_home_show_department_tabs',
			'hvn_realty_home_show_latest_properties',
			'hvn_realty_home_show_property_taxonomies',
			'hvn_realty_home_show_property_types',
			'hvn_realty_home_show_property_locations',
			'hvn_realty_home_show_property_categories',
			'hvn_realty_home_show_featured_agents',
			'hvn_realty_home_show_featured_agencies',
			'hvn_realty_home_show_statistics',
			'hvn_realty_home_show_cta_banner',
			'hvn_realty_home_show_latest_posts',
			'hvn_realty_home_show_footer_cta',
		);
	}

	/**
	 * @return bool
	 */
	private static function stored_home_section_order_has_legacy_slugs() {
		$stored = get_theme_mod( 'hvn_realty_home_section_order', '' );
		if ( ! is_string( $stored ) || '' === $stored ) {
			return false;
		}

		$decoded = json_decode( $stored, true );
		if ( ! is_array( $decoded ) ) {
			return false;
		}

		$legacy = self::get_legacy_home_section_slugs();
		foreach ( $decoded as $slug ) {
			if ( in_array( sanitize_key( (string) $slug ), $legacy, true ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	private static function has_legacy_homepage_visibility_mods() {
		foreach ( self::get_legacy_home_visibility_mod_keys() as $key ) {
			if ( self::theme_mod_has_value( $key ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return void
	 */
	private static function normalize_homepage_section_order() {
		$registry = function_exists( 'hvn_realty_get_default_home_section_order' )
			? hvn_realty_get_default_home_section_order()
			: array( 'hero', 'search', 'why', 'properties', 'types', 'locations', 'agents', 'testimonials', 'blog', 'cta' );

		$map           = self::get_legacy_to_modern_section_map();
		$stored        = get_theme_mod( 'hvn_realty_home_section_order', '' );
		$legacy_order  = array();
		$has_legacy    = false;

		if ( is_string( $stored ) && '' !== $stored ) {
			$decoded = json_decode( $stored, true );
			if ( is_array( $decoded ) ) {
				$legacy_order = $decoded;
			}
		}

		if ( empty( $legacy_order ) ) {
			$installed = function_exists( 'hvn_realty_get_installed_version' )
				? hvn_realty_get_installed_version()
				: (string) get_option( 'hvn_realty_version', '' );

			if ( '' !== $installed && version_compare( $installed, '2.0.0', '<' ) ) {
				set_theme_mod(
					'hvn_realty_home_section_order',
					wp_json_encode( array_values( $registry ) )
				);
			}

			return;
		}

		$modern_order = array();
		foreach ( $legacy_order as $slug ) {
			$slug = sanitize_key( (string) $slug );
			if ( in_array( $slug, $registry, true ) ) {
				if ( ! in_array( $slug, $modern_order, true ) ) {
					$modern_order[] = $slug;
				}
				continue;
			}

			if ( ! in_array( $slug, self::get_legacy_home_section_slugs(), true ) ) {
				continue;
			}

			$has_legacy = true;
			$mapped     = $map[ $slug ] ?? '';
			if ( '' === $mapped || in_array( $mapped, $modern_order, true ) ) {
				continue;
			}

			$modern_order[] = $mapped;
		}

		if ( ! $has_legacy && ! self::stored_home_section_order_has_legacy_slugs() ) {
			return;
		}

		foreach ( $registry as $slug ) {
			if ( ! in_array( $slug, $modern_order, true ) ) {
				$modern_order[] = $slug;
			}
		}

		$new_json = wp_json_encode( array_values( $modern_order ) );
		if ( is_string( $stored ) && $stored === $new_json ) {
			return;
		}

		set_theme_mod( 'hvn_realty_home_section_order', $new_json );
	}

	/**
	 * @return void
	 */
	private static function normalize_homepage_section_visibility() {
		$registry = function_exists( 'hvn_realty_get_default_home_section_order' )
			? hvn_realty_get_default_home_section_order()
			: array( 'hero', 'search', 'why', 'properties', 'types', 'locations', 'agents', 'testimonials', 'blog', 'cta' );

		$legacy_map = self::get_modern_to_legacy_visibility_map();

		foreach ( $registry as $section ) {
			$mod_key = function_exists( 'hvn_realty_get_home_section_visibility_mod' )
				? hvn_realty_get_home_section_visibility_mod( $section )
				: 'hvn_realty_home_show_' . sanitize_key( $section );

			if ( self::theme_mod_has_value( $mod_key ) ) {
				continue;
			}

			$legacy_slugs = $legacy_map[ $section ] ?? array();
			if ( empty( $legacy_slugs ) ) {
				continue;
			}

			$derived = self::derive_modern_section_visibility( $legacy_slugs );
			if ( null === $derived ) {
				continue;
			}

			set_theme_mod( $mod_key, $derived );
		}
	}

	/**
	 * Derive a modern section visibility flag from legacy section slugs.
	 *
	 * Only explicit legacy theme_mod values count. Legacy API defaults are ignored
	 * so Homepage 2.x runtime defaults stay intact when the user never saved a choice.
	 *
	 * @param string[] $legacy_slugs Legacy section slugs.
	 * @return bool|null Null when no explicit legacy value exists.
	 */
	private static function derive_modern_section_visibility( array $legacy_slugs ) {
		$any_signal  = false;
		$any_visible = false;

		foreach ( $legacy_slugs as $legacy_slug ) {
			$value = self::get_explicit_legacy_section_visibility( $legacy_slug );
			if ( null === $value ) {
				continue;
			}

			$any_signal = true;
			if ( $value ) {
				$any_visible = true;
			}
		}

		if ( ! $any_signal ) {
			return null;
		}

		return $any_visible;
	}

	/**
	 * Explicit legacy visibility for a section slug, or null when never saved.
	 *
	 * @param string $legacy_slug Legacy section slug.
	 * @return bool|null
	 */
	private static function get_explicit_legacy_section_visibility( $legacy_slug ) {
		if ( 'department-tabs' === $legacy_slug ) {
			$value = get_theme_mod( 'hvn_realty_home_show_department_tabs', null );
			if ( null === $value || '' === $value ) {
				$value = get_theme_mod( 'hvn_realty_home_show_latest_properties', null );
			}
		} else {
			$key   = 'hvn_realty_home_show_' . sanitize_key( str_replace( '-', '_', $legacy_slug ) );
			$value = get_theme_mod( $key, null );
		}

		if ( null === $value || '' === $value ) {
			return null;
		}

		return (bool) $value;
	}

	/**
	 * Whether a theme_mod is meaningfully set.
	 *
	 * @param string $key Theme mod key.
	 * @return bool
	 */
	private static function theme_mod_has_value( $key ) {
		$value = get_theme_mod( $key, null );

		return null !== $value && '' !== $value;
	}
}
