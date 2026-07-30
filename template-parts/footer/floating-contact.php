<?php
/**
 * Floating contact action menu (site-wide).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_should_show_floating_contact' ) || ! hvn_realty_should_show_floating_contact() ) {
	return;
}

$hvn_items = function_exists( 'hvn_realty_get_floating_contact_items' )
	? hvn_realty_get_floating_contact_items()
	: array();

if ( empty( $hvn_items ) ) {
	return;
}

/**
 * Inline SVG for contact icons.
 *
 * @param string $icon Icon key.
 * @return string
 */
$hvn_fab_icon = static function ( $icon ) {
	switch ( $icon ) {
		case 'whatsapp':
			return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>';
		case 'phone':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>';
		case 'email':
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>';
		default:
			return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9"/></svg>';
	}
};
?>
<div
	class="hvn-realty-fab"
	id="hvnRealtyFab"
	data-hvn-realty-fab
>
	<ul
		class="hvn-realty-fab__menu"
		id="hvnRealtyFabMenu"
		role="menu"
		aria-label="<?php esc_attr_e( 'Contact options', 'havenlytics-realty' ); ?>"
		hidden
	>
		<?php foreach ( $hvn_items as $hvn_index => $hvn_item ) : ?>
			<?php
			$hvn_id    = isset( $hvn_item['id'] ) ? sanitize_key( (string) $hvn_item['id'] ) : 'item-' . (int) $hvn_index;
			$hvn_label = isset( $hvn_item['label'] ) ? (string) $hvn_item['label'] : '';
			$hvn_href  = isset( $hvn_item['href'] ) ? (string) $hvn_item['href'] : '';
			$hvn_icon  = isset( $hvn_item['icon'] ) ? (string) $hvn_item['icon'] : $hvn_id;
			if ( '' === $hvn_href || '' === $hvn_label ) {
				continue;
			}
			$hvn_is_external = ( 0 === strpos( $hvn_href, 'http' ) );
			?>
			<li
				class="hvn-realty-fab__item"
				role="none"
				style="--hvn-realty-fab-i: <?php echo esc_attr( (string) (int) $hvn_index ); ?>"
			>
				<a
					class="hvn-realty-fab__link hvn-realty-fab__link--<?php echo esc_attr( $hvn_id ); ?>"
					role="menuitem"
					href="<?php echo esc_url( $hvn_href ); ?>"
					<?php echo $hvn_is_external ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
				>
					<span class="hvn-realty-fab__icon" aria-hidden="true"><?php echo $hvn_fab_icon( $hvn_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="hvn-realty-fab__label"><?php echo esc_html( $hvn_label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<button
		type="button"
		class="hvn-realty-fab__toggle"
		id="hvnRealtyFabToggle"
		aria-expanded="false"
		aria-controls="hvnRealtyFabMenu"
		aria-label="<?php esc_attr_e( 'Open contact menu', 'havenlytics-realty' ); ?>"
		data-hvn-realty-fab-open-label="<?php esc_attr_e( 'Open contact menu', 'havenlytics-realty' ); ?>"
		data-hvn-realty-fab-close-label="<?php esc_attr_e( 'Close contact menu', 'havenlytics-realty' ); ?>"
	>
		<span class="hvn-realty-fab__toggle-icon hvn-realty-fab__toggle-icon--plus" aria-hidden="true">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
		</span>
		<span class="screen-reader-text"><?php esc_html_e( 'Contact', 'havenlytics-realty' ); ?></span>
	</button>
</div>
