<?php
/**
 * Havenlytics Realty theme upgrade and migration manager.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracks theme version, runs registered migrations, and logs results.
 */
class HVN_Realty_Upgrade_Manager {

	/** @var string Installed theme version option. */
	public const VERSION_OPTION = 'hvn_realty_version';

	/** @var string Migration execution log option. */
	public const MIGRATION_LOG_OPTION = 'hvn_realty_migration_log';

	/**
	 * Version => callback map. Register future migrations here.
	 *
	 * @var array<string, callable>
	 */
	protected static $migrations = array(
		'1.16.0' => array( 'HVN_Realty_Migrations', 'migrate_1160_locations_to_taxonomies' ),
		'1.22.0' => array( 'HVN_Realty_Migrations', 'migrate_1220_starter_sites_baseline' ),
		'1.23.0' => array( 'HVN_Realty_Migrations', 'migrate_1230_active_starter_option' ),
		'2.0.5'  => array( 'HVN_Realty_Migrations', 'migrate_2050_homepage_sections' ),
		'2.0.6'  => array( 'HVN_Realty_Migrations', 'migrate_2060_visibility_recovery' ),
	);

	/**
	 * Boot upgrade checks after theme setup.
	 *
	 * @return void
	 */
	public static function boot() {
		self::maybe_run_upgrades();
		self::maybe_run_patch_migrations();
	}

	/**
	 * Installed (last migrated) theme version.
	 *
	 * @return string
	 */
	public static function get_installed_version() {
		$version = get_option( self::VERSION_OPTION, '' );

		return is_string( $version ) ? $version : '';
	}

	/**
	 * Current theme package version.
	 *
	 * @return string
	 */
	public static function get_current_version() {
		return defined( 'HVN_REALTY_VERSION' ) ? (string) HVN_REALTY_VERSION : '';
	}

	/**
	 * Whether a migration version completed successfully.
	 *
	 * @param string $version Migration version key.
	 * @return bool
	 */
	public static function has_migrated( $version ) {
		$log = self::get_migration_log();

		foreach ( $log as $entry ) {
			if (
				isset( $entry['version'], $entry['status'] )
				&& $version === $entry['version']
				&& 'success' === $entry['status']
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Migration log entries (newest last).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_migration_log() {
		$log = get_option( self::MIGRATION_LOG_OPTION, array() );

		return is_array( $log ) ? $log : array();
	}

	/**
	 * Registered migration versions.
	 *
	 * @return array<int, string>
	 */
	public static function get_registered_migration_versions() {
		$versions = array_keys( self::$migrations );
		usort( $versions, 'version_compare' );

		return $versions;
	}

	/**
	 * Compare stored and current versions; run pending migrations.
	 *
	 * @return void
	 */
	private static function maybe_run_upgrades() {
		$current_version  = self::get_current_version();
		$installed_version = self::get_installed_version();

		if ( '' === $current_version ) {
			return;
		}

		if ( '' === $installed_version ) {
			self::run_pending_migrations( '0.0.0', $current_version );
			update_option( self::VERSION_OPTION, $current_version, false );

			return;
		}

		if ( version_compare( $installed_version, $current_version, '>=' ) ) {
			return;
		}

		self::run_pending_migrations( $installed_version, $current_version );
		update_option( self::VERSION_OPTION, $current_version, false );
	}

	/**
	 * Run one-time migrations that must execute even when the stored version
	 * already matches the package (patch-level data fixes).
	 *
	 * @return void
	 */
	private static function maybe_run_patch_migrations() {
		$patch_migrations = array( '2.0.5', '2.0.6' );

		foreach ( $patch_migrations as $migration_version ) {
			if ( ! isset( self::$migrations[ $migration_version ] ) ) {
				continue;
			}

			if ( self::has_migrated( $migration_version ) ) {
				continue;
			}

			$skip_message = self::get_patch_migration_skip_message( $migration_version );
			if ( null !== $skip_message ) {
				self::append_migration_log( $migration_version, 'success', $skip_message );
				continue;
			}

			self::run_migration( $migration_version );
		}
	}

	/**
	 * Optional skip reason for a patch migration (logged as success, not executed).
	 *
	 * @param string $migration_version Migration version key.
	 * @return string|null Skip message or null to run the migration.
	 */
	private static function get_patch_migration_skip_message( $migration_version ) {
		if ( '2.0.5' === $migration_version ) {
			if ( is_callable( array( 'HVN_Realty_Migrations', 'should_run_homepage_section_migration' ) )
				&& ! HVN_Realty_Migrations::should_run_homepage_section_migration()
			) {
				return 'Skipped: fresh install or already normalized.';
			}

			return null;
		}

		if ( '2.0.6' === $migration_version ) {
			if ( is_callable( array( 'HVN_Realty_Migrations', 'should_run_visibility_recovery' ) )
				&& ! HVN_Realty_Migrations::should_run_visibility_recovery()
			) {
				return 'Skipped: no miswritten homepage visibility keys.';
			}

			return null;
		}

		return null;
	}

	/**
	 * Run migrations between two versions (inclusive of target).
	 *
	 * @param string $from_version Previously installed version.
	 * @param string $to_version   Current theme version.
	 * @return void
	 */
	private static function run_pending_migrations( $from_version, $to_version ) {
		foreach ( self::get_registered_migration_versions() as $migration_version ) {
			if ( version_compare( $migration_version, $from_version, '<=' ) ) {
				continue;
			}

			if ( version_compare( $migration_version, $to_version, '>' ) ) {
				continue;
			}

			if ( self::has_migrated( $migration_version ) ) {
				continue;
			}

			self::run_migration( $migration_version );
		}
	}

	/**
	 * Execute a single migration and append to the log.
	 *
	 * @param string $migration_version Migration version key.
	 * @return void
	 */
	private static function run_migration( $migration_version ) {
		$callback = self::$migrations[ $migration_version ] ?? null;

		if ( ! is_callable( $callback ) ) {
			self::append_migration_log(
				$migration_version,
				'skipped',
				'Migration callback is not callable.'
			);
			return;
		}

		try {
			$result = call_user_func( $callback );

			if ( false === $result ) {
				self::append_migration_log(
					$migration_version,
					'failed',
					'Migration callback returned false.'
				);
				return;
			}

			self::append_migration_log( $migration_version, 'success', '' );
		} catch ( Throwable $exception ) {
			self::append_migration_log(
				$migration_version,
				'failed',
				$exception->getMessage()
			);
		}
	}

	/**
	 * Append a migration log entry.
	 *
	 * @param string $version Migration version.
	 * @param string $status  success|failed|skipped.
	 * @param string $message Optional message.
	 * @return void
	 */
	private static function append_migration_log( $version, $status, $message = '' ) {
		$log   = self::get_migration_log();
		$log[] = array(
			'version'   => (string) $version,
			'status'    => (string) $status,
			'timestamp' => time(),
			'message'   => (string) $message,
		);

		// Keep the log bounded for long-lived installs.
		if ( count( $log ) > 50 ) {
			$log = array_slice( $log, -50 );
		}

		update_option( self::MIGRATION_LOG_OPTION, $log, false );
	}
}

/**
 * Installed Havenlytics Realty theme version.
 *
 * @return string
 */
function hvn_realty_get_installed_version() {
	return HVN_Realty_Upgrade_Manager::get_installed_version();
}

/**
 * Whether a migration version completed successfully.
 *
 * @param string $version Migration version key (e.g. 1.16.0).
 * @return bool
 */
function hvn_realty_has_migrated( $version ) {
	return HVN_Realty_Upgrade_Manager::has_migrated( $version );
}

/**
 * Migration execution log.
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_migration_log() {
	return HVN_Realty_Upgrade_Manager::get_migration_log();
}
