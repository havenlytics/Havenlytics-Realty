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
