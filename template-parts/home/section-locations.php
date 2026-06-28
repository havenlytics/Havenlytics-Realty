<?php
/**
 * Homepage 2.0.0 — Featured locations (bento grid).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_get_property_locations' ) ) {
	return;
}

$hvn_terms = hvn_realty_get_property_locations( 5 );
if ( empty( $hvn_terms ) ) {
	return;
}
?>
<section class="hvn-theme-home-section hvn-theme-home-locations" id="hvn-theme-home-locations" aria-labelledby="hvn-theme-home-locations-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-head hvn-theme-home-reveal">
			<span class="hvn-theme-home-eyebrow"><?php echo esc_html( hvn_realty_get_home_section_subtitle( 'locations', __( 'Featured Locations', 'havenlytics-realty' ) ) ); ?></span>
			<h2 id="hvn-theme-home-locations-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'locations', __( 'Neighborhoods our agents know by heart', 'havenlytics-realty' ) ) ); ?></h2>
			<p><?php echo esc_html( get_theme_mod( 'hvn_realty_home_locations_text', __( 'Every market we serve gets walked, photographed, and tracked by a local agent — not just listed.', 'havenlytics-realty' ) ) ); ?></p>
		</div>
		<div class="hvn-theme-home-locations__grid">
			<?php
			$hvn_index = 0;
			foreach ( $hvn_terms as $hvn_term ) :
				if ( ! $hvn_term instanceof WP_Term ) {
					continue;
				}
				$hvn_link  = get_term_link( $hvn_term );
				$hvn_link  = is_wp_error( $hvn_link ) ? '#' : $hvn_link;
				$hvn_image = function_exists( 'hvn_realty_get_term_image_url' ) ? hvn_realty_get_term_image_url( $hvn_term->term_id, 'large' ) : '';
				$hvn_big   = ( 0 === $hvn_index ) ? ' hvn-theme-home-location-card--big' : '';
				?>
				<a href="<?php echo esc_url( $hvn_link ); ?>" class="hvn-theme-home-location-card<?php echo esc_attr( $hvn_big ); ?> hvn-theme-home-reveal">
					<?php if ( $hvn_image ) : ?>
						<img src="<?php echo esc_url( $hvn_image ); ?>" alt="" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="hvn-theme-home-location-card__icon" aria-hidden="true">
							<svg width="34" height="34" viewBox="0 0 14 14" fill="none"><path d="M7 13C7 13 12 9 12 5.5C12 3 9.8 1 7 1C4.2 1 2 3 2 5.5C2 9 7 13 7 13Z" stroke="currentColor" stroke-width="1.3"/><circle cx="7" cy="5.5" r="1.6" stroke="currentColor" stroke-width="1.3"/></svg>
						</span>
					<?php endif; ?>
					<div class="hvn-theme-home-location-card__tag">
						<strong><?php echo esc_html( $hvn_term->name ); ?></strong>
						<span>
							<?php
							printf(
								/* translators: %s: number of active listings. */
								esc_html( _n( '%s active listing', '%s active listings', (int) $hvn_term->count, 'havenlytics-realty' ) ),
								esc_html( number_format_i18n( (int) $hvn_term->count ) )
							);
							?>
						</span>
					</div>
				</a>
				<?php
				$hvn_index++;
			endforeach;
			?>
		</div>
	</div>
</section>
