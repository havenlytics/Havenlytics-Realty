<?php
/**
 * Homepage hero search — property department tabs.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$departments = function_exists( 'hvn_realty_get_property_departments' ) ? hvn_realty_get_property_departments() : array();

if ( empty( $departments ) ) {
	return;
}

$tabs_hidden = ! (bool) get_theme_mod( 'hvn_realty_show_hero_department_tabs', true );
$tabs_label  = function_exists( 'hvn_realty_get_hero_search_tabs_label' ) ? hvn_realty_get_hero_search_tabs_label() : '';
?>
<div class="hvn-realty-hero-search__tabs" data-hvn-realty-hero-search-tabs<?php echo $tabs_hidden ? ' hidden' : ''; ?>>
	<p class="hvn-realty-hero-search__tabs-label" id="hvn-realty-hero-search-tabs-label"<?php echo '' === $tabs_label ? ' hidden' : ''; ?>>
		<?php echo esc_html( $tabs_label ); ?>
	</p>

	<nav class="hvn-realty-hero-search__tabs-nav" role="tablist" aria-label="<?php esc_attr_e( 'Property departments', 'havenlytics-realty' ); ?>">
		<button
			type="button"
			class="hvn-realty-search-tab active"
			role="tab"
			aria-selected="true"
			data-department=""
		>
			<?php esc_html_e( 'All', 'havenlytics-realty' ); ?>
		</button>

		<?php foreach ( $departments as $term ) : ?>
			<?php
			if ( ! is_object( $term ) || ! isset( $term->slug, $term->name ) ) {
				continue;
			}
			?>
			<button
				type="button"
				class="hvn-realty-search-tab"
				role="tab"
				aria-selected="false"
				data-department="<?php echo esc_attr( $term->slug ); ?>"
			>
				<?php echo esc_html( $term->name ); ?>
			</button>
		<?php endforeach; ?>
	</nav>
</div>
