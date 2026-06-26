<?php

/**

 * The template for displaying all pages

 *

 * @package Havenlytics_Realty

 */



get_header();



$layout_class = hvn_realty_get_layout_sidebar_classes();

$has_sidebar  = hvn_realty_has_sidebar();

if ( function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() ) {
	$has_sidebar = false;
}

?>



<div id="primary" class="hvn-theme-layout hvn-layout-page hvn-theme-page-layout <?php echo esc_attr( $layout_class ); ?><?php echo ( function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() ) ? ' hvn-realty-plugin-page-layout' : ''; ?>">

	<div class="hvn-theme-container">

		<?php hvn_realty_breadcrumbs(); ?>



		<?php if ( $has_sidebar ) : ?>

		<div class="hvn-theme-layout-row">

		<?php endif; ?>



			<main class="hvn-theme-content-area hvn-theme-page-content">

				<?php

				while ( have_posts() ) :

					the_post();

					get_template_part( 'template-parts/content', 'page' );



					if ( comments_open() || get_comments_number() ) :

						comments_template();

					endif;

				endwhile;

				?>

			</main>



		<?php if ( $has_sidebar ) : ?>

			<?php hvn_realty_render_sidebar(); ?>

		</div>

		<?php endif; ?>

	</div>

</div>



<?php

get_footer();

