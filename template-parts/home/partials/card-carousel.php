<?php
/**
 * Homepage card carousel shell (agents / agencies).
 *
 * @package Havenlytics_Realty
 *
 * @var array<string, mixed> $args Template args.
 */

$args = isset( $args ) && is_array( $args ) ? $args : array();

$type            = isset( $args['type'] ) ? sanitize_key( (string) $args['type'] ) : 'agents';
$shortcode       = isset( $args['shortcode'] ) ? sanitize_key( (string) $args['shortcode'] ) : '';
$shortcode_atts  = isset( $args['shortcode_atts'] ) && is_array( $args['shortcode_atts'] ) ? $args['shortcode_atts'] : array();
$carousel_id     = isset( $args['carousel_id'] ) ? sanitize_html_class( (string) $args['carousel_id'] ) : 'hvn-realty-' . $type . '-carousel';

if ( '' === $shortcode ) {
	return;
}

$nav_label = 'agents' === $type
	? __( 'Agents carousel', 'havenlytics-realty' )
	: __( 'Agencies carousel', 'havenlytics-realty' );
?>
<div
	class="hvn-realty-card-carousel"
	id="<?php echo esc_attr( $carousel_id ); ?>"
	data-hvn-realty-card-carousel
	data-carousel-type="<?php echo esc_attr( $type ); ?>"
>
	<button
		type="button"
		class="hvn-realty-card-carousel__nav hvn-realty-card-carousel__nav--prev"
		data-card-carousel-prev
		aria-label="<?php echo esc_attr( 'agents' === $type ? __( 'Previous agents', 'havenlytics-realty' ) : __( 'Previous agencies', 'havenlytics-realty' ) ); ?>"
		disabled
	>
		<span aria-hidden="true">&lsaquo;</span>
	</button>

	<div class="hvn-realty-card-carousel__viewport" aria-roledescription="carousel" aria-label="<?php echo esc_attr( $nav_label ); ?>">
		<div class="hvn-realty-card-carousel__source">
			<?php
			echo hvn_realty_render_shortcode( $shortcode, $shortcode_atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>

	<button
		type="button"
		class="hvn-realty-card-carousel__nav hvn-realty-card-carousel__nav--next"
		data-card-carousel-next
		aria-label="<?php echo esc_attr( 'agents' === $type ? __( 'Next agents', 'havenlytics-realty' ) : __( 'Next agencies', 'havenlytics-realty' ) ); ?>"
	>
		<span aria-hidden="true">&rsaquo;</span>
	</button>
</div>
