<?php
/**
 * Back to top button template part.
 *
 * @package Havenlytics_Realty
 */

if ( ! hvn_realty_show_back_to_top() ) {
	return;
}
?>
<button type="button" id="hvn-scroll-top" class="hvn-theme-back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'havenlytics-realty' ); ?>">
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
		<polyline points="18 15 12 9 6 15"></polyline>
	</svg>
	<span class="screen-reader-text"><?php esc_html_e( 'Back to top', 'havenlytics-realty' ); ?></span>
</button>
