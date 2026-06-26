<?php
/**
 * Homepage: Property taxonomies grid (configurable Havenlytics source).
 *
 * @package Havenlytics_Realty
 */

$source_key  = function_exists( 'hvn_realty_get_home_taxonomy_source' ) ? hvn_realty_get_home_taxonomy_source() : 'locations';
$sources     = function_exists( 'hvn_realty_get_home_taxonomy_sources' ) ? hvn_realty_get_home_taxonomy_sources() : array();
$source      = $sources[ $source_key ] ?? array();
$limit       = function_exists( 'hvn_realty_get_home_taxonomies_count' ) ? hvn_realty_get_home_taxonomies_count() : 8;
$columns     = function_exists( 'hvn_realty_get_home_taxonomies_columns' ) ? hvn_realty_get_home_taxonomies_columns() : 4;
$terms       = function_exists( 'hvn_realty_get_home_taxonomy_terms' ) ? hvn_realty_get_home_taxonomy_terms( $limit ) : array();
$search_url  = function_exists( 'hvn_realty_get_property_search_url' ) ? hvn_realty_get_property_search_url() : home_url( '/' );
$show_counts = function_exists( 'hvn_realty_show_home_taxonomy_counts' ) && hvn_realty_show_home_taxonomy_counts();

$title = function_exists( 'hvn_realty_get_home_taxonomies_title' )
	? hvn_realty_get_home_taxonomies_title()
	: __( 'Property Locations', 'havenlytics-realty' );

$subtitle = function_exists( 'hvn_realty_get_home_taxonomies_subtitle' )
	? hvn_realty_get_home_taxonomies_subtitle()
	: __( 'Explore listings by city and region.', 'havenlytics-realty' );

$link_label = $source['link_label'] ?? __( 'Browse listings', 'havenlytics-realty' );
$source_label = $source['label'] ?? __( 'Properties', 'havenlytics-realty' );
?>
<section
	id="hvn-realty-section-taxonomies"
	class="hvn-realty-section hvn-realty-section--taxonomies"
	aria-labelledby="hvn-realty-taxonomies-title"
	data-taxonomy-source="<?php echo esc_attr( $source_key ); ?>"
>
	<div class="hvn-realty-container">
		<?php
		if ( function_exists( 'hvn_realty_home_section_heading' ) ) {
			hvn_realty_home_section_heading(
				$title,
				$subtitle,
				'',
				'hvn-realty-taxonomies-title'
			);
		}
		?>

		<?php if ( empty( $terms ) ) : ?>
			<p class="hvn-realty-taxonomies__empty">
				<?php
				printf(
					/* translators: %s: taxonomy source label */
					esc_html__( 'No %s are available yet.', 'havenlytics-realty' ),
					esc_html( strtolower( $source_label ) )
				);
				?>
			</p>
		<?php else : ?>
			<ul
				class="hvn-realty-taxonomies__grid"
				data-cols="<?php echo esc_attr( (string) $columns ); ?>"
			>
				<?php foreach ( $terms as $term ) : ?>
					<?php
					if ( ! $term instanceof WP_Term ) {
						continue;
					}

					$url   = get_term_link( $term );
					$url   = is_wp_error( $url ) ? $search_url : $url;
					$count = absint( $term->count );
					$media = function_exists( 'hvn_realty_get_home_taxonomy_card_media' )
						? hvn_realty_get_home_taxonomy_card_media( $term, $source_key )
						: array( 'type' => 'none' );
					$card_class = 'hvn-realty-taxonomies__card';
					if ( 'none' === ( $media['type'] ?? 'none' ) ) {
						$card_class .= ' hvn-realty-taxonomies__card--text';
					}
					?>
					<li class="hvn-realty-taxonomies__item">
						<a class="<?php echo esc_attr( $card_class ); ?>" href="<?php echo esc_url( $url ); ?>">
							<?php if ( 'image' === ( $media['type'] ?? '' ) && ! empty( $media['url'] ) ) : ?>
								<span class="hvn-realty-taxonomies__media hvn-realty-taxonomies__media--image">
									<img
										class="hvn-realty-taxonomies__image"
										src="<?php echo esc_url( $media['url'] ); ?>"
										alt=""
										width="480"
										height="360"
										loading="lazy"
										decoding="async"
									/>
								</span>
							<?php elseif ( 'icon' === ( $media['type'] ?? '' ) && ! empty( $media['class'] ) ) : ?>
								<span class="hvn-realty-taxonomies__media hvn-realty-taxonomies__media--icon" aria-hidden="true">
									<i class="<?php echo esc_attr( $media['class'] ); ?>"></i>
								</span>
							<?php endif; ?>

							<span class="hvn-realty-taxonomies__body">
								<h3 class="hvn-realty-taxonomies__title"><?php echo esc_html( $term->name ); ?></h3>
								<?php if ( $show_counts ) : ?>
									<span class="hvn-realty-taxonomies__count">
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: property count */
												_n( '%d listing', '%d listings', $count, 'havenlytics-realty' ),
												$count
											)
										);
										?>
									</span>
								<?php endif; ?>
								<span class="hvn-realty-taxonomies__link-text"><?php echo esc_html( $link_label ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
