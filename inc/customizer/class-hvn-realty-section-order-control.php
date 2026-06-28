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
 * Drag-and-drop section manager for the homepage.
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
		$order    = $this->get_ordered_slugs();
		$registry = hvn_realty_get_home_section_registry();
		?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<?php
		HVN_Realty_Customizer_UI::open_control(
			'section-order',
			array(
				'data-hvn-realty-section-order-control' => '1',
			)
		);
		HVN_Realty_Customizer_UI::render_hidden_input( $this, 'hvn-realty-section-order-control__value', (string) $this->value() );
		?>

		<ul class="hvn-realty-cx__list hvn-realty-section-order-control__list" data-hvn-realty-cx-sortable>
			<?php foreach ( $order as $slug ) : ?>
				<?php
				if ( ! isset( $registry[ $slug ] ) ) {
					continue;
				}
				$visibility_mod = hvn_realty_get_home_section_visibility_mod( $slug );
				$visible        = (bool) get_theme_mod( $visibility_mod, true );
				?>
				<li class="hvn-realty-cx__card hvn-realty-section-order-control__item" data-slug="<?php echo esc_attr( $slug ); ?>" data-visibility-setting="<?php echo esc_attr( $visibility_mod ); ?>">
					<div class="hvn-realty-cx__card-head">
						<?php HVN_Realty_Customizer_UI::render_handle(); ?>
						<div class="hvn-realty-cx__card-head-main">
							<span class="hvn-realty-cx__card-title"><?php echo esc_html( $registry[ $slug ] ); ?></span>
						</div>
						<label class="hvn-realty-section-order-control__toggle">
							<input type="checkbox" class="hvn-realty-section-order-control__visible" <?php checked( $visible ); ?> />
							<span><?php esc_html_e( 'Show', 'havenlytics-realty' ); ?></span>
						</label>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php HVN_Realty_Customizer_UI::close_control(); ?>
		<?php
	}

	/**
	 * @return string[]
	 */
	private function get_ordered_slugs() {
		$decoded = json_decode( (string) $this->value(), true );
		if ( ! is_array( $decoded ) ) {
			return hvn_realty_get_default_home_section_order();
		}

		$allowed = array_keys( hvn_realty_get_home_section_registry() );
		$order   = array();

		foreach ( $decoded as $slug ) {
			$slug = sanitize_key( (string) $slug );
			if ( in_array( $slug, $allowed, true ) && ! in_array( $slug, $order, true ) ) {
				$order[] = $slug;
			}
		}

		foreach ( $allowed as $slug ) {
			if ( ! in_array( $slug, $order, true ) ) {
				$order[] = $slug;
			}
		}

		return $order;
	}
}
