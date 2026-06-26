<?php
/**
 * Core custom header and background support.
 *
 * Site branding uses custom-logo; global background color uses Havenlytics
 * Theme Settings. Core header/background image panels stay hidden in
 * inc/customizer/panel-integration.php.
 *
 * @package Havenlytics_Realty
 */

/**
 * Register WordPress custom header and background support.
 */
function hvn_realty_custom_header_setup() {
	add_theme_support(
		'custom-header',
		apply_filters(
			'hvn_realty_custom_header_args',
			array(
				'default-image'      => '',
				'default-text-color' => '1e1e2f',
				'width'              => 1920,
				'height'             => 400,
				'flex-height'        => true,
				'flex-width'         => true,
				'uploads'            => true,
				'header-text'        => false,
				'wp-head-callback'   => 'hvn_realty_custom_header_style',
			)
		)
	);

	add_theme_support(
		'custom-background',
		apply_filters(
			'hvn_realty_custom_background_args',
			array(
				'default-color' => 'f8f8f8',
				'default-image' => '',
			)
		)
	);
}
add_action( 'after_setup_theme', 'hvn_realty_custom_header_setup' );

/**
 * Output styles when a core custom header image is set.
 */
function hvn_realty_custom_header_style() {
	$header_image = get_header_image();

	if ( empty( $header_image ) ) {
		return;
	}
	?>
	<style id="hvn-realty-custom-header-styles" type="text/css">
		.hvn-theme-header {
			background-image: url(<?php echo esc_url( $header_image ); ?>);
			background-size: cover;
			background-position: center center;
		}
	</style>
	<?php
}
