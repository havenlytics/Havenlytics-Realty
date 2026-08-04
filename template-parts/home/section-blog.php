<?php
/**
 * Homepage 3.0 — Latest blog posts.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_count = (int) get_theme_mod( 'hvn_realty_home_blog_count', 3 );
$hvn_count = max( 3, min( 6, $hvn_count ) );

$hvn_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => $hvn_count,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $hvn_query->have_posts() && ! is_customize_preview() ) {
	wp_reset_postdata();
	return;
}

$hvn_blog_url = get_permalink( (int) get_option( 'page_for_posts' ) );
if ( ! $hvn_blog_url ) {
	$hvn_blog_url = home_url( '/' );
}

$hvn_arrow_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<section id="hvn-theme-home-blog" aria-labelledby="hvn-theme-home-blog-title">
	<div class="hvn-realty-wrap">
		<div class="hvn-realty-section-head-row hvn-realty-reveal">
			<div class="hvn-realty-section-head" style="margin-bottom:0;">
				<?php
				if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
					hvn_realty_render_home_eyebrow( hvn_realty_get_home_section_subtitle( 'blog', __( 'Latest Insights', 'havenlytics-realty' ) ) );
				}
				?>
				<h2 id="hvn-theme-home-blog-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'blog', __( 'Market notes from our research desk', 'havenlytics-realty' ) ) ); ?></h2>
			</div>
			<a href="<?php echo esc_url( $hvn_blog_url ); ?>" class="hvn-realty-link-arrow"><?php esc_html_e( 'View All Articles', 'havenlytics-realty' ); ?> <?php echo $hvn_arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
		<div class="hvn-realty-blog-grid">
			<?php
			while ( $hvn_query->have_posts() ) :
				$hvn_query->the_post();
				$hvn_cats = get_the_category();
				$hvn_cat  = ! empty( $hvn_cats ) ? $hvn_cats[0]->name : '';
				?>
				<article class="hvn-realty-blog-card hvn-realty-reveal">
					<a href="<?php the_permalink(); ?>">
						<div class="hvn-realty-blog-media">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'hvn-realty-blog', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
							<?php endif; ?>
							<?php if ( $hvn_cat ) : ?>
								<span class="hvn-realty-blog-cat"><?php echo esc_html( $hvn_cat ); ?></span>
							<?php endif; ?>
						</div>
						<div class="hvn-realty-blog-body">
							<div class="hvn-realty-blog-meta">
								<span><?php echo esc_html( get_the_date() ); ?></span>
							</div>
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
							<span class="hvn-realty-link-arrow"><?php esc_html_e( 'Read Article', 'havenlytics-realty' ); ?> <?php echo $hvn_arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
					</a>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
