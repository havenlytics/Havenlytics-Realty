<?php
/**
 * Homepage: Latest blog posts carousel.
 *
 * @package Havenlytics_Realty
 */

$post_count = absint( get_theme_mod( 'hvn_realty_home_posts_count', 6 ) );
$post_count = max( 1, min( 12, $post_count ) );

$blog_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $post_count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $blog_query->have_posts() ) {
	return;
}

$posts_page_id = (int) get_option( 'page_for_posts', 0 );
$blog_url      = $posts_page_id > 0 ? get_permalink( $posts_page_id ) : home_url( '/' );

$title    = hvn_realty_get_home_section_title( 'blog', __( 'Latest from the Blog', 'havenlytics-realty' ) );
$subtitle = hvn_realty_get_home_section_subtitle(
	'blog',
	__( 'Market insights, tips, and industry news.', 'havenlytics-realty' )
);
?>
<section id="hvn-realty-section-blog" class="hvn-realty-section hvn-realty-section--blog" aria-labelledby="hvn-realty-blog-title">
	<div class="hvn-realty-container">
		<header class="hvn-realty-section__header hvn-realty-section__header--split">
			<div class="hvn-realty-section__header-copy">
				<h2 class="hvn-realty-section__title" id="hvn-realty-blog-title">
					<?php echo esc_html( $title ); ?>
				</h2>
				<?php if ( $subtitle ) : ?>
					<p class="hvn-realty-section__subtitle hvn-realty-section__subtitle--inline"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</div>
			<nav class="hvn-realty-carousel-nav" aria-label="<?php esc_attr_e( 'Blog posts carousel', 'havenlytics-realty' ); ?>">
				<button type="button" class="hvn-realty-carousel-btn" data-blog-carousel-prev aria-label="<?php esc_attr_e( 'Previous posts', 'havenlytics-realty' ); ?>" disabled>
					<i class="fas fa-chevron-left" aria-hidden="true"></i>
				</button>
				<button type="button" class="hvn-realty-carousel-btn" data-blog-carousel-next aria-label="<?php esc_attr_e( 'Next posts', 'havenlytics-realty' ); ?>">
					<i class="fas fa-chevron-right" aria-hidden="true"></i>
				</button>
			</nav>
		</header>

		<div class="hvn-realty-section__body">
			<div class="hvn-realty-blog-carousel" data-hvn-realty-blog-carousel>
				<div class="hvn-realty-blog-carousel__viewport">
					<ul class="hvn-realty-blog-carousel__track" data-blog-carousel-track role="list">
						<?php
						while ( $blog_query->have_posts() ) :
							$blog_query->the_post();
							?>
							<li class="hvn-realty-blog-carousel__slide" role="listitem">
								<?php get_template_part( 'template-parts/home/partials/blog-card' ); ?>
							</li>
						<?php endwhile; ?>
					</ul>
				</div>
			</div>
		</div>

		<?php if ( $posts_page_id > 0 ) : ?>
			<footer class="hvn-realty-section__footer">
				<a class="hvn-realty-btn hvn-realty-btn--outline" href="<?php echo esc_url( $blog_url ); ?>">
					<?php esc_html_e( 'Read the Blog', 'havenlytics-realty' ); ?>
				</a>
			</footer>
		<?php endif; ?>
	</div>
</section>
<?php
wp_reset_postdata();
