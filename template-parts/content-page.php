<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package Havenlytics_Realty
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'hvn-theme-page-article' ); ?>>
	<?php if ( has_post_thumbnail() && ! hvn_realty_is_elementor_page() ) : ?>
		<figure class="hvn-theme-page-hero">
			<?php
			the_post_thumbnail(
				'large',
				array(
					'class'   => 'hvn-theme-page-hero-image',
					'loading' => 'eager',
				)
			);
			?>
		</figure>
	<?php endif; ?>

	<?php
	$hide_page_header = hvn_realty_is_elementor_page()
		|| ( function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() );
	?>
	<?php if ( ! $hide_page_header ) : ?>
		<header class="hvn-theme-page-header">
			<?php the_title( '<h1 class="hvn-theme-page-title">', '</h1>' ); ?>
		</header>
	<?php endif; ?>

	<div class="hvn-theme-page-body">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before'      => '<nav class="hvn-theme-page-links" aria-label="' . esc_attr__( 'Page links', 'havenlytics-realty' ) . '"><span class="hvn-theme-page-links-label">' . esc_html__( 'Pages:', 'havenlytics-realty' ) . '</span>',
				'after'       => '</nav>',
				'link_before' => '<span class="hvn-theme-page-link">',
				'link_after'  => '</span>',
			)
		);
		?>
	</div>

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="hvn-theme-page-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						__( 'Edit <span class="screen-reader-text">%s</span>', 'havenlytics-realty' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				),
				'<span class="hvn-theme-page-edit-link">',
				'</span>'
			);
			?>
		</footer>
	<?php endif; ?>
</article>
