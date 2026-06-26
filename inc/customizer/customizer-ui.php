<?php
/**
 * Havenlytics Customizer UI framework helpers.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared markup and icons for Havenlytics Customizer controls.
 */
class HVN_Realty_Customizer_UI {

	/**
	 * Open a Havenlytics control shell.
	 *
	 * @param string               $type  Control variant: repeater|sortable.
	 * @param array<string, mixed> $attrs Extra attributes for the root element.
	 * @return void
	 */
	public static function open_control( $type, $attrs = array() ) {
		$type = sanitize_key( $type );
		$attr = array_merge(
			array(
				'class'                 => 'hvn-realty-cx hvn-realty-cx--' . $type,
				'data-hvn-realty-cx'    => $type,
			),
			$attrs
		);

		echo '<div ' . self::build_attributes( $attr ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="hvn-realty-cx__stack">';
	}

	/**
	 * Close a Havenlytics control shell.
	 *
	 * @return void
	 */
	public static function close_control() {
		echo '</div></div>';
	}

	/**
	 * Render a linked hidden input for Customizer settings.
	 *
	 * @param WP_Customize_Control $control Control instance.
	 * @param string               $class   Input class.
	 * @param string               $value   Current value.
	 * @return void
	 */
	public static function render_hidden_input( $control, $class, $value ) {
		?>
		<input
			type="hidden"
			<?php $control->link(); ?>
			value="<?php echo esc_attr( $value ); ?>"
			class="<?php echo esc_attr( $class ); ?> hvn-realty-cx__value"
		/>
		<?php
	}

	/**
	 * Dashicon class for a homepage section slug.
	 *
	 * @param string $slug Section slug.
	 * @return string
	 */
	public static function get_section_icon( $slug ) {
		$icons = array(
			'hero-map'            => 'dashicons-location-alt',
			'featured-properties' => 'dashicons-star-filled',
			'department-tabs'     => 'dashicons-category',
			'property-taxonomies' => 'dashicons-tag',
			'property-types'      => 'dashicons-building',
			'featured-agents'     => 'dashicons-businessman',
			'featured-agencies'   => 'dashicons-groups',
			'testimonials'        => 'dashicons-format-quote',
			'latest-posts'        => 'dashicons-admin-post',
			'cta-banner'          => 'dashicons-megaphone',
		);

		$slug = sanitize_key( $slug );

		return isset( $icons[ $slug ] ) ? $icons[ $slug ] : 'dashicons-screenoptions';
	}

	/**
	 * Render a dashicon span.
	 *
	 * @param string $icon_class Dashicon class.
	 * @param string $extra_class Optional extra class.
	 * @return void
	 */
	public static function render_icon( $icon_class, $extra_class = '' ) {
		$classes = trim( 'hvn-realty-cx__icon dashicons ' . sanitize_html_class( $icon_class ) . ' ' . sanitize_html_class( $extra_class ) );
		echo '<span class="' . esc_attr( $classes ) . '" aria-hidden="true"></span>';
	}

	/**
	 * Render a card drag handle.
	 *
	 * @return void
	 */
	public static function render_handle() {
		?>
		<button type="button" class="hvn-realty-cx__handle" aria-label="<?php esc_attr_e( 'Drag to reorder', 'havenlytics-realty' ); ?>">
			<?php self::render_icon( 'dashicons-menu' ); ?>
		</button>
		<?php
	}

	/**
	 * Render collapse toggle button.
	 *
	 * @param bool $expanded Whether the card starts expanded.
	 * @return void
	 */
	public static function render_toggle( $expanded = true ) {
		?>
		<button
			type="button"
			class="hvn-realty-cx__toggle"
			aria-expanded="<?php echo $expanded ? 'true' : 'false'; ?>"
			aria-label="<?php esc_attr_e( 'Toggle item', 'havenlytics-realty' ); ?>"
		>
			<?php self::render_icon( 'dashicons-arrow-down-alt2' ); ?>
		</button>
		<?php
	}

	/**
	 * Render a framework action button.
	 *
	 * @param string               $label   Button label.
	 * @param string               $class   Button class suffix.
	 * @param array<string, mixed> $attrs   Extra attributes.
	 * @return void
	 */
	public static function render_button( $label, $class = '', $attrs = array() ) {
		$classes = trim( 'hvn-realty-cx__btn ' . $class );
		$attr    = array_merge(
			array(
				'type'  => 'button',
				'class' => $classes,
			),
			$attrs
		);

		printf(
			'<button %1$s>%2$s</button>',
			self::build_attributes( $attr ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $label )
		);
	}

	/**
	 * Build an HTML attribute string.
	 *
	 * @param array<string, mixed> $attrs Attributes.
	 * @return string
	 */
	public static function build_attributes( $attrs ) {
		$parts = array();

		foreach ( $attrs as $key => $value ) {
			if ( null === $value || false === $value ) {
				continue;
			}

			$key = sanitize_key( $key );

			if ( true === $value ) {
				$parts[] = esc_attr( $key );
				continue;
			}

			$parts[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
		}

		return implode( ' ', $parts );
	}
}
