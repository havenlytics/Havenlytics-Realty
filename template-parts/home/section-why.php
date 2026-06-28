<?php
/**
 * Homepage 2.0.0 — Why choose us / features section.
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
<section class="hvn-theme-home-section hvn-theme-home-why" aria-labelledby="hvn-theme-home-why-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-head hvn-theme-home-head--center hvn-theme-home-reveal">
			<span class="hvn-theme-home-eyebrow hvn-theme-home-eyebrow--center"><?php echo esc_html( get_theme_mod( 'hvn_realty_home_why_eyebrow', __( 'Why Havenlytics', 'havenlytics-realty' ) ) ); ?></span>
			<h2 id="hvn-theme-home-why-title"><?php echo esc_html( get_theme_mod( 'hvn_realty_home_why_title', __( 'Real estate, grounded in evidence', 'havenlytics-realty' ) ) ); ?></h2>
			<p><?php echo esc_html( get_theme_mod( 'hvn_realty_home_why_subtitle', __( 'We combine licensed local expertise with continuously updated market data, so you always know what a home is actually worth.', 'havenlytics-realty' ) ) ); ?></p>
		</div>
		<div class="hvn-theme-home-why__grid">
			<?php
			foreach ( $hvn_why_items as $hvn_item ) :
				$hvn_title = isset( $hvn_item['title'] ) ? (string) $hvn_item['title'] : '';
				$hvn_text  = isset( $hvn_item['text'] ) ? (string) $hvn_item['text'] : '';
				$hvn_url   = isset( $hvn_item['url'] ) ? (string) $hvn_item['url'] : '';
				$hvn_icon  = isset( $hvn_item['icon'] ) ? (string) $hvn_item['icon'] : 'shield';

				if ( '' === $hvn_title && '' === $hvn_text ) {
					continue;
				}

				$hvn_icon_svg = function_exists( 'hvn_realty_get_why_icon_svg' ) ? hvn_realty_get_why_icon_svg( $hvn_icon ) : '';
				$hvn_tag      = '' !== $hvn_url ? 'a' : 'article';
				?>
				<<?php echo esc_attr( $hvn_tag ); ?> class="hvn-theme-home-why__card hvn-theme-home-reveal"<?php echo ( '' !== $hvn_url ) ? ' href="' . esc_url( $hvn_url ) . '"' : ''; ?>>
					<div class="hvn-theme-home-why__icon"><?php echo $hvn_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php if ( $hvn_title ) : ?>
						<h3><?php echo esc_html( $hvn_title ); ?></h3>
					<?php endif; ?>
					<?php if ( $hvn_text ) : ?>
						<p><?php echo esc_html( $hvn_text ); ?></p>
					<?php endif; ?>
				</<?php echo esc_attr( $hvn_tag ); ?>>
			<?php endforeach; ?>
		</div>
	</div>
</section>
