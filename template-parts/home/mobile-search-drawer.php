<?php
/**
 * Mobile search drawer — floating dock and filter panel (homepage, mobile only).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_search_url = function_exists( 'hvn_realty_get_property_search_url' )
	? hvn_realty_get_property_search_url()
	: home_url( '/' );

$hvn_department_tabs = function_exists( 'hvn_realty_get_home_search_department_tabs' )
	? hvn_realty_get_home_search_department_tabs()
	: array();

$hvn_default_department = '';
foreach ( $hvn_department_tabs as $hvn_tab ) {
	if ( ! empty( $hvn_tab['is_default'] ) ) {
		$hvn_default_department = isset( $hvn_tab['department'] ) ? (string) $hvn_tab['department'] : '';
		break;
	}
}

$hvn_default_dept_label = '';
foreach ( $hvn_department_tabs as $hvn_tab ) {
	if ( ! empty( $hvn_tab['is_default'] ) ) {
		$hvn_default_dept_label = isset( $hvn_tab['label'] ) ? (string) $hvn_tab['label'] : '';
		break;
	}
}

$hvn_submit_label = function_exists( 'hvn_realty_get_hero_search_button_text' )
	? hvn_realty_get_hero_search_button_text()
	: __( 'Search properties', 'havenlytics-realty' );
?>
<div class="hvn-theme-home-msd-root" id="hvn-theme-home-msd-root" hidden aria-hidden="true">
	<div class="hvn-theme-home-msd-scrim" id="hvn-theme-home-msd-scrim" hidden></div>

	<div class="hvn-theme-home-msd-dock-wrap" id="hvn-theme-home-msd-dock-wrap">
		<div class="hvn-theme-home-msd-dock" id="hvn-theme-home-msd-dock">
			<div class="hvn-theme-home-msd-pills-scroll" id="hvn-theme-home-msd-pills-scroll">
				<div class="hvn-theme-home-msd-pills-fade hvn-theme-home-msd-pills-fade--start" aria-hidden="true"></div>
				<div
					class="hvn-theme-home-msd-pills"
					id="hvn-theme-home-msd-pills"
					role="tablist"
					aria-label="<?php echo esc_attr( function_exists( 'hvn_realty_get_hero_search_tabs_label' ) ? hvn_realty_get_hero_search_tabs_label() : __( 'Listing type', 'havenlytics-realty' ) ); ?>"
				>
					<?php foreach ( $hvn_department_tabs as $hvn_tab_key => $hvn_tab ) : ?>
					<?php
					$hvn_is_active = ! empty( $hvn_tab['is_default'] );
					$hvn_dept_slug = isset( $hvn_tab['department'] ) ? (string) $hvn_tab['department'] : '';
					?>
					<button
						type="button"
						class="hvn-theme-home-msd-pill<?php echo $hvn_is_active ? ' hvn-theme-home-msd-pill-active' : ''; ?>"
						data-hvn-msd-department="<?php echo esc_attr( $hvn_dept_slug ); ?>"
						data-hvn-msd-tab="<?php echo esc_attr( $hvn_tab_key ); ?>"
						data-hvn-msd-label="<?php echo esc_attr( isset( $hvn_tab['label'] ) ? (string) $hvn_tab['label'] : '' ); ?>"
						role="tab"
						aria-selected="<?php echo $hvn_is_active ? 'true' : 'false'; ?>"
					>
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l9-6 9 6"></path><path d="M5 10v10h14V10"></path></svg>
						<?php echo esc_html( isset( $hvn_tab['label'] ) ? (string) $hvn_tab['label'] : '' ); ?>
					</button>
					<?php endforeach; ?>
				</div>
				<div class="hvn-theme-home-msd-pills-fade hvn-theme-home-msd-pills-fade--end" aria-hidden="true"></div>
			</div>
		</div>

		<div
			class="hvn-theme-home-msd-drawer"
			id="hvn-theme-home-msd-drawer"
			role="dialog"
			aria-modal="true"
			aria-labelledby="hvn-theme-home-msd-drawer-title"
			hidden
		>
			<div class="hvn-theme-home-msd-drag-handle-wrap" id="hvn-theme-home-msd-drag-handle">
				<div class="hvn-theme-home-msd-drag-handle" aria-hidden="true"></div>
			</div>

			<div class="hvn-theme-home-msd-drawer-header">
				<h3 id="hvn-theme-home-msd-drawer-title">
					<?php esc_html_e( 'Search', 'havenlytics-realty' ); ?>
					<span id="hvn-theme-home-msd-drawer-dept"><?php echo esc_html( $hvn_default_dept_label ); ?></span>
				</h3>
				<button type="button" class="hvn-theme-home-msd-close-btn" id="hvn-theme-home-msd-close" aria-label="<?php esc_attr_e( 'Close search drawer', 'havenlytics-realty' ); ?>">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
				</button>
			</div>

			<form
				class="hvn-theme-home-msd-form"
				id="hvn-theme-home-msd-form"
				method="get"
				action="<?php echo esc_url( $hvn_search_url ); ?>"
				aria-label="<?php esc_attr_e( 'Mobile property search', 'havenlytics-realty' ); ?>"
			>
				<input type="hidden" name="department" id="hvn-theme-home-msd-department" value="<?php echo esc_attr( $hvn_default_department ); ?>">
				<input type="hidden" name="view_type" value="grid">
				<input type="hidden" name="paged" value="1">

				<div class="hvn-theme-home-msd-drawer-body" id="hvn-theme-home-msd-drawer-body">
					<?php
					if ( function_exists( 'hvn_realty_render_mobile_search_drawer_fields' ) ) {
						hvn_realty_render_mobile_search_drawer_fields();
					}
					?>
				</div>

				<div class="hvn-theme-home-msd-drawer-footer">
					<button type="submit" class="hvn-theme-home-msd-search-submit" id="hvn-theme-home-msd-submit">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
						<?php echo esc_html( $hvn_submit_label ); ?>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
