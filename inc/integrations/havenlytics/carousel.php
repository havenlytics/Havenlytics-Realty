<?php
/**
 * Havenlytics-style property carousel helpers for the theme homepage.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Property department terms for homepage tabs.
 *
 * @return WP_Term[]
 */
function hvn_realty_get_property_departments() {
	if ( ! taxonomy_exists( 'hvnly_prop_depts' ) ) {
		return array();
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'hvnly_prop_depts',
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Query args for a homepage property carousel.
 *
 * @param array<string, mixed> $args Query overrides.
 * @return array<string, mixed>
 */
function hvn_realty_get_property_carousel_query_args( $args = array() ) {
	$defaults = array(
		'post_type'      => 'hvnly_property',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$query_args = wp_parse_args( $args, $defaults );

	if ( ! empty( $query_args['featured_only'] ) ) {
		$query_args['meta_key']   = '_hvnly_property_featured';
		$query_args['meta_value'] = '1';
		unset( $query_args['featured_only'] );
	}

	return $query_args;
}

/**
 * Render one similar-style property carousel slide.
 *
 * @param int $property_id Property post ID.
 * @param int $slide_index Zero-based slide index.
 * @return void
 */
function hvn_realty_render_similar_property_slide( $property_id, $slide_index = 0 ) {
	$property_id = absint( $property_id );
	if ( $property_id <= 0 ) {
		return;
	}

	$actual_price = get_post_meta( $property_id, '_hvnly_property_price', true );
	$price        = function_exists( 'hvnly_format_price' ) ? hvnly_format_price( $actual_price ) : $actual_price;
	$image_url    = get_the_post_thumbnail_url( $property_id, 'large' );
	$bedrooms     = get_post_meta( $property_id, '_hvnly_property_bedrooms', true );
	$bathrooms    = get_post_meta( $property_id, '_hvnly_property_bathrooms', true );
	$sqft         = get_post_meta( $property_id, '_hvnly_property_sqft', true );
	$is_featured  = get_post_meta( $property_id, '_hvnly_property_featured', true );

	$property_types    = wp_get_post_terms( $property_id, 'hvnly_prop_types', array( 'fields' => 'names' ) );
	$property_status   = wp_get_post_terms( $property_id, 'hvnly_prop_status', array( 'fields' => 'names' ) );
	$property_badges   = wp_get_post_terms( $property_id, 'hvnly_prop_badges', array( 'fields' => 'names' ) );
	$property_features = wp_get_post_terms( $property_id, 'hvnly_prop_features', array( 'fields' => 'names' ) );

	if ( is_wp_error( $property_types ) ) {
		$property_types = array();
	}
	if ( is_wp_error( $property_status ) ) {
		$property_status = array();
	}
	if ( is_wp_error( $property_badges ) ) {
		$property_badges = array();
	}
	if ( is_wp_error( $property_features ) ) {
		$property_features = array();
	}

	$is_featured_property = ( '1' === (string) $is_featured || 1 === $is_featured );
	$permalink            = get_permalink( $property_id );
	$title                = get_the_title( $property_id );
	?>
	<div class="hvnly-property-single__carousel-slide">
		<div class="hvnly-property-single__similar-card">
			<div class="hvnly-property-single__similar-image" data-index="<?php echo esc_attr( (string) $slide_index ); ?>">
				<a href="<?php echo esc_url( $permalink ); ?>">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>"
							alt="<?php echo esc_attr( $title ); ?>"
							data-src="<?php echo esc_url( get_the_post_thumbnail_url( $property_id, 'full' ) ); ?>"
							data-alt="<?php echo esc_attr( $title ); ?>">
					<?php else : ?>
						<div class="hvnly-property-single__similar-image-placeholder">
							<i class="fas fa-home" aria-hidden="true"></i>
						</div>
					<?php endif; ?>

					<div class="hvnly-property-single__image-overlay">
						<?php if ( ! empty( $property_types ) ) : ?>
							<div class="hvnly-property-single__overlay-item">
								<i class="fas fa-tag" aria-hidden="true"></i>
								<span><?php echo esc_html( implode( ', ', array_slice( $property_types, 0, 2 ) ) ); ?></span>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $property_status ) ) : ?>
							<div class="hvnly-property-single__overlay-item">
								<i class="fas fa-info-circle" aria-hidden="true"></i>
								<span><?php echo esc_html( implode( ', ', array_slice( $property_status, 0, 2 ) ) ); ?></span>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $property_badges ) ) : ?>
							<div class="hvnly-property-single__overlay-item">
								<i class="fas fa-star" aria-hidden="true"></i>
								<span><?php echo esc_html( implode( ', ', array_slice( $property_badges, 0, 2 ) ) ); ?></span>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $property_features ) ) : ?>
							<div class="hvnly-property-single__overlay-item">
								<i class="fas fa-check-circle" aria-hidden="true"></i>
								<span><?php echo esc_html( implode( ', ', array_slice( $property_features, 0, 2 ) ) ); ?></span>
								<?php if ( count( $property_features ) > 2 ) : ?>
									<span class="hvnly-property-single__overlay-more">+<?php echo esc_html( (string) ( count( $property_features ) - 2 ) ); ?> <?php esc_html_e( 'more', 'havenlytics-realty' ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $is_featured_property ) : ?>
						<span class="hvnly-property-single__similar-badge hvnly-property-single__similar-badge--featured">
							<?php esc_html_e( 'Featured', 'havenlytics-realty' ); ?>
						</span>
					<?php endif; ?>
				</a>
			</div>

			<div class="hvnly-property-single__similar-content">
				<div class="hvnly-property-single__similar-price"><?php echo wp_kses_post( (string) $price ); ?></div>

				<h3 class="hvnly-property-single__similar-title">
					<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
				</h3>

				<?php if ( $bedrooms || $bathrooms || $sqft ) : ?>
					<div class="hvnly-property-single__similar-meta">
						<?php if ( $bedrooms ) : ?>
							<div class="hvnly-property-single__similar-feature">
								<i class="fas fa-bed" aria-hidden="true"></i>
								<span><?php echo esc_html( (string) $bedrooms ); ?> <?php esc_html_e( 'Beds', 'havenlytics-realty' ); ?></span>
							</div>
						<?php endif; ?>

						<?php if ( $bathrooms ) : ?>
							<div class="hvnly-property-single__similar-feature">
								<i class="fas fa-bath" aria-hidden="true"></i>
								<span><?php echo esc_html( (string) $bathrooms ); ?> <?php esc_html_e( 'Baths', 'havenlytics-realty' ); ?></span>
							</div>
						<?php endif; ?>

						<?php if ( $sqft ) : ?>
							<div class="hvnly-property-single__similar-feature">
								<i class="fas fa-vector-square" aria-hidden="true"></i>
								<span><?php echo esc_html( (string) $sqft ); ?> <?php esc_html_e( 'SqFt', 'havenlytics-realty' ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<a href="<?php echo esc_url( $permalink ); ?>" class="hvnly-property-single__btn hvnly-property-single__btn--full">
					<i class="fas fa-eye" aria-hidden="true"></i>
					<?php esc_html_e( 'View Details', 'havenlytics-realty' ); ?>
				</a>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render a Havenlytics similar-style property carousel.
 *
 * @param array<string, mixed> $args Carousel args.
 * @return void
 */
function hvn_realty_render_similar_property_carousel( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'carousel_id'    => 'hvn-realty-featured-carousel',
			'query_args'     => array(),
			'empty_message'  => __( 'No properties found at the moment.', 'havenlytics-realty' ),
		)
	);

	$query = new WP_Query( hvn_realty_get_property_carousel_query_args( $args['query_args'] ) );
	?>
	<div class="hvn-realty-featured-carousel hvn-realty-similar-carousel" data-hvn-realty-similar-carousel id="<?php echo esc_attr( $args['carousel_id'] ); ?>">
		<div class="hvnly-property-single__carousel-container">
			<div class="hvnly-property-single__carousel-track" data-carousel-track>
				<?php
				if ( $query->have_posts() ) :
					$slide_index = 0;
					while ( $query->have_posts() ) :
						$query->the_post();
						hvn_realty_render_similar_property_slide( get_the_ID(), $slide_index );
						++$slide_index;
					endwhile;
				else :
					?>
					<div class="hvnly-property-single__carousel-slide">
						<div class="hvnly-property-single__similar-card hvnly-property-single__similar-card--empty">
							<div class="hvnly-property-single__similar-content">
								<i class="fas fa-home" aria-hidden="true"></i>
								<h3><?php esc_html_e( 'No Properties', 'havenlytics-realty' ); ?></h3>
								<p><?php echo esc_html( $args['empty_message'] ); ?></p>
							</div>
						</div>
					</div>
					<?php
				endif;
				wp_reset_postdata();
				?>
			</div>
		</div>
		<div class="hvnly-property-single__carousel-dots" data-carousel-dots role="tablist" aria-label="<?php esc_attr_e( 'Property carousel navigation', 'havenlytics-realty' ); ?>"></div>
	</div>
	<?php
}
