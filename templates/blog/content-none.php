<?php
/**
 * Blog empty state.
 *
 * @package Havenlytics_Realty
 */
?>
<section class="hvn-blog-none">
	<div class="hvn-blog-none__inner">
		<div class="hvn-blog-none__icon" aria-hidden="true">
			<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="12" cy="12" r="10"></circle>
				<line x1="12" y1="8" x2="12" y2="12"></line>
				<line x1="12" y1="16" x2="12.01" y2="16"></line>
			</svg>
		</div>

		<?php
		$empty_title_tag = ( is_archive() && ! is_search() ) ? 'h1' : 'h2';
		?>
		<<?php echo tag_escape( $empty_title_tag ); ?> class="hvn-blog-none__title">
			<?php esc_html_e( 'Nothing Found', 'havenlytics-realty' ); ?>
		</<?php echo tag_escape( $empty_title_tag ); ?>>

		<div class="hvn-blog-none__message">
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
				<p>
					<?php
					printf(
						/* translators: 1: link to WP admin new post page. */
						esc_html__( 'Ready to publish your first post? %s.', 'havenlytics-realty' ),
						'<a href="' . esc_url( admin_url( 'post-new.php' ) ) . '">' . esc_html__( 'Get started here', 'havenlytics-realty' ) . '</a>'
					);
					?>
				</p>
			<?php elseif ( is_search() ) : ?>
				<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'havenlytics-realty' ); ?></p>
				<?php get_search_form(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'havenlytics-realty' ); ?></p>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
