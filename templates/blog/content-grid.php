<?php
/**
 * Grid blog card — vertical card layout.
 *
 * @package Havenlytics_Realty
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'hvn-blog-card hvn-blog-card--grid' ); ?>>
	<?php if ( is_sticky() ) : ?>
		<span class="hvn-blog-card__sticky-badge"><?php esc_html_e( 'Featured', 'havenlytics-realty' ); ?></span>
	<?php endif; ?>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="hvn-blog-card__media">
			<a class="hvn-blog-card__media-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<?php hvn_realty_blog_the_thumbnail(); ?>
			</a>
			<div class="hvn-blog-card__badge-row hvn-blog-card__badge-row--overlay">
				<?php hvn_realty_blog_the_category_badge(); ?>
			</div>
		</div>
	<?php else : ?>
		<div class="hvn-blog-card__media hvn-blog-card__media--placeholder" aria-hidden="true">
			<div class="hvn-blog-card__badge-row hvn-blog-card__badge-row--overlay">
				<?php hvn_realty_blog_the_category_badge(); ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="hvn-blog-card__body">
		<header class="hvn-blog-card__header">
			<?php
			the_title(
				'<h2 class="hvn-blog-card__title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">',
				'</a></h2>'
			);
			?>
		</header>

		<?php hvn_realty_blog_the_entry_meta(); ?>

		<div class="hvn-blog-card__excerpt">
			<?php the_excerpt(); ?>
		</div>

		<footer class="hvn-blog-card__footer">
			<a class="hvn-blog-card__read-more" href="<?php the_permalink(); ?>">
				<?php esc_html_e( 'Read More', 'havenlytics-realty' ); ?>
			</a>
			<?php if ( comments_open() ) : ?>
				<span class="hvn-blog-card__comments">
					<?php comments_popup_link( esc_html__( '0 Comments', 'havenlytics-realty' ), esc_html__( '1 Comment', 'havenlytics-realty' ), esc_html__( '% Comments', 'havenlytics-realty' ) ); ?>
				</span>
			<?php endif; ?>
		</footer>
	</div>
</article>
