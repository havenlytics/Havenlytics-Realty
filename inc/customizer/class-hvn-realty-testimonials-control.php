<?php

/**

 * Customizer testimonials repeater control.

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

 * Repeater control for homepage testimonials.

 */

class HVN_Realty_Customize_Testimonials_Control extends WP_Customize_Control {



	/**

	 * Control type.

	 *

	 * @var string

	 */

	public $type = 'hvn_realty_testimonials';



	/**

	 * Render control content.

	 *

	 * @return void

	 */

	public function render_content() {

		$items = $this->get_items();

		?>

		<?php if ( ! empty( $this->label ) ) : ?>

			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>

		<?php endif; ?>

		<?php if ( ! empty( $this->description ) ) : ?>

			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>

		<?php endif; ?>



		<?php

		HVN_Realty_Customizer_UI::open_control(

			'repeater',

			array(

				'data-hvn-realty-testimonials-control' => '1',

			)

		);

		HVN_Realty_Customizer_UI::render_hidden_input( $this, 'hvn-realty-testimonials-control__value', (string) $this->value() );

		?>



		<div class="hvn-realty-cx__list hvn-realty-testimonials-control__list" data-hvn-realty-cx-sortable>

			<?php foreach ( $items as $index => $item ) : ?>

				<?php $this->render_item( $index, $item ); ?>

			<?php endforeach; ?>

		</div>



		<div class="hvn-realty-cx__toolbar">

			<?php

			HVN_Realty_Customizer_UI::render_button(

				__( 'Add testimonial', 'havenlytics-realty' ),

				'hvn-realty-cx__btn hvn-realty-cx__btn--primary hvn-realty-testimonials-control__add',

				array(

					'data-hvn-cx-action' => 'add',

				)

			);

			?>

		</div>



		<?php HVN_Realty_Customizer_UI::close_control(); ?>



		<script type="text/html" class="hvn-realty-testimonials-control__template">

			<?php $this->render_item( '{{index}}', array( 'name' => '', 'position' => '', 'text' => '', 'rating' => 5, 'avatar_id' => 0 ), true ); ?>

		</script>

		<?php

	}



	/**

	 * @return array<int, array<string, mixed>>

	 */

	private function get_items() {

		$decoded = json_decode( (string) $this->value(), true );



		if ( ! is_array( $decoded ) || empty( $decoded ) ) {

			return function_exists( 'hvn_realty_get_default_home_testimonials' )

				? hvn_realty_get_default_home_testimonials()

				: array();

		}



		return $decoded;

	}



	/**

	 * Render one testimonial row.

	 *

	 * @param int|string           $index   Index.

	 * @param array<string, mixed> $item    Item data.

	 * @param bool                 $collapsed Whether the card starts collapsed.

	 * @return void

	 */

	private function render_item( $index, $item, $collapsed = false ) {

		$name       = isset( $item['name'] ) ? (string) $item['name'] : '';

		$position   = isset( $item['position'] ) ? (string) $item['position'] : '';

		$text       = isset( $item['text'] ) ? (string) $item['text'] : '';

		$rating     = isset( $item['rating'] ) ? absint( $item['rating'] ) : 5;

		$avatar_id  = isset( $item['avatar_id'] ) ? absint( $item['avatar_id'] ) : 0;

		$avatar_url = $avatar_id > 0 ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' ) : '';

		$title      = '' !== $name

			? $name

			: sprintf(

				/* translators: %d: testimonial number */

				__( 'Testimonial %d', 'havenlytics-realty' ),

				is_numeric( $index ) ? ( (int) $index + 1 ) : 1

			);

		$card_class = 'hvn-realty-cx__card hvn-realty-testimonials-control__item';

		if ( $collapsed ) {

			$card_class .= ' is-collapsed';

		}

		?>

		<div class="<?php echo esc_attr( $card_class ); ?>" data-index="<?php echo esc_attr( (string) $index ); ?>">

			<div class="hvn-realty-cx__card-head">

				<?php HVN_Realty_Customizer_UI::render_handle(); ?>



				<div class="hvn-realty-cx__card-head-main">

					<span class="hvn-realty-cx__card-icon-wrap">

						<?php HVN_Realty_Customizer_UI::render_icon( 'dashicons-format-quote' ); ?>

					</span>



					<div class="hvn-realty-cx__card-title-wrap">

						<p class="hvn-realty-cx__card-title" data-hvn-cx-card-title><?php echo esc_html( $title ); ?></p>

						<p class="hvn-realty-cx__card-meta" data-hvn-cx-card-meta><?php echo esc_html( $position ); ?></p>

					</div>



					<span class="hvn-realty-cx__rating-badge" data-hvn-cx-rating-badge>

						<?php HVN_Realty_Customizer_UI::render_icon( 'dashicons-star-filled' ); ?>

						<?php echo esc_html( (string) $rating . '/5' ); ?>

					</span>

				</div>



				<div class="hvn-realty-cx__card-actions">
					<?php HVN_Realty_Customizer_UI::render_toggle( ! $collapsed ); ?>
					<button
						type="button"
						class="hvn-realty-cx__btn hvn-realty-cx__btn--icon hvn-realty-testimonials-control__duplicate"
						aria-label="<?php esc_attr_e( 'Duplicate testimonial', 'havenlytics-realty' ); ?>"
					>
						<?php HVN_Realty_Customizer_UI::render_icon( 'dashicons-admin-page' ); ?>
					</button>
					<button
						type="button"
						class="hvn-realty-cx__btn hvn-realty-cx__btn--icon hvn-realty-cx__btn--danger hvn-realty-testimonials-control__remove"
						aria-label="<?php esc_attr_e( 'Remove testimonial', 'havenlytics-realty' ); ?>"
					>
						<?php HVN_Realty_Customizer_UI::render_icon( 'dashicons-trash' ); ?>
					</button>
				</div>

			</div>



			<div class="hvn-realty-cx__card-body">

				<div class="hvn-realty-cx__field">

					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Name', 'havenlytics-realty' ); ?></label>

					<div class="hvn-realty-cx__field-control">

						<input type="text" data-field="name" value="<?php echo esc_attr( $name ); ?>" />

					</div>

				</div>



				<div class="hvn-realty-cx__field">

					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Position / location', 'havenlytics-realty' ); ?></label>

					<div class="hvn-realty-cx__field-control">

						<input type="text" data-field="position" value="<?php echo esc_attr( $position ); ?>" />

					</div>

				</div>



				<div class="hvn-realty-cx__field">

					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Review text', 'havenlytics-realty' ); ?></label>

					<div class="hvn-realty-cx__field-control">

						<textarea rows="3" data-field="text"><?php echo esc_textarea( $text ); ?></textarea>

					</div>

				</div>



				<div class="hvn-realty-cx__field">

					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Rating', 'havenlytics-realty' ); ?></label>

					<div class="hvn-realty-cx__field-control">

						<select data-field="rating">

							<?php for ( $star = 5; $star >= 1; $star-- ) : ?>

								<option value="<?php echo esc_attr( (string) $star ); ?>" <?php selected( $rating, $star ); ?>><?php echo esc_html( (string) $star ); ?></option>

							<?php endfor; ?>

						</select>

					</div>

				</div>



				<div class="hvn-realty-cx__field">

					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Avatar image', 'havenlytics-realty' ); ?></label>

					<div class="hvn-realty-cx__field-control">

						<input type="hidden" data-field="avatar_id" value="<?php echo esc_attr( (string) $avatar_id ); ?>" />

						<div class="hvn-realty-cx__media-row">

							<span class="hvn-realty-cx__preview hvn-realty-testimonials-control__preview">

								<?php if ( $avatar_url ) : ?>

									<img src="<?php echo esc_url( $avatar_url ); ?>" alt="" />

								<?php endif; ?>

							</span>

							<?php

							HVN_Realty_Customizer_UI::render_button(

								__( 'Select image', 'havenlytics-realty' ),

								'hvn-realty-cx__btn hvn-realty-cx__btn--ghost hvn-realty-testimonials-control__upload'

							);

							HVN_Realty_Customizer_UI::render_button(

								__( 'Remove', 'havenlytics-realty' ),

								'hvn-realty-cx__btn hvn-realty-cx__btn--danger hvn-realty-testimonials-control__clear-avatar',

								array(

									'hidden' => $avatar_id ? null : true,

								)

							);

							?>

						</div>

					</div>

				</div>

			</div>

		</div>

		<?php

	}

}


