<?php
/**
 * Header property search panel (overlay form).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_use_header_property_search_panel' ) || ! hvn_realty_use_header_property_search_panel() ) {
	return;
}
?>
<div id="hvn-property-search-panel" class="hvn-theme-property-search-panel" aria-hidden="true" hidden>
	<div class="hvn-theme-property-search-panel__overlay" data-hvn-search-close tabindex="-1" aria-hidden="true"></div>

	<div class="hvn-theme-property-search-panel__dialog" role="dialog" aria-modal="true" aria-labelledby="hvn-property-search-panel-title">
		<button type="button" class="hvn-theme-property-search-panel__close" data-hvn-search-close aria-label="<?php esc_attr_e( 'Close search', 'havenlytics-realty' ); ?>">
			<span aria-hidden="true">&times;</span>
		</button>

		<div class="hvn-theme-property-search-panel__inner">
			<p class="hvn-theme-property-search-panel__eyebrow"><?php esc_html_e( 'Property search', 'havenlytics-realty' ); ?></p>
			<h2 id="hvn-property-search-panel-title" class="hvn-theme-property-search-panel__title">
				<?php esc_html_e( 'Find your next property', 'havenlytics-realty' ); ?>
			</h2>

			<?php get_template_part( 'template-parts/header/property-search-form', null, array( 'context' => 'header' ) ); ?>
		</div>
	</div>
</div>
