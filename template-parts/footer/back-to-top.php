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
	<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
		<path d="M3 11L9 5L15 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
	</svg>
	<span class="screen-reader-text"><?php esc_html_e( 'Back to top', 'havenlytics-realty' ); ?></span>
</button>
