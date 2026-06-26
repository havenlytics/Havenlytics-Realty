<?php
/**
 * Theme preloader markup.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_show_preloader' ) || ! hvn_realty_show_preloader() ) {
	return;
}
?>
<div id="hvn-theme-preloader" class="hvn-theme-preloader" aria-hidden="true">
	<div class="hvn-theme-preloader__inner" role="status" aria-live="polite">
		<span class="hvn-theme-preloader__logo" aria-hidden="true"></span>
		<span class="hvn-theme-preloader__label"><?php esc_html_e( 'Loading', 'havenlytics-realty' ); ?></span>
	</div>
</div>
<script>document.body.classList.add('hvn-theme-is-loading');</script>
