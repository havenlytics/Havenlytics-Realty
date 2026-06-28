<?php
/**
 * Header actions (menu toggle, search, CTA) template part.
 *
 * @package Havenlytics_Realty
 */

$use_property_search   = function_exists( 'hvn_realty_use_header_property_search_panel' ) && hvn_realty_use_header_property_search_panel();
?>
<div class="hvn-theme-actions">
	<button class="hvn-theme-menu-toggle" type="button" aria-label="<?php esc_attr_e( 'Menu', 'havenlytics-realty' ); ?>" aria-expanded="false" aria-controls="hvn-theme-home-mobile">
		<span class="hamburger" aria-hidden="true"></span>
		<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'havenlytics-realty' ); ?></span>
	</button>

	<?php if ( function_exists( 'hvn_realty_show_header_property_search' ) ? hvn_realty_show_header_property_search() : hvn_realty_show_header_search() ) : ?>
		<?php if ( $use_property_search ) : ?>
			<button
				class="hvn-theme-search-toggle"
				type="button"
				aria-label="<?php esc_attr_e( 'Open property search', 'havenlytics-realty' ); ?>"
				aria-expanded="false"
				aria-controls="hvn-property-search-panel"
				data-hvn-search-open
			>
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<circle cx="11" cy="11" r="8"></circle>
					<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
				</svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Search properties', 'havenlytics-realty' ); ?></span>
			</button>
		<?php else : ?>
			<a class="hvn-theme-search-toggle hvn-theme-search-link" href="<?php echo esc_url( hvn_realty_get_search_url() ); ?>" aria-label="<?php esc_attr_e( 'Search', 'havenlytics-realty' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<circle cx="11" cy="11" r="8"></circle>
					<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
				</svg>
				<span class="screen-reader-text"><?php esc_html_e( 'Search', 'havenlytics-realty' ); ?></span>
			</a>
		<?php endif; ?>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/header/header-actions', null, array( 'context' => 'default' ) ); ?>
</div>
