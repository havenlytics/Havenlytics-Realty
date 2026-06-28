<?php
/**
 * Homepage 2.0.0 — Browse by property type.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_get_home_property_type_terms' ) ) {
	return;
}

$hvn_terms = hvn_realty_get_home_property_type_terms( 8 );
if ( empty( $hvn_terms ) ) {
	return;
}
?>
<section class="hvn-theme-home-section hvn-theme-home-types" id="hvn-theme-home-types" aria-labelledby="hvn-theme-home-types-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-head hvn-theme-home-reveal">
			<span class="hvn-theme-home-eyebrow"><?php echo esc_html( hvn_realty_get_home_section_subtitle( 'property_types', __( 'Browse by Type', 'havenlytics-realty' ) ) ); ?></span>
			<h2 id="hvn-theme-home-types-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'property_types', __( 'Whatever shape home takes for you', 'havenlytics-realty' ) ) ); ?></h2>
		</div>
		<div class="hvn-theme-home-types__grid">
			<?php
			foreach ( $hvn_terms as $hvn_term ) :
				if ( ! $hvn_term instanceof WP_Term ) {
					continue;
				}
				$hvn_link  = get_term_link( $hvn_term );
				$hvn_link  = is_wp_error( $hvn_link ) ? '#' : $hvn_link;
				$hvn_image = function_exists( 'hvn_realty_get_term_image_url' ) ? hvn_realty_get_term_image_url( $hvn_term->term_id, 'medium_large' ) : '';
				?>
				<a href="<?php echo esc_url( $hvn_link ); ?>" class="hvn-theme-home-type-card hvn-theme-home-reveal">
					<?php if ( $hvn_image ) : ?>
						<img src="<?php echo esc_url( $hvn_image ); ?>" alt="" loading="lazy" decoding="async">
					<?php else : ?>
						<span class="hvn-theme-home-type-card__icon" aria-hidden="true">
							<svg width="40" height="40" viewBox="0 0 34 34" fill="none"><path d="M5 16L17 6L29 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 14V28H26V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</span>
					<?php endif; ?>
					<div class="hvn-theme-home-type-card__overlay">
						<strong><?php echo esc_html( $hvn_term->name ); ?></strong>
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
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
