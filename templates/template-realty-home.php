<?php
/**
 * Template Name: Real Estate Homepage
 * Template Post Type: page
 *
 * Renders the Homepage 2.0.0 layout when the Havenlytics plugin is active.
 * Falls back to standard page layout in standalone blog mode.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_realty_home = function_exists( 'hvn_realty_has_havenlytics' )
	&& hvn_realty_has_havenlytics()
	&& function_exists( 'hvn_realty_is_home_design' )
	&& hvn_realty_is_home_design();

if ( $show_realty_home ) {
	get_header( 'home' );
	?>
	<main id="primary" class="hvn-theme-home-main" role="main">
		<?php
		if ( function_exists( 'hvn_realty_render_homepage_sections' ) ) {
			hvn_realty_render_homepage_sections();
		}
		?>
	</main>
	<?php
	get_footer( 'home' );
	return;
}

require get_template_directory() . '/page.php';
