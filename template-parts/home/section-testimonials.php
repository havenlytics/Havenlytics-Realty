<?php
/**
 * Homepage 2.0.0 — Testimonials slider.
 *
 * Reuses the theme/plugin testimonial data source; prototype markup only.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_get_home_testimonials' ) ) {
	return;
}

$hvn_items = hvn_realty_get_home_testimonials();
if ( empty( $hvn_items ) || ! is_array( $hvn_items ) ) {
	return;
}

$hvn_star = '<svg width="14" height="14" viewBox="0 0 14 14"><path d="M7 1L8.6 4.9L13 5.3L9.7 8L10.7 12.3L7 10L3.3 12.3L4.3 8L1 5.3L5.4 4.9Z"/></svg>';
?>
<section class="hvn-theme-home-section hvn-theme-home-testimonials" id="hvn-theme-home-testimonials" aria-labelledby="hvn-theme-home-testimonials-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-head hvn-theme-home-head--center hvn-theme-home-reveal">
			<span class="hvn-theme-home-eyebrow hvn-theme-home-eyebrow--center"><?php echo esc_html( hvn_realty_get_home_section_subtitle( 'testimonials', __( 'Client Stories', 'havenlytics-realty' ) ) ); ?></span>
			<h2 id="hvn-theme-home-testimonials-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'testimonials', __( 'Trusted by buyers and sellers alike', 'havenlytics-realty' ) ) ); ?></h2>
		</div>
		<div class="hvn-theme-home-testimonial-wrap">
			<div class="hvn-theme-home-testimonial-track" id="hvn-theme-home-testimonial-track">
				<?php
				foreach ( $hvn_items as $hvn_item ) :
					$hvn_name   = isset( $hvn_item['name'] ) ? (string) $hvn_item['name'] : '';
					$hvn_role   = '';
					if ( ! empty( $hvn_item['position'] ) ) {
						$hvn_role = (string) $hvn_item['position'];
					} elseif ( ! empty( $hvn_item['location'] ) ) {
						$hvn_role = (string) $hvn_item['location'];
					}
					$hvn_text   = isset( $hvn_item['text'] ) ? (string) $hvn_item['text'] : '';
					$hvn_rating = isset( $hvn_item['rating'] ) ? (int) $hvn_item['rating'] : 5;
					$hvn_rating = max( 1, min( 5, $hvn_rating ) );
					$hvn_avatar = function_exists( 'hvn_realty_get_testimonial_avatar_url' ) ? hvn_realty_get_testimonial_avatar_url( $hvn_item ) : '';
					$hvn_init   = function_exists( 'hvn_realty_get_testimonial_avatar_initial' ) ? hvn_realty_get_testimonial_avatar_initial( $hvn_name ) : '';
					?>
					<figure class="hvn-theme-home-testimonial-card">
						<div class="hvn-theme-home-testimonial-stars" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating out of 5. */ __( '%d out of 5 stars', 'havenlytics-realty' ), $hvn_rating ) ); ?>">
							<?php echo str_repeat( $hvn_star, $hvn_rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<?php if ( $hvn_text ) : ?>
							<p><?php echo esc_html( $hvn_text ); ?></p>
						<?php endif; ?>
						<figcaption class="hvn-theme-home-testimonial-person">
							<?php if ( $hvn_avatar ) : ?>
								<img src="<?php echo esc_url( $hvn_avatar ); ?>" alt="" loading="lazy" decoding="async">
							<?php elseif ( $hvn_init ) : ?>
								<span class="hvn-theme-home-testimonial-avatar-fallback" aria-hidden="true"><?php echo esc_html( $hvn_init ); ?></span>
							<?php endif; ?>
							<span>
								<strong><?php echo esc_html( $hvn_name ); ?></strong>
								<?php if ( $hvn_role ) : ?>
									<small><?php echo esc_html( $hvn_role ); ?></small>
								<?php endif; ?>
							</span>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ( count( $hvn_items ) > 1 ) : ?>
			<div class="hvn-theme-home-testimonial-nav" role="tablist" aria-label="<?php esc_attr_e( 'Testimonial pagination', 'havenlytics-realty' ); ?>">
				<?php foreach ( $hvn_items as $hvn_dot_index => $hvn_dot_item ) : ?>
					<button class="hvn-theme-home-testimonial-dot<?php echo 0 === $hvn_dot_index ? ' hvn-theme-home-active' : ''; ?>" data-hvn-theme-dot="<?php echo esc_attr( (string) $hvn_dot_index ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number. */ __( 'Show testimonial %d', 'havenlytics-realty' ), (int) $hvn_dot_index + 1 ) ); ?>"></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
