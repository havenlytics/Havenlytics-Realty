<?php

/**

 * Homepage hero map — uses Havenlytics map API, not the archive shortcode.

 *

 * @package Havenlytics_Realty

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



/**

 * Render the homepage hero property map.

 *

 * Outputs map-only markup via hvnly_render_map_container() plus minimal hidden

 * scaffolding the plugin map script expects (#hvnly-property-grid, load-more meta).

 *

 * @param int $posts_per_page Max properties to load on the map (capped at 500).

 * @return string

 */

function hvn_realty_render_home_hero_map( $posts_per_page = 500 ) {

	if ( ! hvn_realty_is_havenlytics_plugin_active() || ! function_exists( 'hvnly_render_map_container' ) ) {

		return '';

	}



	$posts_per_page   = max( 12, min( 500, absint( $posts_per_page ) ) );

	$department_slugs = function_exists( 'hvn_realty_get_home_map_department_slugs' ) ? hvn_realty_get_home_map_department_slugs() : array();

	$found_posts      = function_exists( 'hvn_realty_get_property_count_for_map' )

		? hvn_realty_get_property_count_for_map( $department_slugs )

		: hvn_realty_get_property_count();

	$instance_id      = 'hvn-realty-home-hero';

	$max_pages          = $found_posts > 0 ? (int) ceil( $found_posts / $posts_per_page ) : 1;



	ob_start();

	?>

	<div class="hvn-realty-home-map" id="hvn-realty-home-map-shell" data-hvn-realty-map-instance="<?php echo esc_attr( $instance_id ); ?>"<?php echo ! empty( $department_slugs ) ? ' data-map-departments="' . esc_attr( wp_json_encode( array_values( $department_slugs ) ) ) . '"' : ''; ?>>

		<?php if ( ! empty( $department_slugs ) ) : ?>

			<div class="hvn-realty-home-map-filters" aria-hidden="true" hidden>

				<?php if ( 1 === count( $department_slugs ) ) : ?>

					<input type="hidden" id="department" name="department" value="<?php echo esc_attr( $department_slugs[0] ); ?>" />

				<?php endif; ?>

				<?php foreach ( $department_slugs as $slug ) : ?>

					<input type="checkbox" name="hvnly_prop_depts[]" value="<?php echo esc_attr( $slug ); ?>" checked="checked" tabindex="-1" aria-hidden="true" />

				<?php endforeach; ?>

			</div>

		<?php endif; ?>



		<div id="hvnly-property-grid" class="hvnly-property-grid-view map-view" aria-hidden="true" hidden></div>



		<div id="hvnly-load-more-container" class="hvnly-load-more-wrapper" aria-hidden="true" hidden>

			<div

				id="hvnly-load-more-<?php echo esc_attr( $instance_id ); ?>"

				class="hvnly-property-load-more-container"

				data-instance-id="<?php echo esc_attr( $instance_id ); ?>"

				data-current-page="1"

				data-max-pages="<?php echo esc_attr( (string) $max_pages ); ?>"

				data-posts-per-page="<?php echo esc_attr( (string) $posts_per_page ); ?>"

				data-per-page="<?php echo esc_attr( (string) $posts_per_page ); ?>"

				data-found-posts="<?php echo esc_attr( (string) $found_posts ); ?>"

			></div>

		</div>



		<div id="hvnly-map-placeholder" class="hvnly-map-placeholder hvn-realty-home-map__canvas">

			<?php hvnly_render_map_container(); ?>

		</div>

	</div>

	<?php

	return (string) ob_get_clean();

}


