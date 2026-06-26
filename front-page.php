<?php
/**
 * Front page — Real Estate Homepage sections.
 *
 * @package Havenlytics_Realty
 */

get_header();

if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) :
	?>
	<main id="primary" class="hvn-realty-home" role="main">
		<?php
		if ( function_exists( 'hvn_realty_render_homepage_sections' ) ) {
			hvn_realty_render_homepage_sections();
		}
		?>
	</main>
	<?php
else :
	$layout_class = hvn_realty_get_layout_sidebar_classes();
	$shell_class  = function_exists( 'hvn_realty_get_blog_shell_classes' )
		? hvn_realty_get_blog_shell_classes()
		: 'hvn-theme-layout hvn-layout-blog ' . $layout_class;
	$has_sidebar    = hvn_realty_has_sidebar();
	$sidebar_layout = function_exists( 'hvn_realty_sidebar_layout_enabled' ) && hvn_realty_sidebar_layout_enabled();
	?>
	<main id="primary" class="<?php echo esc_attr( trim( $shell_class ) ); ?>" role="main">
		<div class="hvn-theme-container">
			<?php if ( $sidebar_layout ) : ?>
			<div class="hvn-theme-layout-row">
			<?php endif; ?>
				<div class="hvn-theme-content-area">
					<?php get_template_part( 'template-parts/layout/blog-content' ); ?>
				</div>
			<?php if ( $has_sidebar ) : ?>
				<?php hvn_realty_render_sidebar(); ?>
			<?php endif; ?>
			<?php if ( $sidebar_layout ) : ?>
			</div>
			<?php endif; ?>
		</div>
	</main>
	<?php
endif;

get_footer();
