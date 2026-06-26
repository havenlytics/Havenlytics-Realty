<?php
/**
 * Realty admin — first-time onboarding tutorial modal.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option: user dismissed or completed the onboarding video. */
define( 'HVN_REALTY_ONBOARDING_VIDEO_SEEN_OPTION', 'hvn_realty_onboarding_video_seen' );

/** Option: site setup marked complete (blocks auto-show). */
define( 'HVN_REALTY_SETUP_COMPLETE_OPTION', 'hvn_realty_setup_complete' );

/** Transient: allow one auto-show after theme activation. */
define( 'HVN_REALTY_ONBOARDING_AUTOSHOW_TRANSIENT', 'hvn_realty_onboarding_autoshow' );

/** YouTube video ID for the setup tutorial. */
define( 'HVN_REALTY_ONBOARDING_VIDEO_ID', 'cEVQ0uhwiHc' );

/**
 * Flag onboarding auto-show after theme activation.
 *
 * @return void
 */
function hvn_realty_set_onboarding_autoshow_transient() {
	if ( 'havenlytics-realty' !== get_template() ) {
		return;
	}

	set_transient( HVN_REALTY_ONBOARDING_AUTOSHOW_TRANSIENT, 1, HOUR_IN_SECONDS );
}
add_action( 'after_switch_theme', 'hvn_realty_set_onboarding_autoshow_transient', 6 );

/**
 * Persist setup-complete flag for existing configured sites.
 *
 * @return void
 */
function hvn_realty_maybe_persist_setup_complete_flag() {
	if ( get_option( HVN_REALTY_SETUP_COMPLETE_OPTION, false ) ) {
		return;
	}

	if ( function_exists( 'hvn_realty_is_site_setup_complete' ) && hvn_realty_is_site_setup_complete() ) {
		update_option( HVN_REALTY_SETUP_COMPLETE_OPTION, true, false );
	}
}

/**
 * Whether onboarding auto-show is blocked.
 *
 * @return bool
 */
function hvn_realty_is_onboarding_auto_show_blocked() {
	if ( get_option( HVN_REALTY_SETUP_COMPLETE_OPTION, false ) ) {
		return true;
	}

	if ( get_option( HVN_REALTY_ONBOARDING_VIDEO_SEEN_OPTION, false ) ) {
		return true;
	}

	if ( function_exists( 'hvn_realty_is_site_setup_complete' ) && hvn_realty_is_site_setup_complete() ) {
		return true;
	}

	return false;
}

/**
 * Whether the tutorial modal should auto-open on the Realty page.
 *
 * @return bool
 */
function hvn_realty_should_auto_show_onboarding_modal() {
	if ( hvn_realty_is_onboarding_auto_show_blocked() ) {
		return false;
	}

	return (bool) get_transient( HVN_REALTY_ONBOARDING_AUTOSHOW_TRANSIENT );
}

/**
 * Mark onboarding video as seen and clear auto-show transient.
 *
 * @return void
 */
function hvn_realty_mark_onboarding_video_seen() {
	update_option( HVN_REALTY_ONBOARDING_VIDEO_SEEN_OPTION, true, false );
	delete_transient( HVN_REALTY_ONBOARDING_AUTOSHOW_TRANSIENT );
}

/**
 * AJAX: mark onboarding tutorial as seen.
 *
 * @return void
 */
function hvn_realty_ajax_mark_onboarding_video_seen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
	}

	check_ajax_referer( 'hvn_realty_onboarding_seen', 'nonce' );

	hvn_realty_mark_onboarding_video_seen();

	wp_send_json_success();
}
add_action( 'wp_ajax_hvn_realty_mark_onboarding_video_seen', 'hvn_realty_ajax_mark_onboarding_video_seen' );

/**
 * Enqueue onboarding modal assets on the Realty admin page.
 *
 * @param string $hook_suffix Admin hook suffix.
 * @return void
 */
function hvn_realty_onboarding_admin_assets( $hook_suffix ) {
	if ( 'toplevel_page_' . HVN_REALTY_ADMIN_SLUG !== $hook_suffix ) {
		return;
	}

	hvn_realty_maybe_persist_setup_complete_flag();

	$script_path = get_template_directory() . '/assets/js/admin-realty-onboarding.js';
	if ( ! file_exists( $script_path ) ) {
		return;
	}

	wp_enqueue_script(
		'hvn-realty-onboarding',
		HVN_REALTY_TEMPLATE_URL . '/assets/js/admin-realty-onboarding.js',
		array( 'jquery' ),
		HVN_REALTY_VERSION,
		true
	);

	wp_localize_script(
		'hvn-realty-onboarding',
		'hvnRealtyOnboarding',
		array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'hvn_realty_onboarding_seen' ),
			'videoId'  => HVN_REALTY_ONBOARDING_VIDEO_ID,
			'autoShow' => hvn_realty_should_auto_show_onboarding_modal(),
			'delayMs'  => 2000,
			'i18n'     => array(
				'close' => __( 'Close', 'havenlytics-realty' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'hvn_realty_onboarding_admin_assets', 20 );

/**
 * Setup Tutorial card in the Realty sidebar.
 *
 * @return void
 */
function hvn_realty_render_onboarding_tutorial_card() {
	?>
	<div class="hvn-realty-admin__panel hvn-realty-admin__panel--tutorial">
		<h2><?php esc_html_e( 'Setup Tutorial', 'havenlytics-realty' ); ?></h2>
		<p class="hvn-realty-admin__panel-desc">
			<?php esc_html_e( 'Watch a quick walkthrough for installing Havenlytics, importing properties, and launching your site.', 'havenlytics-realty' ); ?>
		</p>
		<p>
			<button type="button" class="button button-secondary" data-hvn-onboarding-open>
				<?php esc_html_e( 'Watch Tutorial', 'havenlytics-realty' ); ?>
			</button>
		</p>
	</div>
	<?php
}

/**
 * Onboarding tutorial modal markup.
 *
 * @return void
 */
function hvn_realty_render_onboarding_modal() {
	$video_id  = preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) HVN_REALTY_ONBOARDING_VIDEO_ID );
	$embed_url = esc_url( 'https://www.youtube.com/embed/' . $video_id );
	?>
	<div
		id="hvn-realty-onboarding-modal"
		class="hvn-realty-onboarding"
		role="dialog"
		aria-modal="true"
		aria-labelledby="hvn-realty-onboarding-title"
		aria-hidden="true"
		hidden
	>
		<div class="hvn-realty-onboarding__overlay" data-hvn-onboarding-dismiss tabindex="-1" aria-hidden="true"></div>
		<div class="hvn-realty-onboarding__dialog">
			<button type="button" class="hvn-realty-onboarding__close" data-hvn-onboarding-dismiss aria-label="<?php esc_attr_e( 'Close', 'havenlytics-realty' ); ?>">
				<span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
			</button>
			<header class="hvn-realty-onboarding__header">
				<h2 id="hvn-realty-onboarding-title" class="hvn-realty-onboarding__title">
					<?php esc_html_e( 'Welcome to Havenlytics Realty', 'havenlytics-realty' ); ?>
				</h2>
				<p class="hvn-realty-onboarding__subtitle">
					<?php esc_html_e( 'Watch this quick setup guide and launch your real estate website in minutes.', 'havenlytics-realty' ); ?>
				</p>
			</header>
			<div class="hvn-realty-onboarding__video">
				<iframe
					id="hvn-realty-onboarding-video"
					title="<?php esc_attr_e( 'Havenlytics Realty setup tutorial', 'havenlytics-realty' ); ?>"
					src="<?php echo esc_url( 'about:blank' ); ?>"
					data-src-base="<?php echo esc_url( $embed_url ); ?>"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					allowfullscreen
					loading="lazy"
					referrerpolicy="strict-origin-when-cross-origin"
				></iframe>
			</div>
			<footer class="hvn-realty-onboarding__actions">
				<button type="button" class="button button-primary" data-hvn-onboarding-dismiss data-hvn-onboarding-action="start">
					<?php esc_html_e( 'Get Started', 'havenlytics-realty' ); ?>
				</button>
				<button type="button" class="button button-secondary" data-hvn-onboarding-dismiss data-hvn-onboarding-action="later">
					<?php esc_html_e( 'Watch Later', 'havenlytics-realty' ); ?>
				</button>
				<button type="button" class="button button-link" data-hvn-onboarding-dismiss data-hvn-onboarding-action="close">
					<?php esc_html_e( 'Close', 'havenlytics-realty' ); ?>
				</button>
			</footer>
		</div>
	</div>
	<?php
}
