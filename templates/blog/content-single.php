<?php
/**
 * Single blog post — semantic markup, blog-module scoped classes.
 *
 * @package Havenlytics_Realty
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'hvn-blog-single hvn-theme-single-post' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="hvn-blog-single__media">
			<?php the_post_thumbnail( 'large', array( 'class' => 'hvn-blog-single__image' ) ); ?>
		</div>
	<?php endif; ?>

	<header class="hvn-blog-single__header entry-header">
		<?php the_title( '<h1 class="hvn-blog-single__title entry-title">', '</h1>' ); ?>

		<div class="hvn-blog-single__meta entry-meta">
			<time class="hvn-blog-single__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
			<span class="hvn-blog-single__author">
				<?php
				printf(
					/* translators: %s: post author */
					esc_html__( 'By %s', 'havenlytics-realty' ),
					'<a class="hvn-blog-single__author-link" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
				);
				?>
			</span>
			<?php if ( comments_open() || get_comments_number() ) : ?>
				<span class="hvn-blog-single__comments">
					<?php comments_popup_link( esc_html__( '0 Comments', 'havenlytics-realty' ), esc_html__( '1 Comment', 'havenlytics-realty' ), esc_html__( '% Comments', 'havenlytics-realty' ) ); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php
		$term = function_exists( 'hvn_realty_blog_get_primary_category' ) ? hvn_realty_blog_get_primary_category() : null;
		if ( $term instanceof WP_Term ) :
			?>
			<div class="hvn-blog-single__categories">
				<?php hvn_realty_blog_the_category_badge(); ?>
			</div>
		<?php endif; ?>
	</header>

	<div class="hvn-blog-single__content entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					__( 'Continue reading %s', 'havenlytics-realty' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				the_title( '<span class="screen-reader-text">"', '"</span>', false )
			)
		);

		wp_link_pages(
			array(
				'before'      => '<div class="page-links hvn-blog-single__page-links">' . esc_html__( 'Pages:', 'havenlytics-realty' ),
				'after'       => '</div>',
				'link_before' => '<span class="page-link">',
				'link_after'  => '</span>',
			)
		);
		?>
	</div>

	<footer class="hvn-blog-single__footer entry-footer">
		<?php if ( has_tag() ) : ?>
			<div class="hvn-blog-single__tags">
				<span class="hvn-blog-single__tags-label"><?php esc_html_e( 'Tags:', 'havenlytics-realty' ); ?></span>
				<?php the_tags( '', ' ', '' ); ?>
			</div>
		<?php endif; ?>
	</footer>

	<?php if ( get_the_author_meta( 'description' ) ) : ?>
		<aside class="hvn-blog-single__author" aria-label="<?php esc_attr_e( 'About the author', 'havenlytics-realty' ); ?>">
			<div class="hvn-blog-single__author-avatar">
				<?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
			</div>
			<div class="hvn-blog-single__author-body">
				<h2 class="hvn-blog-single__author-name"><?php echo esc_html( get_the_author() ); ?></h2>
				<div class="hvn-blog-single__author-bio">
					<?php echo wp_kses_post( get_the_author_meta( 'description' ) ); ?>
				</div>
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="hvn-blog-single__author-link">
					<?php esc_html_e( 'View all posts', 'havenlytics-realty' ); ?> &rarr;
				</a>
			</div>
		</aside>
	<?php endif; ?>
</article>
