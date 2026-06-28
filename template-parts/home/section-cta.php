<?php
/**
 * Homepage 2.0.0 — Call to action banner.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_title    = (string) get_theme_mod( 'hvn_realty_home_cta_title', __( 'Ready to see what your home is really worth?', 'havenlytics-realty' ) );
$hvn_subtitle = (string) get_theme_mod( 'hvn_realty_home_cta_subtitle', __( 'Get a free, data-backed valuation from a local Havenlytics agent within 24 hours.', 'havenlytics-realty' ) );
$hvn_p_label  = (string) get_theme_mod( 'hvn_realty_home_cta_primary_label', __( 'Get a Free Valuation', 'havenlytics-realty' ) );
$hvn_p_url    = (string) get_theme_mod( 'hvn_realty_home_cta_primary_url', '#hvn-theme-home-footer' );
$hvn_s_label  = (string) get_theme_mod( 'hvn_realty_home_cta_secondary_label', __( 'Talk to an Agent', 'havenlytics-realty' ) );
$hvn_s_url    = (string) get_theme_mod( 'hvn_realty_home_cta_secondary_url', '#hvn-theme-home-agents' );

if ( '' === $hvn_title && '' === $hvn_subtitle ) {
	return;
}
?>
<section class="hvn-theme-home-section" aria-labelledby="hvn-theme-home-cta-title">
	<div class="hvn-theme-home-cta hvn-theme-home-reveal">
		<div class="hvn-theme-home-cta__copy">
			<?php if ( $hvn_title ) : ?>
				<h2 id="hvn-theme-home-cta-title"><?php echo esc_html( $hvn_title ); ?></h2>
			<?php endif; ?>
			<?php if ( $hvn_subtitle ) : ?>
				<p><?php echo esc_html( $hvn_subtitle ); ?></p>
			<?php endif; ?>
		</div>
		<div class="hvn-theme-home-cta__actions">
			<?php if ( $hvn_p_label ) : ?>
				<a href="<?php echo esc_url( $hvn_p_url ); ?>" class="hvn-theme-home-btn hvn-theme-home-btn--gold"><?php echo esc_html( $hvn_p_label ); ?></a>
			<?php endif; ?>
			<?php if ( $hvn_s_label ) : ?>
				<a href="<?php echo esc_url( $hvn_s_url ); ?>" class="hvn-theme-home-btn hvn-theme-home-btn--ghost"><?php echo esc_html( $hvn_s_label ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>
