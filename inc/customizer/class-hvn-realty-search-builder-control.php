<?php
/**
 * Customizer Hero Search Builder control.
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
 * Drag-and-drop field manager for the homepage hero search form.
 */
class HVN_Realty_Customize_Search_Builder_Control extends WP_Customize_Control {

	/**
	 * Control type.
	 *
	 * @var string
	 */
	public $type = 'hvn_realty_search_builder';

	/**
	 * Render control content.
	 *
	 * @return void
	 */
	public function render_content() {
		$fields   = $this->get_fields();
		$registry = function_exists( 'hvn_realty_get_home_search_field_registry' )
			? hvn_realty_get_home_search_field_registry()
			: array();
		?>
		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif; ?>
		<?php if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>

		<?php
		HVN_Realty_Customizer_UI::open_control(
			'search-builder',
			array(
				'data-hvn-realty-search-builder-control' => '1',
			)
		);
		HVN_Realty_Customizer_UI::render_hidden_input( $this, 'hvn-realty-search-builder-control__value', (string) $this->value() );
		?>

		<ul class="hvn-realty-cx__list hvn-realty-search-builder-control__list" data-hvn-realty-cx-sortable>
			<?php foreach ( $fields as $field ) : ?>
				<?php $this->render_field_card( $field, $registry ); ?>
			<?php endforeach; ?>
		</ul>

		<?php HVN_Realty_Customizer_UI::close_control(); ?>

		<script type="text/html" class="hvn-realty-search-builder-control__template">
			<?php
			$this->render_field_card(
				array(
					'id'          => '{{id}}',
					'enabled'     => true,
					'zone'        => 'primary',
					'label'       => '',
					'placeholder' => '',
					'default'     => '',
					'required'    => false,
					'width'       => '1',
				),
				$registry,
				true
			);
			?>
		</script>
		<?php
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private function get_fields() {
		$decoded = json_decode( (string) $this->value(), true );

		if ( ! is_array( $decoded ) || empty( $decoded ) ) {
			return function_exists( 'hvn_realty_get_default_home_search_fields_config' )
				? hvn_realty_get_default_home_search_fields_config()
				: array();
		}

		if ( function_exists( 'hvn_realty_sanitize_home_search_fields' ) ) {
			$clean = json_decode( hvn_realty_sanitize_home_search_fields( $decoded ), true );
			return is_array( $clean ) ? $clean : array();
		}

		return $decoded;
	}

	/**
	 * Render one field configuration card.
	 *
	 * @param array<string, mixed>                $field    Field row.
	 * @param array<string, array<string, mixed>> $registry Field registry.
	 * @param bool                                  $collapsed Start collapsed.
	 * @return void
	 */
	private function render_field_card( array $field, array $registry, $collapsed = true ) {
		$id = isset( $field['id'] ) ? sanitize_key( (string) $field['id'] ) : '';
		if ( '' === $id || ! isset( $registry[ $id ] ) ) {
			return;
		}

		if ( function_exists( 'hvn_realty_home_search_field_is_supported' ) && ! hvn_realty_home_search_field_is_supported( $id ) ) {
			return;
		}

		$meta        = $registry[ $id ];
		$enabled     = ! empty( $field['enabled'] );
		$zone        = isset( $field['zone'] ) ? (string) $field['zone'] : 'primary';
		$label       = isset( $field['label'] ) ? (string) $field['label'] : '';
		$placeholder = isset( $field['placeholder'] ) ? (string) $field['placeholder'] : '';
		$default     = isset( $field['default'] ) ? (string) $field['default'] : '';
		$required    = ! empty( $field['required'] );
		$width       = isset( $field['width'] ) ? (string) $field['width'] : '1';

		$display = '' !== $label ? $label : ( isset( $meta['label'] ) ? (string) $meta['label'] : $id );

		$card_class = 'hvn-realty-cx__card hvn-realty-search-builder-control__item';
		if ( $collapsed ) {
			$card_class .= ' is-collapsed';
		}
		if ( ! $enabled ) {
			$card_class .= ' is-disabled';
		}
		?>
		<li class="<?php echo esc_attr( $card_class ); ?>" data-field-id="<?php echo esc_attr( $id ); ?>">
			<div class="hvn-realty-cx__card-head">
				<?php HVN_Realty_Customizer_UI::render_handle(); ?>
				<div class="hvn-realty-cx__card-head-main">
					<span class="hvn-realty-cx__card-title" data-hvn-cx-card-title><?php echo esc_html( $display ); ?></span>
					<span class="hvn-realty-cx__card-meta" data-hvn-cx-card-meta><?php echo esc_html( $id ); ?></span>
				</div>
				<label class="hvn-realty-search-builder-control__toggle">
					<input type="checkbox" class="hvn-realty-search-builder-control__enabled" data-field="enabled" <?php checked( $enabled ); ?> />
					<span><?php esc_html_e( 'Enable', 'havenlytics-realty' ); ?></span>
				</label>
				<div class="hvn-realty-cx__card-actions">
					<?php HVN_Realty_Customizer_UI::render_toggle( ! $collapsed ); ?>
				</div>
			</div>
			<div class="hvn-realty-cx__card-body">
				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Label', 'havenlytics-realty' ); ?></label>
					<div class="hvn-realty-cx__field-control">
						<input type="text" data-field="label" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php echo esc_attr( isset( $meta['label'] ) ? (string) $meta['label'] : '' ); ?>" />
					</div>
				</div>
				<?php if ( ! empty( $meta['supports_placeholder'] ) ) : ?>
					<div class="hvn-realty-cx__field">
						<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Placeholder', 'havenlytics-realty' ); ?></label>
						<div class="hvn-realty-cx__field-control">
							<input type="text" data-field="placeholder" value="<?php echo esc_attr( $placeholder ); ?>" placeholder="<?php echo esc_attr( isset( $meta['placeholder'] ) ? (string) $meta['placeholder'] : '' ); ?>" />
						</div>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $meta['supports_default'] ) ) : ?>
					<div class="hvn-realty-cx__field">
						<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Default value', 'havenlytics-realty' ); ?></label>
						<div class="hvn-realty-cx__field-control">
							<input type="text" data-field="default" value="<?php echo esc_attr( $default ); ?>" />
						</div>
					</div>
				<?php endif; ?>
				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Panel', 'havenlytics-realty' ); ?></label>
					<div class="hvn-realty-cx__field-control">
						<select data-field="zone">
							<option value="primary" <?php selected( $zone, 'primary' ); ?>><?php esc_html_e( 'Main row', 'havenlytics-realty' ); ?></option>
							<option value="advanced" <?php selected( $zone, 'advanced' ); ?>><?php esc_html_e( 'More Filters', 'havenlytics-realty' ); ?></option>
						</select>
					</div>
				</div>
				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label"><?php esc_html_e( 'Width', 'havenlytics-realty' ); ?></label>
					<div class="hvn-realty-cx__field-control">
						<select data-field="width">
							<option value="1" <?php selected( $width, '1' ); ?>><?php esc_html_e( '1 column', 'havenlytics-realty' ); ?></option>
							<option value="2" <?php selected( $width, '2' ); ?>><?php esc_html_e( '2 columns', 'havenlytics-realty' ); ?></option>
							<option value="full" <?php selected( $width, 'full' ); ?>><?php esc_html_e( 'Full width', 'havenlytics-realty' ); ?></option>
						</select>
					</div>
				</div>
				<div class="hvn-realty-cx__field">
					<label class="hvn-realty-cx__field-label hvn-realty-search-builder-control__required">
						<input type="checkbox" data-field="required" <?php checked( $required ); ?> />
						<?php esc_html_e( 'Required', 'havenlytics-realty' ); ?>
					</label>
				</div>
			</div>
		</li>
		<?php
	}
}
