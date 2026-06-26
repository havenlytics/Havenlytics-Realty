<?php
/**
 * Admin welcome notice after Havenlytics Realty theme activation.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option key for dismissing the welcome notice.
 */
define( 'HVN_REALTY_WELCOME_DISMISSED_OPTION', 'hvnly_realty_welcome_dismissed' );

/**
 * Reset dismiss flag when this theme is activated.
 *
 * @return void
 */
function hvn_realty_theme_welcome_reset() {
	if ( 'havenlytics-realty' !== get_template() ) {
		return;
	}

	delete_option( HVN_REALTY_WELCOME_DISMISSED_OPTION );
}
add_action( 'after_switch_theme', 'hvn_realty_theme_welcome_reset' );

/**
 * Handle notice dismissal.
 *
 * @return void
 */
function hvn_realty_theme_welcome_handle_dismiss() {
	if ( ! isset( $_GET['hvn-realty-dismiss-welcome'] ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'hvn_realty_welcome_dismiss' ) ) {
		return;
	}

	update_option( HVN_REALTY_WELCOME_DISMISSED_OPTION, true );

	wp_safe_redirect( remove_query_arg( array( 'hvn-realty-dismiss-welcome', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'hvn_realty_theme_welcome_handle_dismiss' );

/**
 * Whether the welcome notice should display.
 *
 * @return bool
 */
function hvn_realty_should_show_welcome_notice() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return false;
	}

	if ( get_option( HVN_REALTY_WELCOME_DISMISSED_OPTION, false ) ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_site_setup_complete' ) && hvn_realty_is_site_setup_complete() ) {
		return false;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && isset( $_GET['page'] ) && 'hvnly-property-import' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
		return false;
	}

	return true;
}

/**
 * Render the theme welcome admin notice.
 *
 * @return void
 */
function hvn_realty_theme_welcome_notice() {
	if ( ! hvn_realty_should_show_welcome_notice() ) {
		return;
	}

	$dismiss_url = wp_nonce_url(
		add_query_arg( 'hvn-realty-dismiss-welcome', '1' ),
		'hvn_realty_welcome_dismiss'
	);

	$plugin_active = hvn_realty_is_havenlytics_plugin_active();
	$realty_url    = function_exists( 'hvn_realty_get_realty_admin_url' ) ? hvn_realty_get_realty_admin_url() : admin_url( 'themes.php' );

	if ( $plugin_active ) {
		$primary_url  = admin_url( 'edit.php?post_type=hvnly_property&page=hvnly-property-import' );
		$primary_text = esc_html__( 'Run the Setup Wizard', 'havenlytics-realty' );
		$message      = esc_html__( 'Welcome to Havenlytics Realty. Import demo properties to launch your site in minutes.', 'havenlytics-realty' );
	} else {
		$primary_url  = hvn_realty_get_plugin_install_url();
		$primary_text = esc_html__( 'Install Havenlytics Plugin', 'havenlytics-realty' );
		$message      = esc_html__( 'Welcome to Havenlytics Realty. Install the free Havenlytics plugin to unlock your real estate homepage.', 'havenlytics-realty' );
	}

	?>
	<div class="notice notice-success is-dismissible hvn-realty-welcome-notice">
		<p>
			<strong><?php esc_html_e( 'Havenlytics Realty', 'havenlytics-realty' ); ?></strong> –
			<?php echo esc_html( $message ); ?>
		</p>
		<p class="submit">
			<a href="<?php echo esc_url( $primary_url ); ?>" class="button button-primary">
				<?php echo esc_html( $primary_text ); ?>
			</a>
			<a href="<?php echo esc_url( $realty_url ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Open Realty', 'havenlytics-realty' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Customize Theme', 'havenlytics-realty' ); ?>
			</a>
			<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button button-secondary">
				<?php esc_html_e( 'Dismiss', 'havenlytics-realty' ); ?>
			</a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'hvn_realty_theme_welcome_notice' );
