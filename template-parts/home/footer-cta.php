<?php
/**
 * Homepage: Footer CTA section.
 *
 * @package Havenlytics_Realty
 */

$text       = hvn_realty_get_home_footer_cta_text();
$search_url = hvn_realty_get_plugin_page_url( 'property_search' );
?>
<section class="hvn-realty-home-section hvn-realty-home-footer-cta" aria-labelledby="hvn-realty-footer-cta-title">
	<div class="hvn-theme-container">
		<div class="hvn-realty-home-footer-cta__inner">
			<h2 id="hvn-realty-footer-cta-title" class="hvn-realty-home-footer-cta__title">
				<?php echo esc_html( $text ); ?>
			</h2>
			<a class="hvn-realty-home-btn hvn-realty-home-btn--primary" href="<?php echo esc_url( $search_url ); ?>">
				<?php esc_html_e( 'Search Properties', 'havenlytics-realty' ); ?>
			</a>
		</div>
	</div>
</section>
