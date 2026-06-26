<?php
/**
 * Homepage: Featured properties — Havenlytics similar-style carousel.
 *
 * @package Havenlytics_Realty
 */

$count = absint( get_theme_mod( 'hvn_realty_home_featured_count', 12 ) );
$count = max( 4, min( 24, $count ) );

$title    = hvn_realty_get_home_section_title( 'featured', __( 'Featured Properties', 'havenlytics-realty' ) );
$subtitle = hvn_realty_get_home_section_subtitle( 'featured', '' );
?>
<section id="hvn-realty-section-featured" class="hvn-realty-section hvn-realty-section--featured" aria-labelledby="hvn-realty-featured-title">
	<div class="hvn-realty-container">
		<header class="hvn-realty-section__header hvn-realty-section__header--split">
			<div class="hvn-realty-section__header-copy">
				<h2 class="hvn-realty-section__title" id="hvn-realty-featured-title">
					<?php echo esc_html( $title ); ?>
				</h2>
				<?php if ( $subtitle ) : ?>
					<p class="hvn-realty-section__subtitle hvn-realty-section__subtitle--inline"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</div>
			<nav class="hvn-realty-carousel-nav" aria-label="<?php esc_attr_e( 'Featured properties carousel', 'havenlytics-realty' ); ?>">
				<button type="button" class="hvn-realty-carousel-btn" data-carousel-prev aria-label="<?php esc_attr_e( 'Previous properties', 'havenlytics-realty' ); ?>" disabled>
					<i class="fas fa-chevron-left" aria-hidden="true"></i>
				</button>
				<button type="button" class="hvn-realty-carousel-btn" data-carousel-next aria-label="<?php esc_attr_e( 'Next properties', 'havenlytics-realty' ); ?>">
					<i class="fas fa-chevron-right" aria-hidden="true"></i>
				</button>
			</nav>
		</header>

		<div class="hvn-realty-section__body">
			<?php
			hvn_realty_render_similar_property_carousel(
				array(
					'carousel_id'   => 'hvn-realty-featured-carousel',
					'query_args'    => array(
						'posts_per_page' => $count,
						'featured_only'  => true,
					),
					'empty_message' => __( 'No featured properties at the moment.', 'havenlytics-realty' ),
				)
			);
			?>
		</div>
	</div>
</section>
