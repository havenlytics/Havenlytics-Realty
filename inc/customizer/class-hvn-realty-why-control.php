<?php
/**
 * Customizer "Why Choose Us" repeater control.
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
 * Repeater control for homepage Why-Choose items.
 */
class HVN_Realty_Customize_Why_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'hvn_realty_why';

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
				'data-hvn-realty-why-control' => '1',
			)
		);
		HVN_Realty_Customizer_UI::render_hidden_input( $this, 'hvn-realty-why-control__value', (string) $this->value() );
		?>

		<div class="hvn-realty-cx__list hvn-realty-why-control__list" data-hvn-realty-cx-sortable>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php $this->render_item( $index, $item ); ?>
			<?php endforeach; ?>
		</div>

		<div class="hvn-realty-cx__toolbar">
			<?php
			HVN_Realty_Customizer_UI::render_button(
				__( 'Add item', 'havenlytics-realty' ),
				'hvn-realty-cx__btn hvn-realty-cx__btn--primary hvn-realty-why-control__add',
				array(
					'data-hvn-cx-action' => 'add',
				)
			);
			?>
		</div>

		<?php HVN_Realty_Customizer_UI::close_control(); ?>

		<script type="text/html" class="hvn-realty-why-control__template">
			<?php $this->render_item( '{{index}}', array( 'icon' => 'shield', 'title' => '', 'text' => '', 'url' => '' ), true ); ?>
		</script>
		<?php
	}

	/**
	 * @return array<int, array<string, string>>
	 */
	private function get_items() {
		$decoded = json_decode( (string) $this->value(), true );

		if ( ! is_array( $decoded ) || empty( $decoded ) ) {
			return function_exists( 'hvn_realty_get_default_home_why_items' )
				? hvn_realty_get_default_home_why_items()
				: array();
		}

		return $decoded;
	}

	/**
	 * Render one item row.
	 *
	 * @param int|string           $index     Index.
	 * @param array<string, string> $item      Item data.
	 * @param bool                 $collapsed Whether the card starts collapsed.
	 * @return void
	 */
	private function render_item( $index, $item, $collapsed = false ) {
		$icon  = isset( $item['icon'] ) ? (string) $item['icon'] : 'shield';
		$title = isset( $item['title'] ) ? (string) $item['title'] : '';
		$text  = isset( $item['text'] ) ? (string) $item['text'] : '';
		$url   = isset( $item['url'] ) ? (string) $item['url'] : '';

		$choices = function_exists( 'hvn_realty_get_why_icon_choices' ) ? hvn_realty_get_why_icon_choices() : array( 'shield' => 'Shield' );

		$display_title = '' !== $title
			? $title
			: sprintf(
				/* translators: %d: item number */
				__( 'Feature %d', 'havenlytics-realty' ),
				is_numeric( $index ) ? ( (int) $index + 1 ) : 1
			);

		$card_class = 'hvn-realty-cx__card hvn-realty-why-control__item';
		if ( $collapsed ) {
			$card_class .= ' is-collapsed';
		}
		?>
		<div class="<?php echo esc_attr( $card_class ); ?>" data-index="<?php echo esc_attr( (string) $index ); ?>">
			<div class="hvn-realty-cx__card-head">
				<?php HVN_Realty_Customizer_UI::render_handle(); ?>

				<div class="hvn-realty-cx__card-head-main">
					<span class="hvn-realty-cx__card-icon-wrap">
						<?php HVN_Realty_Customizer_UI::render_icon( 'dashicons-awards' ); ?>
					</span>
					<div class="hvn-realty-cx__card-title-wrap">
						<p class="hvn-realty-cx__card-title" data-hvn-cx-card-title><?php echo esc_html( $display_title ); ?></p>
						<p class="hvn-realty-cx__card-meta" data-hvn-cx-card-meta><?php echo esc_html( $text ); ?></p>
					</div>
				</div>

				<div class="hvn-realty-cx__card-actions">
					<?php HVN_Realty_Customizer_UI::render_toggle( ! $collapsed ); ?>
					<button
						type="button"
						class="hvn-realty-cx__btn hvn-realty-cx__btn--icon hvn-realty-why-control__duplicate"
						aria-label="<?php esc_attr_e( 'Duplicate item', 'havenlytics-realty' ); ?>"
					>
						<?php HVN_Realty_Customizer_UI::render_icon( 'dashicons-admin-page' ); ?>
					</button>
					<button
						type="button"
						class="hvn-realty-cx__btn hvn-realty-cx__btn--icon hvn-realty-cx__btn--danger hvn-realty-why-control__remove"
						aria-label="<?php esc_attr_e( 'Remove item', 'havenlytics-realty' ); ?>"
					>
						<?php HVN_Realty_Customizer_UI::render_icon( 'dashicons-trash' ); ?>
					</button>
				</div>
			</div>

			<div class="hvn-realty-cx__card-body">
				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Icon', 'havenlytics-realty' ); ?></label>
					<div class="hvn-realty-cx__field-control">
						<select data-field="icon">
							<?php foreach ( $choices as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Title', 'havenlytics-realty' ); ?></label>
					<div class="hvn-realty-cx__field-control">
						<input type="text" data-field="title" value="<?php echo esc_attr( $title ); ?>" />
					</div>
				</div>

				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Description', 'havenlytics-realty' ); ?></label>
					<div class="hvn-realty-cx__field-control">
						<textarea rows="3" data-field="text"><?php echo esc_textarea( $text ); ?></textarea>
					</div>
				</div>

				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Link URL (optional)', 'havenlytics-realty' ); ?></label>
					<div class="hvn-realty-cx__field-control">
						<input type="url" data-field="url" value="<?php echo esc_attr( $url ); ?>" placeholder="https://" />
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
