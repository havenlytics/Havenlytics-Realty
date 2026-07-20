<?php
/**
 * Homepage 2.0.0 — Featured properties.
 *
 * Rebuilt card markup from the prototype, populated by a real hvnly_property
 * WP_Query. Only renders when the Havenlytics property post type exists.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'hvnly_property' ) ) {
	return;
}

$hvn_count    = (int) get_theme_mod( 'hvn_realty_home_featured_count', 6 );
$hvn_count    = max( 3, min( 12, $hvn_count ) );
$hvn_view_all = function_exists( 'hvn_realty_get_property_search_url' ) ? hvn_realty_get_property_search_url() : '';

$hvn_query = new WP_Query(
	array(
		'post_type'           => 'hvnly_property',
		'posts_per_page'      => $hvn_count,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array(
				'key'     => '_hvnly_property_action_tool_is_featured',
				'value'   => '1',
				'compare' => '=',
			),
			array(
				'key'     => '_hvnly_property_action_tool_is_featured',
				'compare' => 'NOT EXISTS',
			),
		),
	)
);

if ( ! $hvn_query->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<section class="hvn-theme-home-section hvn-theme-home-properties" id="hvn-theme-home-properties" aria-labelledby="hvn-theme-home-properties-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-properties__head">
			<div class="hvn-theme-home-head hvn-theme-home-reveal">
				<span class="hvn-theme-home-eyebrow"><?php echo esc_html( hvn_realty_get_home_section_subtitle( 'featured', __( 'Featured Properties', 'havenlytics-realty' ) ) ); ?></span>
				<h2 id="hvn-theme-home-properties-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'featured', __( 'Handpicked homes worth a closer look', 'havenlytics-realty' ) ) ); ?></h2>
			</div>
			<?php if ( $hvn_view_all ) : ?>
				<a href="<?php echo esc_url( $hvn_view_all ); ?>" class="hvn-theme-home-btn hvn-theme-home-btn--outline"><?php esc_html_e( 'View All Listings', 'havenlytics-realty' ); ?></a>
			<?php endif; ?>
		</div>

		<div class="hvn-theme-home-property-grid">
			<?php
			while ( $hvn_query->have_posts() ) :
				$hvn_query->the_post();
				$hvn_id = get_the_ID();

				$hvn_price_raw = get_post_meta( $hvn_id, '_hvnly_property_price', true );
				$hvn_price     = '';
				if ( '' !== $hvn_price_raw && null !== $hvn_price_raw ) {
					$hvn_price = function_exists( 'hvnly_format_price' ) ? hvnly_format_price( $hvn_price_raw ) : esc_html( number_format_i18n( (float) $hvn_price_raw ) );
				}

				$hvn_beds  = get_post_meta( $hvn_id, '_hvnly_property_bedrooms', true );
				$hvn_baths = get_post_meta( $hvn_id, '_hvnly_property_bathrooms', true );
				$hvn_area  = get_post_meta( $hvn_id, '_hvnly_property_sqft', true );

				$hvn_status_term = function_exists( 'hvnly_get_property_status' ) ? hvnly_get_property_status( $hvn_id ) : false;
				$hvn_status_name = ( $hvn_status_term && ! is_wp_error( $hvn_status_term ) ) ? $hvn_status_term->name : __( 'For Sale', 'havenlytics-realty' );
				$hvn_is_rent     = ( false !== stripos( $hvn_status_name, 'rent' ) );

				$hvn_loc_terms = get_the_terms( $hvn_id, 'hvnly_prop_locations' );
				$hvn_loc_name  = ( $hvn_loc_terms && ! is_wp_error( $hvn_loc_terms ) ) ? $hvn_loc_terms[0]->name : '';
				?>
				<article class="hvn-theme-home-property-card hvn-theme-home-reveal">
					<div class="hvn-theme-home-property-card__media">
						<span class="hvn-theme-home-property-badge<?php echo $hvn_is_rent ? ' hvn-theme-home-property-badge--rent' : ''; ?>"><?php echo esc_html( $hvn_status_name ); ?></span>
						<?php
						if ( function_exists( 'hvn_realty_favorites_available' ) && hvn_realty_favorites_available() ) {
							$hvn_is_favorited = function_exists( 'hvnly_is_property_favorited' ) ? hvnly_is_property_favorited( $hvn_id ) : false;
							$hvn_fav_label    = $hvn_is_favorited
								? __( 'Remove from favorites', 'havenlytics-realty' )
								: __( 'Add to favorites', 'havenlytics-realty' );
							$hvn_toast        = function_exists( 'hvnly_get_favorite_toast_data' )
								? hvnly_get_favorite_toast_data( $hvn_id )
								: array( 'title' => get_the_title( $hvn_id ), 'thumb' => '' );
							?>
							<button
								type="button"
								class="hvnly-property--grid-list--favorite hvn-theme-home-property-fav<?php echo $hvn_is_favorited ? ' is-favorited' : ''; ?>"
								data-hvnly-favorite="1"
								data-property-id="<?php echo esc_attr( (string) $hvn_id ); ?>"
								data-property-title="<?php echo esc_attr( isset( $hvn_toast['title'] ) ? $hvn_toast['title'] : '' ); ?>"
								data-property-thumb="<?php echo esc_url( isset( $hvn_toast['thumb'] ) ? $hvn_toast['thumb'] : '' ); ?>"
								aria-pressed="<?php echo $hvn_is_favorited ? 'true' : 'false'; ?>"
								aria-label="<?php echo esc_attr( $hvn_fav_label ); ?>"
							>
								<i class="<?php echo $hvn_is_favorited ? 'fas' : 'far'; ?> fa-heart" aria-hidden="true"></i>
							</button>
							<?php
						}
						?>
						<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
							<?php else : ?>
								<span class="hvn-theme-home-property-card__media-placeholder" aria-hidden="true">
									<svg width="34" height="34" viewBox="0 0 34 34" fill="none"><path d="M5 16L17 6L29 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 14V28H26V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
								</span>
							<?php endif; ?>
						</a>
					</div>
					<div class="hvn-theme-home-property-body">
						<?php if ( $hvn_price ) : ?>
							<div class="hvn-theme-home-property-price"><?php echo wp_kses_post( $hvn_price ); ?></div>
						<?php endif; ?>
						<h3 class="hvn-theme-home-property-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php if ( $hvn_loc_name ) : ?>
							<div class="hvn-theme-home-property-loc">
								<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 13C7 13 12 9 12 5.5C12 3 9.8 1 7 1C4.2 1 2 3 2 5.5C2 9 7 13 7 13Z" stroke-width="1.3"/><circle cx="7" cy="5.5" r="1.6" stroke-width="1.3"/></svg>
								<?php echo esc_html( $hvn_loc_name ); ?>
							</div>
						<?php endif; ?>
						<?php if ( '' !== $hvn_beds || '' !== $hvn_baths || '' !== $hvn_area ) : ?>
							<div class="hvn-theme-home-property-meta">
								<?php if ( '' !== $hvn_beds ) : ?>
									<div><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2 9V3.5C2 2.7 2.7 2 3.5 2H6C6.8 2 7.5 2.7 7.5 3.5V9M2 9H13V6.5C13 5.7 12.3 5 11.5 5H8.5M2 9V12M13 9V12" stroke-width="1.3"/></svg><?php echo esc_html( sprintf( /* translators: %s: bedroom count. */ _n( '%s Bed', '%s Beds', (int) $hvn_beds, 'havenlytics-realty' ), number_format_i18n( (int) $hvn_beds ) ) ); ?></div>
								<?php endif; ?>
								<?php if ( '' !== $hvn_baths ) : ?>
									<div><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><path d="M2 8H13V10.5C13 11.3 12.3 12 11.5 12H3.5C2.7 12 2 11.3 2 10.5V8Z" stroke-width="1.3"/><path d="M3 8V3.5C3 2.7 3.7 2 4.5 2H5" stroke-width="1.3"/></svg><?php echo esc_html( sprintf( /* translators: %s: bathroom count. */ _n( '%s Bath', '%s Baths', (int) $hvn_baths, 'havenlytics-realty' ), number_format_i18n( (int) $hvn_baths ) ) ); ?></div>
								<?php endif; ?>
								<?php if ( '' !== $hvn_area ) : ?>
									<div><svg width="15" height="15" viewBox="0 0 15 15" fill="none"><rect x="2" y="2" width="11" height="11" stroke-width="1.3"/></svg><?php echo esc_html( sprintf( /* translators: %s: area in square feet. */ __( '%s sqft', 'havenlytics-realty' ), number_format_i18n( (float) $hvn_area ) ) ); ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<?php if ( $hvn_view_all ) : ?>
			<div class="hvn-theme-home-properties__more">
				<a href="<?php echo esc_url( $hvn_view_all ); ?>" class="hvn-theme-home-btn hvn-theme-home-btn--primary"><?php esc_html_e( 'Browse All Properties', 'havenlytics-realty' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
