<?php
/**
 * Homepage 3.0 — Featured locations.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_get_property_locations' ) ) {
	return;
}

$hvn_terms = hvn_realty_get_property_locations( 4 );
if ( empty( $hvn_terms ) ) {
	return;
}

$hvn_locations_text = (string) get_theme_mod(
	'hvn_realty_home_locations_text',
	__( 'Every market we serve gets walked, photographed, and tracked by a local agent — not just listed.', 'havenlytics-realty' )
);
?>
<section id="hvn-theme-home-locations" aria-labelledby="hvn-theme-home-locations-title">
	<div class="hvn-realty-wrap">
		<div class="hvn-realty-section-head hvn-realty-reveal">
			<?php
			if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
				hvn_realty_render_home_eyebrow( hvn_realty_get_home_section_subtitle( 'locations', __( 'Featured Locations', 'havenlytics-realty' ) ) );
			}
			?>
			<h2 id="hvn-theme-home-locations-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'locations', __( 'Neighborhoods our agents know by heart', 'havenlytics-realty' ) ) ); ?></h2>
			<?php if ( $hvn_locations_text ) : ?>
				<p><?php echo esc_html( $hvn_locations_text ); ?></p>
			<?php endif; ?>
		</div>
		<div class="hvn-realty-loc-grid">
			<?php foreach ( $hvn_terms as $hvn_term ) : ?>
				<?php
				if ( ! $hvn_term instanceof WP_Term ) {
					continue;
				}
				$hvn_link  = get_term_link( $hvn_term );
				$hvn_link  = is_wp_error( $hvn_link ) ? '#' : $hvn_link;
				$hvn_image = function_exists( 'hvn_realty_get_term_image_url' ) ? hvn_realty_get_term_image_url( $hvn_term->term_id, 'large' ) : '';
				?>
				<a class="hvn-realty-loc-card hvn-realty-reveal" href="<?php echo esc_url( $hvn_link ); ?>">
					<?php if ( $hvn_image ) : ?>
						<img src="<?php echo esc_url( $hvn_image ); ?>" alt="<?php echo esc_attr( $hvn_term->name ); ?>" loading="lazy" decoding="async">
					<?php endif; ?>
					<div class="hvn-realty-loc-inner">
						<h3><?php echo esc_html( $hvn_term->name ); ?></h3>
						<div class="hvn-realty-loc-row">
							<span>
								<?php
								printf(
									/* translators: %s: number of listings. */
									esc_html( _n( '%s listing', '%s listings', (int) $hvn_term->count, 'havenlytics-realty' ) ),
									esc_html( number_format_i18n( (int) $hvn_term->count ) )
								);
								?>
							</span>
						</div>
					</div>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
