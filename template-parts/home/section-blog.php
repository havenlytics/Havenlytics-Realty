<?php
/**
 * Homepage 2.0.0 — Latest insights (blog).
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

if ( ! $hvn_query->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<section class="hvn-theme-home-section hvn-theme-home-blog" id="hvn-theme-home-blog" aria-labelledby="hvn-theme-home-blog-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-head hvn-theme-home-reveal">
			<span class="hvn-theme-home-eyebrow"><?php echo esc_html( hvn_realty_get_home_section_subtitle( 'blog', __( 'Latest Insights', 'havenlytics-realty' ) ) ); ?></span>
			<h2 id="hvn-theme-home-blog-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'blog', __( 'Market notes from our research desk', 'havenlytics-realty' ) ) ); ?></h2>
		</div>
		<div class="hvn-theme-home-blog__grid">
			<?php
			while ( $hvn_query->have_posts() ) :
				$hvn_query->the_post();
				$hvn_cats = get_the_category();
				$hvn_cat  = ! empty( $hvn_cats ) ? $hvn_cats[0]->name : '';
				?>
				<article class="hvn-theme-home-blog-card hvn-theme-home-reveal">
					<a class="hvn-theme-home-blog-card__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title() ) ) ); ?>
						<?php else : ?>
							<span class="hvn-theme-home-blog-card__media-placeholder" aria-hidden="true">
								<svg width="30" height="30" viewBox="0 0 24 24" fill="none"><path d="M4 4H20V20H4V4Z" stroke="currentColor" stroke-width="1.6"/><path d="M4 14L9 9L13 13L16 10L20 14" stroke="currentColor" stroke-width="1.6"/></svg>
							</span>
						<?php endif; ?>
					</a>
					<div class="hvn-theme-home-blog-card__body">
						<div class="hvn-theme-home-blog-card__meta">
							<?php if ( $hvn_cat ) : ?>
								<span><?php echo esc_html( $hvn_cat ); ?></span>
							<?php endif; ?>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						</div>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="hvn-theme-home-blog-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
						<a href="<?php the_permalink(); ?>" class="hvn-theme-home-blog-link">
							<?php esc_html_e( 'Read article', 'havenlytics-realty' ); ?>
							<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M3 7H11M11 7L7 3M11 7L7 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
