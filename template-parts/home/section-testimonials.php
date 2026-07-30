<?php

/**

 * Homepage 3.0 — Testimonials carousel.

 *

 * Each testimonial is one flex slide. CSS shows 1 on mobile / 3 on desktop.

 * The track must never expand document width (see home-v3.css overflow + flex-basis).

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



$hvn_show_stars = (bool) get_theme_mod( 'hvn_realty_home_show_testimonial_stars', true );

$hvn_autoplay   = function_exists( 'hvn_realty_home_testimonials_autoplay' )

	? hvn_realty_home_testimonials_autoplay()

	: (bool) get_theme_mod( 'hvn_realty_home_testimonials_autoplay', false );

$hvn_speed      = function_exists( 'hvn_realty_get_home_testimonials_speed' )

	? hvn_realty_get_home_testimonials_speed()

	: max( 2000, min( 15000, absint( get_theme_mod( 'hvn_realty_home_testimonials_speed', 5000 ) ) ) );

$hvn_count      = count( $hvn_items );

?>

<section style="background:var(--hvn-realty-cream-200);" id="hvn-theme-home-testimonials" aria-labelledby="hvn-theme-home-testimonials-title">

	<div class="hvn-realty-wrap">

		<div class="hvn-realty-section-head hvn-realty-reveal" style="margin:0 auto 56px; text-align:center;">

			<div class="hvn-realty-eyebrow" style="justify-content:center;">

				<svg class="hvn-realty-eyebrow-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M12 2l3 7h7l-5.5 4.5L18.5 21 12 16.5 5.5 21l2-7.5L2 9h7z"/></svg>

				<span class="hvn-realty-eyebrow-rule" aria-hidden="true"></span><span class="hvn-realty-eyebrow-text"><?php echo esc_html( hvn_realty_get_home_section_subtitle( 'testimonials', __( 'Client Stories', 'havenlytics-realty' ) ) ); ?></span><span class="hvn-realty-eyebrow-rule" aria-hidden="true"></span>

			</div>

			<h2 id="hvn-theme-home-testimonials-title" style="margin:0 auto;"><?php echo esc_html( hvn_realty_get_home_section_title( 'testimonials', __( 'Trusted by buyers and sellers alike', 'havenlytics-realty' ) ) ); ?></h2>

		</div>

		<div class="hvn-realty-testi-wrap" role="region" aria-roledescription="<?php esc_attr_e( 'carousel', 'havenlytics-realty' ); ?>" aria-labelledby="hvn-theme-home-testimonials-title">

			<div

				class="hvn-realty-testi-track"

				id="hvnRealtyTestiTrack"

				data-hvn-theme-testimonial-track="hvn-theme-home-testimonial-track"

				data-autoplay="<?php echo $hvn_autoplay ? '1' : '0'; ?>"

				data-speed="<?php echo esc_attr( (string) $hvn_speed ); ?>"

			>

				<?php foreach ( $hvn_items as $hvn_item ) : ?>

					<?php

					$hvn_name = isset( $hvn_item['name'] ) ? (string) $hvn_item['name'] : '';

					$hvn_role = '';

					if ( ! empty( $hvn_item['position'] ) ) {

						$hvn_role = (string) $hvn_item['position'];

					} elseif ( ! empty( $hvn_item['location'] ) ) {

						$hvn_role = (string) $hvn_item['location'];

					}

					$hvn_text   = isset( $hvn_item['text'] ) ? (string) $hvn_item['text'] : '';

					$hvn_rating = isset( $hvn_item['rating'] ) ? (int) $hvn_item['rating'] : 5;

					$hvn_rating = max( 1, min( 5, $hvn_rating ) );

					$hvn_avatar = function_exists( 'hvn_realty_get_testimonial_avatar_url' ) ? hvn_realty_get_testimonial_avatar_url( $hvn_item ) : '';

					?>

					<div class="hvn-realty-testi-slide">

						<div class="hvn-realty-testi-card">

							<?php if ( $hvn_show_stars ) : ?>

								<div class="hvn-realty-stars" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating out of 5. */ __( '%d out of 5 stars', 'havenlytics-realty' ), $hvn_rating ) ); ?>"><?php echo esc_html( str_repeat( '★', $hvn_rating ) ); ?></div>

							<?php endif; ?>

							<?php if ( $hvn_text ) : ?>

								<p class="hvn-realty-testi-quote"><?php echo esc_html( $hvn_text ); ?></p>

							<?php endif; ?>

							<div class="hvn-realty-testi-who">

								<?php if ( $hvn_avatar ) : ?>

									<img src="<?php echo esc_url( $hvn_avatar ); ?>" alt="<?php echo esc_attr( $hvn_name ); ?>" loading="lazy" decoding="async">

								<?php endif; ?>

								<div>

									<?php if ( $hvn_name ) : ?>

										<h3><?php echo esc_html( $hvn_name ); ?></h3>

									<?php endif; ?>

									<?php if ( $hvn_role ) : ?>

										<span><?php echo esc_html( $hvn_role ); ?></span>

									<?php endif; ?>

								</div>

							</div>

						</div>

					</div>

				<?php endforeach; ?>

			</div>

			<?php if ( $hvn_count > 1 ) : ?>

				<div class="hvn-realty-testi-nav">

					<button type="button" id="hvnRealtyTestiPrev" aria-label="<?php esc_attr_e( 'Previous', 'havenlytics-realty' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>

					<button type="button" id="hvnRealtyTestiNext" aria-label="<?php esc_attr_e( 'Next', 'havenlytics-realty' ); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>

				</div>

			<?php endif; ?>

		</div>

	</div>

</section>


