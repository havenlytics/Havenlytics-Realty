<?php
/**
 * Reusable social profile icon links.
 *
 * Renders the theme social profiles (Customizer-managed) as accessible icon
 * links. Used in the footer brand block and the mobile menu. Renders nothing
 * when no social profiles are configured.
 *
 * @package Havenlytics_Realty
 *
 * @var array $args {
 *     @type string $context Visual context for styling hooks. Default 'footer'.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_get_social_links' ) ) {
	return;
}

$hvn_social = hvn_realty_get_social_links();
if ( empty( $hvn_social ) ) {
	return;
}

$hvn_social_context = isset( $args['context'] ) ? sanitize_key( $args['context'] ) : 'footer';

$hvn_social_icons = array(
	'facebook'  => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>',
	'instagram' => '<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>',
	'twitter'   => '<path d="M4 4l7.7 10.3L4.5 20H7l5.5-5.9L17 20h3l-8-10.7L19.5 4H17l-4.8 5.2L8 4z"/>',
	'linkedin'  => '<path d="M16 8a6 6 0 0 1 6 6v6h-4v-6a2 2 0 0 0-4 0v6h-4v-10h4v1.5A6 6 0 0 1 16 8z"/><rect x="2" y="9" width="4" height="11"/><circle cx="4" cy="4" r="2"/>',
	'youtube'   => '<path d="M22 8.5a3 3 0 0 0-2.1-2.1C18 5.8 12 5.8 12 5.8s-6 0-7.9.6A3 3 0 0 0 2 8.5 31 31 0 0 0 1.6 12 31 31 0 0 0 2 15.5a3 3 0 0 0 2.1 2.1C6 18.2 12 18.2 12 18.2s6 0 7.9-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 22.4 12 31 31 0 0 0 22 8.5z"/><polygon points="10 9.2 15 12 10 14.8"/>',
);
?>
<div class="hvn-theme-social-links hvn-theme-social-links--<?php echo esc_attr( $hvn_social_context ); ?>">
	<?php
	foreach ( $hvn_social as $hvn_network => $hvn_profile ) :
		$hvn_icon = isset( $hvn_social_icons[ $hvn_network ] ) ? $hvn_social_icons[ $hvn_network ] : '<circle cx="12" cy="12" r="9"/>';
		?>
		<a class="hvn-theme-social-links__item hvn-theme-social-links__item--<?php echo esc_attr( $hvn_network ); ?>" href="<?php echo esc_url( $hvn_profile['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $hvn_profile['label'] ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" aria-hidden="true" focusable="false">
				<?php echo $hvn_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</svg>
		</a>
	<?php endforeach; ?>
</div>
