<?php
/**
 * Reusable global header action buttons.
 *
 * One component used across every header context (default header, homepage
 * header, and mobile menu). Driven entirely by the Customizer so the buttons
 * stay consistent site-wide.
 *
 * @package Havenlytics_Realty
 *
 * @var array $args {
 *     @type string $context Visual context: default|home|mobile. Default 'default'.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_context = isset( $args['context'] ) ? sanitize_key( $args['context'] ) : 'default';

if ( ! function_exists( 'hvn_realty_get_header_action_buttons' ) || ! hvn_realty_show_header_actions( $hvn_context ) ) {
	return;
}

$hvn_buttons = hvn_realty_get_header_action_buttons( $hvn_context );
if ( empty( $hvn_buttons ) ) {
	return;
}

$hvn_target = hvn_realty_header_actions_open_new_tab() ? ' target="_blank" rel="noopener noreferrer"' : '';

$hvn_variant_classes = array(
	'default' => array(
		'primary'   => 'hvn-theme-btn',
		'secondary' => 'hvn-theme-btn hvn-theme-btn-outline',
	),
	'home'    => array(
		'primary'   => 'hvn-theme-home-btn hvn-theme-home-btn--gold',
		'secondary' => 'hvn-theme-home-btn hvn-theme-home-btn--ghost',
	),
	'mobile'  => array(
		'primary'   => 'hvn-theme-btn hvn-theme-btn-block',
		'secondary' => 'hvn-theme-btn hvn-theme-btn-outline hvn-theme-btn-block',
	),
);

$hvn_classes = isset( $hvn_variant_classes[ $hvn_context ] ) ? $hvn_variant_classes[ $hvn_context ] : $hvn_variant_classes['default'];
?>
<div class="hvn-theme-header-actions hvn-theme-header-actions--<?php echo esc_attr( $hvn_context ); ?>">
	<?php
	foreach ( $hvn_buttons as $hvn_button ) :
		$hvn_variant = isset( $hvn_button['variant'] ) ? $hvn_button['variant'] : 'primary';
		$hvn_class   = isset( $hvn_classes[ $hvn_variant ] ) ? $hvn_classes[ $hvn_variant ] : $hvn_classes['primary'];
		?>
		<a
			class="<?php echo esc_attr( $hvn_class ); ?> hvn-theme-header-actions__btn hvn-theme-header-actions__btn--<?php echo esc_attr( $hvn_variant ); ?>"
			href="<?php echo esc_url( $hvn_button['url'] ); ?>"
			<?php if ( '' !== $hvn_target ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
		>
			<?php echo esc_html( $hvn_button['label'] ); ?>
		</a>
	<?php endforeach; ?>
</div>
