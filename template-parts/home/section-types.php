<?php
/**
 * Homepage 3.0 — Browse by property type.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_get_home_property_type_terms' ) ) {
	return;
}

// Intentional fixed layout: four type cards (no count Customizer control in this release).
$hvn_terms = hvn_realty_get_home_property_type_terms( 4 );
if ( empty( $hvn_terms ) && ! is_customize_preview() ) {
	return;
}
if ( ! is_array( $hvn_terms ) ) {
	$hvn_terms = array();
}

$hvn_type_icon_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>';
?>
<section id="hvn-theme-home-types" aria-labelledby="hvn-theme-home-types-title">
	<div class="hvn-realty-wrap">
		<div class="hvn-realty-section-head hvn-realty-reveal">
			<?php
			if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
				hvn_realty_render_home_eyebrow( hvn_realty_get_home_section_subtitle( 'property_types', __( 'Browse by Type', 'havenlytics-realty' ) ) );
			}
			?>
			<h2 id="hvn-theme-home-types-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'property_types', __( 'Whatever shape home takes for you', 'havenlytics-realty' ) ) ); ?></h2>
		</div>
		<div class="hvn-realty-type-grid">
			<?php foreach ( $hvn_terms as $hvn_term ) : ?>
				<?php
				if ( ! $hvn_term instanceof WP_Term ) {
					continue;
				}
				$hvn_link  = get_term_link( $hvn_term );
				$hvn_link  = is_wp_error( $hvn_link ) ? '#' : $hvn_link;
				$hvn_image = function_exists( 'hvn_realty_get_term_image_url' ) ? hvn_realty_get_term_image_url( $hvn_term->term_id, 'medium_large' ) : '';
				?>
				<a class="hvn-realty-type-card hvn-realty-reveal" href="<?php echo esc_url( $hvn_link ); ?>">
					<?php if ( $hvn_image ) : ?>
						<img src="<?php echo esc_url( $hvn_image ); ?>" alt="<?php echo esc_attr( $hvn_term->name ); ?>" loading="lazy" decoding="async">
					<?php endif; ?>
					<div class="hvn-realty-type-inner">
						<div class="hvn-realty-type-icon"><?php echo $hvn_type_icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<h3><?php echo esc_html( $hvn_term->name ); ?></h3>
						<span>
							<?php
							printf(
								/* translators: %s: number of listings. */
								esc_html( _n( '%s home', '%s homes', (int) $hvn_term->count, 'havenlytics-realty' ) ),
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
