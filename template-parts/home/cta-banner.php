<?php
/**
 * Homepage: Call-to-action banner.
 *
 * @package Havenlytics_Realty
 */

$headline   = hvn_realty_get_home_cta_headline();
$subtext    = hvn_realty_get_home_cta_subtext();
$search_url = hvn_realty_get_property_search_url();
$agents_url = hvn_realty_get_plugin_page_url( 'property_agents' );
$bg_attr    = hvn_realty_get_home_cta_background_attr();
?>
<section id="hvn-realty-section-cta" class="hvn-realty-section hvn-realty-section--cta<?php echo $bg_attr ? ' hvn-realty-section--cta-has-image' : ''; ?>" aria-labelledby="hvn-realty-cta-title"<?php echo $bg_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="hvn-realty-cta__backdrop" aria-hidden="true"></div>
	<div class="hvn-realty-container">
		<div class="hvn-realty-cta__inner">
			<h2 id="hvn-realty-cta-title" class="hvn-realty-cta__title">
				<?php echo esc_html( $headline ); ?>
			</h2>
			<p class="hvn-realty-cta__text">
				<?php echo esc_html( $subtext ); ?>
			</p>
			<div class="hvn-realty-cta__actions">
				<a class="hvn-realty-btn hvn-realty-btn--primary hvn-realty-home-cta__primary" href="<?php echo esc_url( $search_url ); ?>">
					<?php echo esc_html( hvn_realty_get_home_cta_primary_text() ); ?>
				</a>
				<a class="hvn-realty-btn hvn-realty-btn--secondary" href="<?php echo esc_url( $search_url ); ?>">
					<?php esc_html_e( 'Search Properties', 'havenlytics-realty' ); ?>
				</a>
				<a class="hvn-realty-btn hvn-realty-btn--outline hvn-realty-btn--on-dark" href="<?php echo esc_url( $agents_url ); ?>">
					<?php esc_html_e( 'Find an Agent', 'havenlytics-realty' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
