<?php

/**

 * Customizer homepage section order control.

 *

 * @package Havenlytics_Realty

 */



if ( ! defined( 'ABSPATH' ) ) {

	exit;

}



if ( ! class_exists( 'WP_Customize_Control' ) ) {

	return;

}



/**

 * Drag-and-drop section order control.

 */

class HVN_Realty_Customize_Section_Order_Control extends WP_Customize_Control {



	/**

	 * Control type.

	 *

	 * @var string

	 */

	public $type = 'hvn_realty_section_order';



	/**

	 * Render control content.

	 *

	 * @return void

	 */

	public function render_content() {

		$order  = function_exists( 'hvn_realty_get_home_section_order_for_control' )

			? hvn_realty_get_home_section_order_for_control()

			: array();

		$labels = function_exists( 'hvn_realty_get_home_section_labels' )

			? hvn_realty_get_home_section_labels()

			: array();

		$value  = '' !== (string) $this->value()

			? (string) $this->value()

			: wp_json_encode( $order );

		?>

		<?php if ( ! empty( $this->label ) ) : ?>

			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

		<?php endif; ?>

		<?php if ( ! empty( $this->description ) ) : ?>

			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>

		<?php endif; ?>



		<?php

		HVN_Realty_Customizer_UI::open_control(

			'sortable',

			array(

				'data-hvn-realty-section-order-control' => '1',

			)

		);

		HVN_Realty_Customizer_UI::render_hidden_input( $this, 'hvn-realty-section-order-control__value', $value );

		?>



		<ul class="hvn-realty-cx__list hvn-realty-section-order-control__list" data-hvn-realty-cx-sortable>

			<?php foreach ( $order as $slug ) : ?>

				<?php

				$label = isset( $labels[ $slug ] ) ? $labels[ $slug ] : $slug;

				$icon  = HVN_Realty_Customizer_UI::get_section_icon( $slug );

				?>

				<li class="hvn-realty-cx__card hvn-realty-section-order-control__item" data-slug="<?php echo esc_attr( $slug ); ?>">

					<div class="hvn-realty-cx__card-head">

						<?php HVN_Realty_Customizer_UI::render_handle(); ?>



						<div class="hvn-realty-cx__card-head-main">

							<span class="hvn-realty-cx__card-icon-wrap">

								<?php HVN_Realty_Customizer_UI::render_icon( $icon ); ?>

							</span>



							<div class="hvn-realty-cx__card-title-wrap">

								<p class="hvn-realty-cx__card-title"><?php echo esc_html( $label ); ?></p>

							</div>

						</div>

					</div>

				</li>

			<?php endforeach; ?>

		</ul>



		<p class="hvn-realty-cx__hint"><?php esc_html_e( 'Drag sections to reorder the homepage. Publish to apply changes.', 'havenlytics-realty' ); ?></p>



		<?php HVN_Realty_Customizer_UI::close_control(); ?>

		<?php

	}

}


