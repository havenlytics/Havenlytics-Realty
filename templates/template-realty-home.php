<?php
/**
 * Template Name: Real Estate Homepage
 * Template Post Type: page
 *
 * @package Havenlytics_Realty
 */

get_header();
?>

<main id="primary" class="hvn-realty-home">
	<?php
	if ( function_exists( 'hvn_realty_render_homepage_sections' ) ) {
		hvn_realty_render_homepage_sections();
	}
	?>
</main>

<?php
get_footer();
