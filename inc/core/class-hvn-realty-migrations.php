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
	 * Homepage 2.3.0 / Homepage 3.0 layout migration.
	 *
	 * Appends new sections, maps legacy CTA/hero theme_mods into current keys,
	 * and never deletes user settings.
	 *
	 * @return bool True on success.
	 */
	public static function migrate_230_homepage_v3() {
		self::migrate_230_append_new_sections();
		self::migrate_230_map_legacy_cta_mods();
		self::migrate_230_map_legacy_hero_image();
		self::migrate_230_default_hero_bg_mode();

		return true;
	}

	/**
	 * Whether the 2.3.0 homepage migration should mutate theme_mods.
	 *
	 * @return bool
	 */
	public static function should_run_homepage_v3_migration() {
		$order = get_theme_mod( 'hvn_realty_home_section_order', '' );
		if ( is_string( $order ) && '' !== $order ) {
			$decoded = json_decode( $order, true );
			if ( is_array( $decoded ) ) {
				$has_map         = in_array( 'map', $decoded, true );
				$has_collections = in_array( 'collections', $decoded, true );
				if ( ! $has_map || ! $has_collections ) {
					return true;
				}
			}
		}

		if ( ! self::theme_mod_has_value( 'hvn_realty_home_cta_title' )
			&& self::theme_mod_has_value( 'hvn_realty_home_cta_headline' )
		) {
			return true;
		}

		if ( ! self::theme_mod_has_value( 'hvn_realty_home_hero_image_a' )
			&& self::theme_mod_has_value( 'hvn_realty_home_hero_image_b' )
		) {
			return true;
		}

		$installed = function_exists( 'hvn_realty_get_installed_version' )
			? hvn_realty_get_installed_version()
			: (string) get_option( 'hvn_realty_version', '' );

		// Existing installs below 2.3 should always normalize once.
		if ( '' !== $installed && version_compare( $installed, '2.3.0', '<' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Append map + collections to a saved section order without reordering
	 * the user's existing sections.
	 *
	 * @return void
	 */
	private static function migrate_230_append_new_sections() {
		$registry = function_exists( 'hvn_realty_get_default_home_section_order' )
			? hvn_realty_get_default_home_section_order()
			: array();

		$stored  = get_theme_mod( 'hvn_realty_home_section_order', '' );
		$decoded = array();

		if ( is_string( $stored ) && '' !== $stored ) {
			$maybe = json_decode( $stored, true );
			if ( is_array( $maybe ) ) {
				$decoded = array_values(
					array_filter(
						array_map( 'sanitize_key', $maybe )
					)
				);
			}
		}

		if ( empty( $decoded ) ) {
			// Fresh / unset order — sanitize path will use registry defaults.
			return;
		}

		$append = array( 'map', 'collections' );
		$changed = false;
		foreach ( $append as $slug ) {
			if ( ! in_array( $slug, $registry, true ) ) {
				continue;
			}
			if ( in_array( $slug, $decoded, true ) ) {
				continue;
			}
			// Insert map after locations when possible; collections after why/map.
			if ( 'map' === $slug ) {
				$loc_i = array_search( 'locations', $decoded, true );
				if ( false !== $loc_i ) {
					array_splice( $decoded, (int) $loc_i + 1, 0, array( 'map' ) );
				} else {
					$decoded[] = 'map';
				}
			} elseif ( 'collections' === $slug ) {
				$why_i = array_search( 'why', $decoded, true );
				$map_i = array_search( 'map', $decoded, true );
				$insert_at = false !== $why_i ? (int) $why_i + 1 : ( false !== $map_i ? (int) $map_i + 1 : count( $decoded ) );
				array_splice( $decoded, $insert_at, 0, array( 'collections' ) );
			}
			$changed = true;
		}

		if ( $changed ) {
			set_theme_mod( 'hvn_realty_home_section_order', wp_json_encode( array_values( $decoded ) ) );
		}
	}

	/**
	 * Copy legacy CTA theme_mods into Homepage 3.0 keys when unset.
	 *
	 * @return void
	 */
	private static function migrate_230_map_legacy_cta_mods() {
		$map = array(
			'hvn_realty_home_cta_headline'     => 'hvn_realty_home_cta_title',
			'hvn_realty_home_cta_subtext'      => 'hvn_realty_home_cta_subtitle',
			'hvn_realty_home_cta_primary_text' => 'hvn_realty_home_cta_primary_label',
		);

		foreach ( $map as $legacy => $modern ) {
			if ( self::theme_mod_has_value( $modern ) ) {
				continue;
			}
			if ( ! self::theme_mod_has_value( $legacy ) ) {
				continue;
			}
			set_theme_mod( $modern, get_theme_mod( $legacy ) );
		}
	}

	/**
	 * Prefer inset hero image as primary when large image was never set.
	 *
	 * @return void
	 */
	private static function migrate_230_map_legacy_hero_image() {
		if ( self::theme_mod_has_value( 'hvn_realty_home_hero_image_a' ) ) {
			return;
		}
		if ( ! self::theme_mod_has_value( 'hvn_realty_home_hero_image_b' ) ) {
			return;
		}
		set_theme_mod( 'hvn_realty_home_hero_image_a', get_theme_mod( 'hvn_realty_home_hero_image_b' ) );
	}

	/**
	 * Ensure hero background mode theme_mod exists (static default).
	 *
	 * @return void
	 */
	private static function migrate_230_default_hero_bg_mode() {
		if ( self::theme_mod_has_value( 'hvn_realty_home_hero_bg_mode' ) ) {
			return;
		}
		set_theme_mod( 'hvn_realty_home_hero_bg_mode', 'static' );
	}

	/**
	 * Homepage 2.3.1 — normalize invalid testimonial slider settings.
	 *
	 * Upgraded sites with 4+ testimonials exposed a multi-slide flex overflow
	 * bug. This migration only re-sanitizes slider-related theme_mods; it does
	 * not delete or rewrite testimonial copy/photos beyond the existing
	 * sanitizer (which preserves valid items).
	 *
	 * @return bool True on success.
	 */
	public static function migrate_231_testimonials_slider() {
		self::migrate_231_normalize_testimonials_json();
		self::migrate_231_clamp_testimonials_speed();
		self::migrate_231_normalize_testimonials_autoplay();

		return true;
	}

	/**
	 * Whether the 2.3.1 testimonials slider migration should mutate theme_mods.
	 *
	 * @return bool
	 */
	public static function should_run_testimonials_slider_migration() {
		$raw = get_theme_mod( 'hvn_realty_home_testimonials', null );

		// Corrupt / non-JSON storage from older builds.
		if ( is_array( $raw ) ) {
			return true;
		}

		if ( is_string( $raw ) && '' !== $raw ) {
			$decoded = json_decode( $raw, true );
			if ( ! is_array( $decoded ) ) {
				return true;
			}
		}

		$speed = get_theme_mod( 'hvn_realty_home_testimonials_speed', null );
		if ( null !== $speed && '' !== $speed ) {
			$speed = absint( $speed );
			if ( $speed < 2000 || $speed > 15000 ) {
				return true;
			}
		}

		$autoplay = get_theme_mod( 'hvn_realty_home_testimonials_autoplay', null );
		if ( null !== $autoplay && ! is_bool( $autoplay ) && ! in_array( $autoplay, array( 0, 1, '0', '1', true, false ), true ) ) {
			return true;
		}

		// Always safe to re-clamp once for upgraded 2.3.0 installs.
		$installed = function_exists( 'hvn_realty_get_installed_version' )
			? hvn_realty_get_installed_version()
			: (string) get_option( 'hvn_realty_version', '' );

		if ( '' !== $installed && version_compare( $installed, '2.3.1', '<' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Re-run testimonials JSON through the sanitizer when present.
	 *
	 * @return void
	 */
	private static function migrate_231_normalize_testimonials_json() {
		$raw = get_theme_mod( 'hvn_realty_home_testimonials', null );
		if ( null === $raw || '' === $raw ) {
			return;
		}

		if ( ! function_exists( 'hvn_realty_sanitize_home_testimonials' ) ) {
			return;
		}

		$sanitized = hvn_realty_sanitize_home_testimonials( $raw );
		if ( ! is_string( $sanitized ) || '' === $sanitized ) {
			return;
		}

		// Only write when storage shape changed (array → JSON, invalid → valid).
		if ( is_array( $raw ) || ( is_string( $raw ) && $raw !== $sanitized ) ) {
			$before = is_string( $raw ) ? json_decode( $raw, true ) : $raw;
			$after  = json_decode( $sanitized, true );
			$before_count = is_array( $before ) ? count( $before ) : -1;
			$after_count  = is_array( $after ) ? count( $after ) : -1;

			// Never wipe user content if sanitizer unexpectedly emptied a populated set.
			if ( $before_count > 0 && 0 === $after_count ) {
				return;
			}

			set_theme_mod( 'hvn_realty_home_testimonials', $sanitized );
		}
	}

	/**
	 * Clamp autoplay speed into the supported 2000–15000 ms range.
	 *
	 * @return void
	 */
	private static function migrate_231_clamp_testimonials_speed() {
		$speed = get_theme_mod( 'hvn_realty_home_testimonials_speed', null );
		if ( null === $speed || '' === $speed ) {
			return;
		}

		$clamped = absint( $speed );
		$clamped = max( 2000, min( 15000, $clamped ) );

		if ( (int) $speed !== $clamped ) {
			set_theme_mod( 'hvn_realty_home_testimonials_speed', $clamped );
		}
	}

	/**
	 * Coerce autoplay theme_mod to a real boolean.
	 *
	 * @return void
	 */
	private static function migrate_231_normalize_testimonials_autoplay() {
		$autoplay = get_theme_mod( 'hvn_realty_home_testimonials_autoplay', null );
		if ( null === $autoplay ) {
			return;
		}

		$bool = (bool) $autoplay;
		if ( is_string( $autoplay ) && in_array( strtolower( $autoplay ), array( 'false', 'off', 'no', '' ), true ) ) {
			$bool = false;
		}

		if ( $autoplay !== $bool ) {
			set_theme_mod( 'hvn_realty_home_testimonials_autoplay', $bool );
		}
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
