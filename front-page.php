<?php
/**
 * Front page — Havenlytics Realty Homepage 2.0.0.
 *
 * A complete rebuild from the havenlytics-realty.html prototype. Each section
 * is a dedicated, Customizer-driven template part rendered by
 * hvn_realty_render_homepage_sections(). Uses the dedicated homepage header
 * and footer so the prototype's transparent sticky header and dark footer
 * match exactly, while the rest of the site keeps its existing chrome.
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
