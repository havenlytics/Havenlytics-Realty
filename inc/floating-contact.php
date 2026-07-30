<?php
/**
 * Floating contact action menu — helpers, assets, and item config.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the floating contact menu is enabled in Customizer.
 *
 * @return bool
 */
function hvn_realty_floating_contact_enabled() {
	return (bool) get_theme_mod( 'hvn_realty_floating_contact_enable', true );
}

/**
 * Digits-only phone string for tel:/wa.me links.
 *
 * @param string $value Raw phone or WhatsApp value.
 * @return string
 */
function hvn_realty_floating_contact_digits( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/**
 * Build contact action items (extensible).
 *
 * Reuses footer phone/email theme_mods; WhatsApp is dedicated.
 *
 * @return array<int, array<string, string>>
 */
function hvn_realty_get_floating_contact_items() {
	$phone    = (string) get_theme_mod( 'hvn_realty_footer_contact_phone', '' );
	$email    = (string) get_theme_mod( 'hvn_realty_footer_contact_email', '' );
	$whatsapp = (string) get_theme_mod( 'hvn_realty_floating_contact_whatsapp', '' );

	if ( '' === $whatsapp && '' !== $phone ) {
		$whatsapp = $phone;
	}

	if ( '' === $email ) {
		$admin_email = (string) get_option( 'admin_email', '' );
		if ( is_email( $admin_email ) ) {
			$email = $admin_email;
		}
	}

	$items = array();

	$wa_digits = hvn_realty_floating_contact_digits( $whatsapp );
	if ( '' !== $wa_digits ) {
		$items[] = array(
			'id'    => 'whatsapp',
			'label' => __( 'WhatsApp', 'havenlytics-realty' ),
			'href'  => 'https://wa.me/' . $wa_digits,
			'icon'  => 'whatsapp',
		);
	}

	$tel_digits = hvn_realty_floating_contact_digits( $phone );
	if ( '' !== $tel_digits ) {
		$items[] = array(
			'id'    => 'phone',
			'label' => __( 'Call us', 'havenlytics-realty' ),
			'href'  => 'tel:' . $tel_digits,
			'icon'  => 'phone',
		);
	}

	$email = sanitize_email( $email );
	if ( is_email( $email ) ) {
		$items[] = array(
			'id'    => 'email',
			'label' => __( 'Email us', 'havenlytics-realty' ),
			'href'  => 'mailto:' . $email,
			'icon'  => 'email',
		);
	}

	/**
	 * Filter floating contact menu items.
	 *
	 * @param array<int, array<string, string>> $items Contact items.
	 */
	$items = apply_filters( 'hvn_realty_floating_contact_items', $items );

	return is_array( $items ) ? array_values( $items ) : array();
}

/**
 * Whether the floating contact menu should render on the current view.
 *
 * @return bool
 */
function hvn_realty_should_show_floating_contact() {
	if ( ! hvn_realty_floating_contact_enabled() ) {
		return false;
	}

	// Avoid stacking with the plugin single-property contact dock.
	if ( function_exists( 'is_singular' ) && is_singular( 'hvnly_property' ) ) {
		return false;
	}

	$items = hvn_realty_get_floating_contact_items();
	return ! empty( $items );
}

/**
 * Enqueue floating contact assets when the menu will render.
 *
 * @return void
 */
function hvn_realty_enqueue_floating_contact_assets() {
	if ( is_admin() || ! hvn_realty_should_show_floating_contact() ) {
		return;
	}

	$deps = array( 'hvn-realty-theme' );

	if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {
		hvn_realty_enqueue_theme_style( 'hvn-realty-floating-contact', 'assets/css/floating-contact.css', $deps );
		hvn_realty_enqueue_theme_script( 'hvn-realty-floating-contact', 'assets/js/floating-contact.js', array(), false, true );
		return;
	}

	$version = defined( 'HVN_REALTY_VERSION' ) ? HVN_REALTY_VERSION : false;
	$base    = defined( 'HVN_REALTY_TEMPLATE_URL' ) ? HVN_REALTY_TEMPLATE_URL : get_template_directory_uri();

	wp_enqueue_style(
		'hvn-realty-floating-contact',
		$base . '/assets/css/floating-contact.css',
		$deps,
		$version
	);
	wp_enqueue_script(
		'hvn-realty-floating-contact',
		$base . '/assets/js/floating-contact.js',
		array(),
		$version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_floating_contact_assets', 35 );
