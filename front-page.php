<?php
/**
 * Front page — realty homepage when the Havenlytics plugin is active,
 * otherwise standard WordPress front-page behavior (standalone blog mode).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {
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

if ( function_exists( 'hvn_realty_get_standalone_front_template' ) && 'home' === hvn_realty_get_standalone_front_template() ) {
	if ( function_exists( 'hvn_realty_is_empty_realty_front_page' ) && hvn_realty_is_empty_realty_front_page() ) {
		get_header();
		hvn_realty_render_standalone_blog_index();
		get_footer();
		return;
	}

	require get_template_directory() . '/home.php';
	return;
}

require get_template_directory() . '/page.php';
