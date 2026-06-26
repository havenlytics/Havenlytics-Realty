<?php

/**

 * The template for displaying all single posts

 *

 * @package Havenlytics_Realty

 */



get_header();



$layout_class = hvn_realty_get_layout_sidebar_classes();

$has_sidebar  = hvn_realty_has_sidebar();
$sidebar_layout = function_exists( 'hvn_realty_sidebar_layout_enabled' ) && hvn_realty_sidebar_layout_enabled();

?>



<main id="primary" class="hvn-theme-layout hvn-layout-single hvn-theme-single-layout <?php echo esc_attr( $layout_class ); ?>" role="main">

	<div class="hvn-theme-container">

		<?php hvn_realty_breadcrumbs(); ?>



		<?php if ( $sidebar_layout ) : ?>

		<div class="hvn-theme-layout-row">

		<?php endif; ?>



			<div class="hvn-theme-content-area">

				<?php

				while ( have_posts() ) :

					the_post();

					get_template_part( 'template-parts/content', 'single' );

					hvn_realty_property_single_after();

					the_post_navigation(

						array(

							'prev_text'          => '<span class="nav-label">' . esc_html__( 'Previous Post', 'havenlytics-realty' ) . '</span><span class="nav-title">%title</span>',

							'next_text'          => '<span class="nav-label">' . esc_html__( 'Next Post', 'havenlytics-realty' ) . '</span><span class="nav-title">%title</span>',

							'screen_reader_text' => esc_html__( 'Post navigation', 'havenlytics-realty' ),

						)

					);

					if ( comments_open() || get_comments_number() ) :

						comments_template();

					endif;

				endwhile;

				?>

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

