<?php

/**

 * The template for displaying archive pages

 *

 * @package Havenlytics_Realty

 */



get_header();



$layout_class = hvn_realty_get_layout_sidebar_classes();

$shell_class = function_exists( 'hvn_realty_get_blog_shell_classes' )
	? hvn_realty_get_blog_shell_classes()
	: 'hvn-theme-layout hvn-layout-blog ' . $layout_class;

$has_sidebar  = hvn_realty_has_sidebar();
$sidebar_layout = function_exists( 'hvn_realty_sidebar_layout_enabled' ) && hvn_realty_sidebar_layout_enabled();

?>



<main id="primary" class="<?php echo esc_attr( trim( $shell_class ) ); ?>" role="main">

	<div class="hvn-theme-container">

		<?php hvn_realty_breadcrumbs(); ?>

		<header class="hvn-theme-blog-header">
			<?php
			if ( is_archive() ) {
				the_archive_title( '<h1 class="hvn-theme-blog-title">', '</h1>' );
				the_archive_description( '<div class="hvn-theme-archive-description">', '</div>' );
			} elseif ( is_home() && ! is_front_page() ) {
				echo '<h1 class="hvn-theme-blog-title">';
				single_post_title();
				echo '</h1>';
			}
			?>
		</header>

		<?php hvn_realty_property_archive_before(); ?>



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

get_footer();

