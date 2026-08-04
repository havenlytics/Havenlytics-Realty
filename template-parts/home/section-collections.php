<?php
/**
 * Homepage 3.0 — Curated collections.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'hvnly_property' ) ) {
	return;
}

$hvn_base_args = array(
	'post_type'           => 'hvnly_property',
	'posts_per_page'      => 3,
	'post_status'         => 'publish',
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
	'orderby'             => 'date',
	'order'               => 'DESC',
);

$hvn_featured_args               = $hvn_base_args;
$hvn_featured_args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	'relation' => 'OR',
	array(
		'key'     => '_hvnly_property_featured',
		'value'   => '1',
		'compare' => '=',
	),
	array(
		'key'     => '_hvnly_property_action_tool_is_featured',
		'value'   => '1',
		'compare' => '=',
	),
);

$hvn_query = new WP_Query( $hvn_featured_args );

if ( ! $hvn_query->have_posts() ) {
	$hvn_query = new WP_Query( $hvn_base_args );
}

if ( ! $hvn_query->have_posts() && ! is_customize_preview() ) {
	wp_reset_postdata();
	return;
}

$hvn_coll_eyebrow = (string) get_theme_mod( 'hvn_realty_home_collections_subtitle', '' );
$hvn_coll_title   = (string) get_theme_mod( 'hvn_realty_home_collections_title', '' );
if ( '' === trim( $hvn_coll_eyebrow ) ) {
	$hvn_coll_eyebrow = __( 'Curated Collections', 'havenlytics-realty' );
}
if ( '' === trim( $hvn_coll_title ) ) {
	$hvn_coll_title = __( 'Portfolios for the particular buyer', 'havenlytics-realty' );
}
$hvn_arrow_svg    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<section id="hvn-theme-home-collections" aria-labelledby="hvn-theme-home-collections-title">
	<div class="hvn-realty-wrap">
		<div class="hvn-realty-section-head hvn-realty-reveal">
			<?php
			if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
				hvn_realty_render_home_eyebrow( $hvn_coll_eyebrow );
			}
			?>
			<h2 id="hvn-theme-home-collections-title"><?php echo esc_html( $hvn_coll_title ); ?></h2>
		</div>
		<div class="hvn-realty-collections">
			<?php
			while ( $hvn_query->have_posts() ) :
				$hvn_query->the_post();
				$hvn_id        = (int) get_the_ID();
				$hvn_title     = get_the_title( $hvn_id );
				$hvn_permalink = get_permalink( $hvn_id );

				$hvn_price_raw = get_post_meta( $hvn_id, '_hvnly_property_price', true );
				$hvn_price     = '';
				if ( '' !== $hvn_price_raw && null !== $hvn_price_raw ) {
					$hvn_price = function_exists( 'hvnly_format_price' )
						? wp_strip_all_tags( hvnly_format_price( $hvn_price_raw ) )
						: number_format_i18n( (float) $hvn_price_raw );
				}

				$hvn_is_featured = ( '1' === (string) get_post_meta( $hvn_id, '_hvnly_property_featured', true ) )
					|| ( '1' === (string) get_post_meta( $hvn_id, '_hvnly_property_action_tool_is_featured', true ) );
				$hvn_tag         = $hvn_is_featured ? __( 'Featured', 'havenlytics-realty' ) : $hvn_price;

				$hvn_status_term = function_exists( 'hvnly_get_property_status' ) ? hvnly_get_property_status( $hvn_id ) : false;
				$hvn_status_name = ( $hvn_status_term && ! is_wp_error( $hvn_status_term ) ) ? $hvn_status_term->name : '';

				$hvn_excerpt = get_the_excerpt( $hvn_id );
				if ( ! $hvn_excerpt && $hvn_status_name ) {
					$hvn_excerpt = $hvn_status_name;
				}
				?>
				<article class="hvn-realty-coll-card hvn-realty-reveal">
					<?php if ( has_post_thumbnail( $hvn_id ) ) : ?>
						<?php echo get_the_post_thumbnail( $hvn_id, 'large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( $hvn_title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes attributes. ?>
					<?php endif; ?>
					<div class="hvn-realty-coll-inner">
						<?php if ( $hvn_tag ) : ?>
							<span class="hvn-realty-coll-tag"><?php echo esc_html( $hvn_tag ); ?></span>
						<?php endif; ?>
						<h3><?php echo esc_html( $hvn_title ); ?></h3>
						<?php if ( $hvn_excerpt ) : ?>
							<p><?php echo esc_html( wp_trim_words( $hvn_excerpt, 18 ) ); ?></p>
						<?php endif; ?>
						<a href="<?php echo esc_url( $hvn_permalink ); ?>" class="hvn-realty-link-arrow">
							<?php esc_html_e( 'Explore Collection', 'havenlytics-realty' ); ?> <?php echo $hvn_arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</a>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
