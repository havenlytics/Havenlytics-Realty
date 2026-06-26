<?php
/**
 * Homepage blog carousel card.
 *
 * @package Havenlytics_Realty
 */
?>
<article <?php post_class( 'hvn-realty-blog-card' ); ?>>
	<a class="hvn-realty-blog-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php
			the_post_thumbnail(
				'hvn-realty-blog',
				array(
					'class'   => 'hvn-realty-blog-card__image',
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
			?>
		<?php else : ?>
			<span class="hvn-realty-blog-card__placeholder" aria-hidden="true">
				<i class="fas fa-newspaper"></i>
			</span>
		<?php endif; ?>
	</a>
	<div class="hvn-realty-blog-card__body">
		<?php
		$categories = get_the_category();
		if ( ! empty( $categories ) ) :
			?>
			<p class="hvn-realty-blog-card__category">
				<a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
					<?php echo esc_html( $categories[0]->name ); ?>
				</a>
			</p>
		<?php endif; ?>
		<h3 class="hvn-realty-blog-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<p class="hvn-realty-blog-card__meta">
			<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
		</p>
		<p class="hvn-realty-blog-card__excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?>
		</p>
		<a class="hvn-realty-blog-card__link" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'havenlytics-realty' ); ?>
		</a>
	</div>
</article>
