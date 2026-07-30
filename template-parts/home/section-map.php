<?php
/**
 * Homepage 3.0 — Interactive property map.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'hvnly_property' ) ) {
	return;
}

$hvn_map_eyebrow = (string) get_theme_mod( 'hvn_realty_home_map_subtitle', __( 'Explore The Map', 'havenlytics-realty' ) );
$hvn_map_title   = (string) get_theme_mod( 'hvn_realty_home_map_title', __( 'Every listing, precisely placed', 'havenlytics-realty' ) );
$hvn_map_text    = (string) get_theme_mod( 'hvn_realty_home_map_text', __( 'Pan the map, zoom into a block, and preview a home without leaving the page.', 'havenlytics-realty' ) );
$hvn_heart_svg   = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>';
?>
<section class="hvn-realty-map-section" id="hvn-theme-home-map" aria-labelledby="hvn-theme-home-map-title">
	<div class="hvn-realty-wrap">
		<div class="hvn-realty-section-head-row hvn-realty-reveal">
			<div class="hvn-realty-section-head" style="margin-bottom:0;">
				<?php
				if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
					hvn_realty_render_home_eyebrow( $hvn_map_eyebrow );
				}
				?>
				<h2 id="hvn-theme-home-map-title"><?php echo esc_html( $hvn_map_title ); ?></h2>
				<?php if ( $hvn_map_text ) : ?>
					<p><?php echo esc_html( $hvn_map_text ); ?></p>
				<?php endif; ?>
			</div>
			<div class="hvn-realty-map-legend">
				<span class="hvn-realty-map-legend-item"><i></i><?php esc_html_e( 'Available Now', 'havenlytics-realty' ); ?></span>
				<span class="hvn-realty-map-legend-item hvn-realty-map-legend-item--open"><i></i><?php esc_html_e( 'Open House', 'havenlytics-realty' ); ?></span>
			</div>
		</div>
	</div>

	<div class="hvn-realty-map-stage hvn-realty-reveal" id="hvnRealtyMapStage">
		<div id="hvnRealtyPropertyMap" class="hvn-realty-map-canvas" role="application" aria-label="<?php esc_attr_e( 'Interactive map of featured properties', 'havenlytics-realty' ); ?>"></div>

		<div class="hvn-realty-map-tooltip" id="hvnRealtyMapTooltip" role="dialog" aria-modal="true" aria-labelledby="hvnRealtyMapTooltipTitle" aria-hidden="true" inert>
			<button class="hvn-realty-map-tooltip-close" id="hvnRealtyMapTooltipClose" type="button" tabindex="-1" aria-label="<?php esc_attr_e( 'Close property preview', 'havenlytics-realty' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
			</button>
			<div class="hvn-realty-map-tooltip-media">
				<img id="hvnRealtyMapTooltipImg" src="" alt="">
				<button
					type="button"
					class="hvn-realty-fav-btn hvnly-property--grid-list--favorite"
					id="hvnRealtyMapTooltipFav"
					tabindex="-1"
					data-hvnly-favorite="1"
					data-property-id=""
					data-property-title=""
					data-property-thumb=""
					aria-pressed="false"
					aria-label="<?php esc_attr_e( 'Save property', 'havenlytics-realty' ); ?>"
				><?php echo $hvn_heart_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<div class="hvn-realty-map-tooltip-media-meta">
					<span class="hvn-realty-map-tooltip-price" id="hvnRealtyMapTooltipPrice"></span>
					<h3 id="hvnRealtyMapTooltipTitle"></h3>
				</div>
			</div>
			<div class="hvn-realty-map-tooltip-body">
				<p class="hvn-realty-map-tooltip-address" id="hvnRealtyMapTooltipAddress"></p>
				<div class="hvn-realty-map-tooltip-meta" id="hvnRealtyMapTooltipMeta"></div>
				<a href="#" class="hvn-realty-btn hvn-realty-btn-forest hvn-realty-map-tooltip-cta" id="hvnRealtyMapTooltipLink" tabindex="-1"><?php esc_html_e( 'View Property', 'havenlytics-realty' ); ?></a>
			</div>
		</div>
	</div>
</section>
