<?php

/**

 * The template for displaying search results pages

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



		<div class="hvn-theme-search-header">

			<h1 class="hvn-theme-search-title">

				<?php

				printf(

					/* translators: %s: search query. */

					esc_html__( 'Search Results for: %s', 'havenlytics-realty' ),

					'<span class="search-query">"' . esc_html( get_search_query() ) . '"</span>'

				);

				?>

			</h1>

			<div class="hvn-theme-search-count">

				<?php

				global $wp_query;

				printf(

					/* translators: %d: number of results */

					esc_html( _n( '%d result found', '%d results found', $wp_query->found_posts, 'havenlytics-realty' ) ),

					number_format_i18n( $wp_query->found_posts )

				);

				?>

			</div>

			<div class="hvn-theme-search-inline">

				<?php get_search_form(); ?>

			</div>

		</div>



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

