<?php
/**
 * Homepage 3.0 — Why choose us.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_why_items = function_exists( 'hvn_realty_get_home_why_items' ) ? hvn_realty_get_home_why_items() : array();
if ( empty( $hvn_why_items ) || ! is_array( $hvn_why_items ) ) {
	return;
}
?>
<section id="hvn-theme-home-why" aria-labelledby="hvn-theme-home-why-title">
	<div class="hvn-realty-why-section">
		<div class="hvn-realty-wrap">
			<div class="hvn-realty-section-head hvn-realty-reveal">
				<?php
				if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
					hvn_realty_render_home_eyebrow( (string) get_theme_mod( 'hvn_realty_home_why_eyebrow', __( 'Why Havenlytics', 'havenlytics-realty' ) ) );
				}
				?>
				<h2 id="hvn-theme-home-why-title"><?php echo esc_html( get_theme_mod( 'hvn_realty_home_why_title', __( 'Real estate, grounded in evidence', 'havenlytics-realty' ) ) ); ?></h2>
				<p><?php echo esc_html( get_theme_mod( 'hvn_realty_home_why_subtitle', __( 'We combine licensed local expertise with continuously updated market data, so you always know what a home is actually worth.', 'havenlytics-realty' ) ) ); ?></p>
			</div>
			<div class="hvn-realty-why-grid">
				<?php
				$hvn_why_i = 0;
				foreach ( $hvn_why_items as $hvn_item ) :
					$hvn_title = isset( $hvn_item['title'] ) ? (string) $hvn_item['title'] : '';
					$hvn_text  = isset( $hvn_item['text'] ) ? (string) $hvn_item['text'] : '';
					if ( '' === $hvn_title && '' === $hvn_text ) {
						continue;
					}
					++$hvn_why_i;
					?>
					<div class="hvn-realty-why-card hvn-realty-reveal">
						<div class="hvn-realty-why-card-num"><?php echo esc_html( str_pad( (string) $hvn_why_i, 2, '0', STR_PAD_LEFT ) ); ?></div>
						<?php if ( $hvn_title ) : ?>
							<h3><?php echo esc_html( $hvn_title ); ?></h3>
						<?php endif; ?>
						<?php if ( $hvn_text ) : ?>
							<p><?php echo esc_html( $hvn_text ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
