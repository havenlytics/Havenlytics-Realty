<?php
/**
 * Homepage: Explore by Property Type.
 *
 * @package Havenlytics_Realty
 */

$terms       = function_exists( 'hvn_realty_get_home_property_type_terms' ) ? hvn_realty_get_home_property_type_terms() : array();
$columns     = function_exists( 'hvn_realty_get_home_property_types_columns' ) ? hvn_realty_get_home_property_types_columns() : 4;
$show_counts = function_exists( 'hvn_realty_show_home_property_type_counts' ) && hvn_realty_show_home_property_type_counts();
$search_url  = function_exists( 'hvn_realty_get_property_search_url' ) ? hvn_realty_get_property_search_url() : home_url( '/' );

$title = function_exists( 'hvn_realty_get_home_section_title' )
	? hvn_realty_get_home_section_title( 'property_types', __( 'Explore by Property Type', 'havenlytics-realty' ) )
	: __( 'Explore by Property Type', 'havenlytics-realty' );

$subtitle = function_exists( 'hvn_realty_get_home_section_subtitle' )
	? hvn_realty_get_home_section_subtitle( 'property_types', __( 'Browse listings by home style and category.', 'havenlytics-realty' ) )
	: __( 'Browse listings by home style and category.', 'havenlytics-realty' );

if ( empty( $terms ) ) {
	return;
}
?>
<section
	id="hvn-realty-section-property-types"
	class="hvn-realty-section hvn-realty-section--property-types"
	aria-labelledby="hvn-realty-property-types-title"
>
	<div class="hvn-realty-container">
		<?php
		if ( function_exists( 'hvn_realty_home_section_heading' ) ) {
			hvn_realty_home_section_heading(
				$title,
				$subtitle,
				'',
				'hvn-realty-property-types-title'
			);
		}
		?>

		<ul class="hvn-realty-type-cards" data-cols="<?php echo esc_attr( (string) $columns ); ?>">
			<?php foreach ( $terms as $term ) : ?>
				<?php
				if ( ! $term instanceof WP_Term ) {
					continue;
				}

				$url   = get_term_link( $term );
				$url   = is_wp_error( $url ) ? $search_url : $url;
				$count = absint( $term->count );
				$media = function_exists( 'hvn_realty_get_home_property_type_card_media' )
					? hvn_realty_get_home_property_type_card_media( $term )
					: array( 'type' => 'none' );
				?>
				<li class="hvn-realty-type-cards__item">
					<a class="hvn-realty-type-cards__card" href="<?php echo esc_url( $url ); ?>">
						<?php if ( $show_counts ) : ?>
							<span class="hvn-realty-type-cards__count">
								<?php
								printf(
									/* translators: %d: number of properties */
									esc_html( _n( '%d property', '%d properties', $count, 'havenlytics-realty' ) ),
									$count
								);
								?>
							</span>
						<?php endif; ?>

						<div class="hvn-realty-type-cards__media" aria-hidden="true">
							<?php if ( 'image' === ( $media['type'] ?? '' ) && ! empty( $media['url'] ) ) : ?>
								<img
									class="hvn-realty-type-cards__image"
									src="<?php echo esc_url( $media['url'] ); ?>"
									alt=""
									loading="lazy"
									decoding="async"
								/>
							<?php elseif ( 'icon' === ( $media['type'] ?? '' ) && ! empty( $media['class'] ) ) : ?>
								<span class="hvn-realty-type-cards__icon <?php echo esc_attr( $media['class'] ); ?>"></span>
							<?php else : ?>
								<span class="hvn-realty-type-cards__icon fas fa-building"></span>
							<?php endif; ?>
						</div>

						<div class="hvn-realty-type-cards__body">
							<h3 class="hvn-realty-type-cards__title"><?php echo esc_html( $term->name ); ?></h3>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
