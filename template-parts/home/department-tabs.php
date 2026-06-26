<?php

/**

 * Homepage: Properties by department tabs (3 columns × 6).

 *

 * @package Havenlytics_Realty

 */



$count       = absint( get_theme_mod( 'hvn_realty_home_latest_count', 6 ) );

$count       = max( 6, min( 12, $count ) );

$columns     = 3;

$view_all_url  = hvn_realty_get_home_department_button_url();
$view_all_text = hvn_realty_get_home_department_button_text();

$departments = hvn_realty_get_property_departments();



if ( empty( $departments ) ) {

	$departments = array(

		(object) array(

			'slug'  => '',

			'name'  => __( 'All Properties', 'havenlytics-realty' ),

			'count' => hvn_realty_get_property_count(),

		),

	);

}

?>

<section id="hvn-realty-section-departments" class="hvn-realty-section hvn-realty-section--departments" aria-labelledby="hvn-realty-departments-title">

	<div class="hvn-realty-container">

		<?php

		hvn_realty_home_section_heading(
			hvn_realty_get_home_section_title( 'department', __( 'Browse by Department', 'havenlytics-realty' ) ),
			hvn_realty_get_home_section_subtitle( 'department', __( 'Explore listings organized by property department.', 'havenlytics-realty' ) ),
			'hvn-realty-section__header',
			'hvn-realty-departments-title'
		);

		?>



		<div class="hvn-realty-dept-tabs" data-hvn-realty-dept-tabs>

			<nav class="hvn-realty-dept-tabs__nav" role="tablist" aria-label="<?php esc_attr_e( 'Property departments', 'havenlytics-realty' ); ?>">

				<?php foreach ( $departments as $index => $term ) : ?>

					<?php

					$slug       = is_object( $term ) && isset( $term->slug ) ? (string) $term->slug : '';

					$label      = is_object( $term ) && isset( $term->name ) ? (string) $term->name : __( 'All Properties', 'havenlytics-realty' );

					$dept_count = is_object( $term ) && isset( $term->count ) ? absint( $term->count ) : hvn_realty_get_property_count();

					$tab_id     = 'hvn-realty-dept-tab-' . ( $slug ? sanitize_key( $slug ) : 'all' );

					$panel_id   = 'hvn-realty-dept-panel-' . ( $slug ? sanitize_key( $slug ) : 'all' );

					$is_active  = 0 === $index;

					?>

					<button

						type="button"

						class="hvn-realty-dept-tabs__btn<?php echo $is_active ? ' is-active' : ''; ?>"

						id="<?php echo esc_attr( $tab_id ); ?>"

						role="tab"

						aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"

						aria-controls="<?php echo esc_attr( $panel_id ); ?>"

						data-dept-tab="<?php echo esc_attr( $slug ? $slug : 'all' ); ?>"

					>

						<span class="hvn-realty-dept-tabs__label"><?php echo esc_html( $label ); ?></span>

						<span class="hvn-realty-dept-tabs__count" aria-hidden="true"><?php echo esc_html( (string) $dept_count ); ?></span>

						<span class="screen-reader-text">

							<?php

							echo esc_html(

								sprintf(

									/* translators: %d: number of properties in this department */

									_n( '%d property', '%d properties', $dept_count, 'havenlytics-realty' ),

									$dept_count

								)

							);

							?>

						</span>

					</button>

				<?php endforeach; ?>

			</nav>



			<?php foreach ( $departments as $index => $term ) : ?>

				<?php

				$slug      = is_object( $term ) && isset( $term->slug ) ? (string) $term->slug : '';

				$panel_id  = 'hvn-realty-dept-panel-' . ( $slug ? sanitize_key( $slug ) : 'all' );

				$is_active = 0 === $index;



				$shortcode_atts = array(

					'posts_per_page'     => $count,

					'columns'            => $columns,

					'show_pagination'    => 'no',

					'show_results_count' => 'no',

					'orderby'            => 'date',

					'order'              => 'DESC',

					'class'              => 'hvn-realty-home-plugin-slim hvn-realty-dept-tabs__grid',

				);



				if ( $slug ) {

					$shortcode_atts['department'] = $slug;

				}

				?>

				<div

					class="hvn-realty-dept-tabs__panel<?php echo $is_active ? ' is-active' : ''; ?>"

					id="<?php echo esc_attr( $panel_id ); ?>"

					role="tabpanel"

					aria-labelledby="<?php echo esc_attr( 'hvn-realty-dept-tab-' . ( $slug ? sanitize_key( $slug ) : 'all' ) ); ?>"

					data-dept-panel="<?php echo esc_attr( $slug ? $slug : 'all' ); ?>"

					<?php echo $is_active ? '' : ' hidden'; ?>

				>

					<div class="hvn-realty-section__body">

						<?php

						echo hvn_realty_render_shortcode( 'hvnly_property_grid', $shortcode_atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						?>

					</div>

				</div>

			<?php endforeach; ?>

		</div>



		<footer class="hvn-realty-section__footer">

			<a id="hvn-realty-dept-view-all" class="hvn-realty-btn hvn-realty-btn--outline" href="<?php echo esc_url( $view_all_url ); ?>">

				<?php echo esc_html( $view_all_text ); ?>

			</a>

		</footer>

	</div>

</section>

