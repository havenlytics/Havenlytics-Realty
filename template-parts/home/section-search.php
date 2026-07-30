<?php
/**
 * Homepage 3.0 — Search console.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_search_url = function_exists( 'hvn_realty_get_property_search_url' )
	? hvn_realty_get_property_search_url()
	: home_url( '/' );

$hvn_fields_config = function_exists( 'hvn_realty_get_home_search_fields_config' )
	? hvn_realty_get_home_search_fields_config()
	: array();

$hvn_render_context = array(
	'type_terms'     => function_exists( 'hvn_realty_get_home_property_type_terms' )
		? hvn_realty_get_home_property_type_terms( 12 )
		: array(),
	'location_terms' => function_exists( 'hvn_realty_home_search_get_terms' )
		? hvn_realty_home_search_get_terms( 'hvnly_prop_locations', 100 )
		: array(),
	'status_terms'   => function_exists( 'hvn_realty_home_search_get_terms' )
		? hvn_realty_home_search_get_terms( 'hvnly_prop_status', 20 )
		: array(),
	'feature_terms'  => function_exists( 'hvn_realty_home_search_get_terms' )
		? hvn_realty_home_search_get_terms( 'hvnly_prop_features', 50 )
		: array(),
	'badge_terms'    => function_exists( 'hvn_realty_home_search_get_terms' )
		? hvn_realty_home_search_get_terms( 'hvnly_prop_badges', 50 )
		: array(),
	'count_options'  => array(
		'bedrooms'        => function_exists( 'hvn_realty_home_search_get_count_options' ) ? hvn_realty_home_search_get_count_options( 'bedrooms' ) : array(),
		'bathrooms'       => function_exists( 'hvn_realty_home_search_get_count_options' ) ? hvn_realty_home_search_get_count_options( 'bathrooms' ) : array(),
		'reception_rooms' => function_exists( 'hvn_realty_home_search_get_count_options' ) ? hvn_realty_home_search_get_count_options( 'reception_rooms' ) : array(),
	),
);

$hvn_department_tabs = function_exists( 'hvn_realty_get_home_search_department_tabs' )
	? hvn_realty_get_home_search_department_tabs()
	: array();

$hvn_default_department = '';
$hvn_default_dept_count = null;
foreach ( $hvn_department_tabs as $hvn_tab ) {
	if ( ! empty( $hvn_tab['is_default'] ) ) {
		$hvn_default_department = isset( $hvn_tab['department'] ) ? (string) $hvn_tab['department'] : '';
		$hvn_default_dept_count = isset( $hvn_tab['count'] ) ? (int) $hvn_tab['count'] : null;
		break;
	}
}

$hvn_has_advanced = function_exists( 'hvn_realty_home_search_has_advanced_fields' )
	? hvn_realty_home_search_has_advanced_fields()
	: false;

if ( null !== $hvn_default_dept_count ) {
	$hvn_listing_count = (int) $hvn_default_dept_count;
} else {
	$hvn_listing_count = 0;
	if ( post_type_exists( 'hvnly_property' ) ) {
		$hvn_counts        = wp_count_posts( 'hvnly_property' );
		$hvn_listing_count = isset( $hvn_counts->publish ) ? (int) $hvn_counts->publish : 0;
	}
}

/**
 * Simple department tab icon for the search console.
 *
 * @param string $slug Department slug.
 * @return string
 */
$hvn_realty_search_tab_icon = static function ( $slug ) {
	$slug = sanitize_key( (string) $slug );
	if ( false !== strpos( $slug, 'rent' ) ) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';
	}
	if ( false !== strpos( $slug, 'sell' ) ) {
		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 12h4l3 8 4-16 3 8h4"/></svg>';
	}
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 10.5 12 4l9 6.5"/><path d="M5 9v11h14V9"/></svg>';
};
?>
<section id="hvn-theme-home-search" class="hvn-realty-search-section" aria-label="<?php esc_attr_e( 'Property search', 'havenlytics-realty' ); ?>">
<div class="hvn-realty-wrap hvn-realty-search-console">
	<form id="hvn-theme-home-search-form" action="<?php echo esc_url( $hvn_search_url ); ?>" method="get" aria-label="<?php esc_attr_e( 'Property search', 'havenlytics-realty' ); ?>">
		<input type="hidden" name="department" id="hvn-theme-home-search-department" value="<?php echo esc_attr( $hvn_default_department ); ?>">
		<input type="hidden" name="view_type" value="grid">
		<input type="hidden" name="paged" value="1">

		<div class="hvn-realty-console-card hvn-realty-reveal">
		<?php if ( ! empty( $hvn_department_tabs ) ) : ?>
			<div class="hvn-realty-console-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Listing type', 'havenlytics-realty' ); ?>">
				<?php foreach ( $hvn_department_tabs as $hvn_tab_key => $hvn_tab ) : ?>
					<?php
					$hvn_is_active  = ! empty( $hvn_tab['is_default'] );
					$hvn_dept_slug  = isset( $hvn_tab['department'] ) ? (string) $hvn_tab['department'] : '';
					$hvn_dept_count = isset( $hvn_tab['count'] ) ? (int) $hvn_tab['count'] : 0;
					$hvn_tab_class  = 'hvn-realty-is-active hvn-theme-home-active';
					?>
					<button
						type="button"
						class="<?php echo $hvn_is_active ? esc_attr( $hvn_tab_class ) : ''; ?>"
						data-hvn-theme-tab="<?php echo esc_attr( $hvn_tab_key ); ?>"
						data-hvn-theme-department="<?php echo esc_attr( $hvn_dept_slug ); ?>"
						data-hvn-theme-count="<?php echo esc_attr( (string) $hvn_dept_count ); ?>"
						role="tab"
						aria-selected="<?php echo $hvn_is_active ? 'true' : 'false'; ?>"
					><?php echo $hvn_realty_search_tab_icon( $hvn_dept_slug ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $hvn_tab['label'] ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="hvn-realty-console-body">
			<div class="hvn-realty-console-fields">
				<?php
				foreach ( $hvn_fields_config as $hvn_field_row ) {
					if ( empty( $hvn_field_row['enabled'] ) || 'primary' !== $hvn_field_row['zone'] ) {
						continue;
					}
					if ( function_exists( 'hvn_realty_render_home_search_field' ) ) {
						hvn_realty_render_home_search_field( $hvn_field_row, $hvn_render_context );
					}
				}
				?>
			</div>

			<?php if ( $hvn_has_advanced ) : ?>
				<div class="hvn-realty-adv-panel" id="hvn-theme-home-search-advanced" hidden>
					<div class="hvn-realty-adv-inner">
						<?php
						foreach ( $hvn_fields_config as $hvn_field_row ) {
							if ( empty( $hvn_field_row['enabled'] ) || 'advanced' !== $hvn_field_row['zone'] ) {
								continue;
							}
							if ( function_exists( 'hvn_realty_render_home_search_field' ) ) {
								hvn_realty_render_home_search_field( $hvn_field_row, $hvn_render_context );
							}
						}
						?>
					</div>
				</div>
			<?php endif; ?>

			<div class="hvn-realty-search-submit">
				<button type="submit" aria-label="<?php esc_attr_e( 'Search', 'havenlytics-realty' ); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
				</button>
			</div>
		</div>

		<div class="hvn-realty-console-foot">
			<div class="hvn-realty-match-count">
				<span class="hvn-realty-pulse"></span>
				<b data-hvn-theme-search-count><?php echo esc_html( number_format_i18n( $hvn_listing_count ) ); ?></b>&nbsp;<?php esc_html_e( 'homes match these filters', 'havenlytics-realty' ); ?>
			</div>
			<?php if ( $hvn_has_advanced ) : ?>
				<button type="button" class="hvn-realty-adv-toggle" id="hvn-theme-home-search-more" aria-expanded="false" aria-controls="hvn-theme-home-search-advanced">
					<?php esc_html_e( 'Advanced Filters', 'havenlytics-realty' ); ?>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
				</button>
			<?php endif; ?>
		</div>
		</div>
	</form>
</div>
</section>
