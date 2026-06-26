<?php

/**

 * The template for displaying 404 pages (not found)

 *

 * @package Havenlytics_Realty

 */



get_header();

?>



<div id="primary" class="hvn-theme-layout hvn-layout-page hvn-theme-page-layout">

	<div class="hvn-theme-container">

		<?php hvn_realty_breadcrumbs(); ?>



		<main class="hvn-theme-content-area hvn-theme-page-content">

			<div class="hvn-theme-error-404-modern">

				<div class="hvn-theme-error-content">

					<div class="hvn-theme-error-code">404</div>

					<h1 class="hvn-theme-error-title">

						<?php esc_html_e( 'Page Not Found', 'havenlytics-realty' ); ?>

					</h1>

					<p class="hvn-theme-error-message">

						<?php esc_html_e( 'Oops! The page you are looking for does not exist.', 'havenlytics-realty' ); ?>

					</p>

					<div class="hvn-theme-error-search">

						<?php get_search_form(); ?>

					</div>

					<div class="hvn-theme-error-actions">

						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hvn-theme-btn">

							<?php esc_html_e( 'Back to Homepage', 'havenlytics-realty' ); ?>

						</a>

					</div>

				</div>

			</div>

		</main>

	</div>

</div>



<?php

get_footer();

