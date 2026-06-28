<?php
/**
 * Template Name: Real Estate Homepage
 * Template Post Type: page
 *
 * Renders the Homepage 2.0.0 layout on any assigned page, using the dedicated
 * homepage header/footer so the design matches the front page exactly.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
