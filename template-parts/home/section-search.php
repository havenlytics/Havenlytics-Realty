<?php
/**
 * Homepage 2.0.2 — Property search panel (overlaps hero).
 *
 * Fields render from the Search Builder configuration (theme_mod).
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
foreach ( $hvn_department_tabs as $hvn_tab ) {
	if ( ! empty( $hvn_tab['is_default'] ) ) {
		$hvn_default_department = isset( $hvn_tab['department'] ) ? (string) $hvn_tab['department'] : '';
		break;
	}
}

$hvn_has_advanced = function_exists( 'hvn_realty_home_search_has_advanced_fields' )
	? hvn_realty_home_search_has_advanced_fields()
	: false;

$hvn_listing_count = 0;
if ( post_type_exists( 'hvnly_property' ) ) {
	$hvn_counts = wp_count_posts( 'hvnly_property' );
	$hvn_listing_count = isset( $hvn_counts->publish ) ? (int) $hvn_counts->publish : 0;
}
?>
<div class="hvn-theme-home-container">
	<div class="hvn-theme-home-search-wrap hvn-theme-home-reveal" id="hvn-theme-home-search">
		<form class="hvn-theme-home-search" id="hvn-theme-home-search-form" action="<?php echo esc_url( $hvn_search_url ); ?>" method="get" aria-label="<?php esc_attr_e( 'Property search', 'havenlytics-realty' ); ?>">
			<input type="hidden" name="department" id="hvn-theme-home-search-department" value="<?php echo esc_attr( $hvn_default_department ); ?>">
			<input type="hidden" name="view_type" value="grid">
			<input type="hidden" name="paged" value="1">

			<div class="hvn-theme-home-search__tabrow">
				<div class="hvn-theme-home-search__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Listing type', 'havenlytics-realty' ); ?>">
					<?php foreach ( $hvn_department_tabs as $hvn_tab_key => $hvn_tab ) : ?>
						<?php
						$hvn_is_active = ! empty( $hvn_tab['is_default'] );
						$hvn_dept_slug = isset( $hvn_tab['department'] ) ? (string) $hvn_tab['department'] : '';
						?>
						<button
							type="button"
							class="hvn-theme-home-search__tab<?php echo $hvn_is_active ? ' hvn-theme-home-active' : ''; ?>"
							data-hvn-theme-tab="<?php echo esc_attr( $hvn_tab_key ); ?>"
							data-hvn-theme-department="<?php echo esc_attr( $hvn_dept_slug ); ?>"
							role="tab"
							aria-selected="<?php echo $hvn_is_active ? 'true' : 'false'; ?>"
						><?php echo esc_html( $hvn_tab['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
				<?php if ( $hvn_has_advanced ) : ?>
					<button type="button" class="hvn-theme-home-search__more" id="hvn-theme-home-search-more" aria-expanded="false" aria-controls="hvn-theme-home-search-advanced">
						<span><?php esc_html_e( 'More Filters', 'havenlytics-realty' ); ?></span>
						<svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</button>
				<?php endif; ?>
			</div>

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

			<button type="submit" class="hvn-theme-home-btn hvn-theme-home-btn--primary hvn-theme-home-search__submit"><?php esc_html_e( 'Search', 'havenlytics-realty' ); ?></button>

			<?php if ( $hvn_has_advanced ) : ?>
				<div class="hvn-theme-home-search__advanced" id="hvn-theme-home-search-advanced" hidden>
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
			<?php endif; ?>

			<?php if ( $hvn_listing_count > 0 ) : ?>
				<p class="hvn-theme-home-search__note">
					<?php
					printf(
						/* translators: %s: number of listings. */
						esc_html__( 'Searching %s verified listings updated this week.', 'havenlytics-realty' ),
						'<b>' . esc_html( number_format_i18n( $hvn_listing_count ) ) . '</b>'
					);
					?>
				</p>
			<?php endif; ?>
		</form>
	</div>
</div>
