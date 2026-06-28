<?php
/**
 * Footer bottom — copyright (left) + footer-bottom menu (right).
 *
 * The footer-bottom menu location (Privacy / Terms / Sitemap) is Customizer and
 * menu editable. When no menu is assigned, the theme credit is shown instead so
 * attribution is never lost.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_has_footer_bottom_menu = has_nav_menu( 'footer-bottom' );
?>
<div class="hvn-theme-footer-bottom">
	<div class="hvn-theme-copyright">
		<?php echo hvn_realty_get_copyright_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<?php if ( $hvn_has_footer_bottom_menu ) : ?>
		<nav class="hvn-theme-footer-bottom-nav" aria-label="<?php esc_attr_e( 'Footer bottom', 'havenlytics-realty' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer-bottom',
					'container'      => false,
					'menu_class'     => 'hvn-theme-footer-bottom-menu',
					'depth'          => 1,
					'fallback_cb'    => '__return_false',
				)
			);
			?>
		</nav>
	<?php else : ?>
		<div class="hvn-theme-credit">
			<?php
			printf(
				/* translators: %s: WordPress */
				esc_html__( 'Powered by %s', 'havenlytics-realty' ),
				'<a href="' . esc_url( __( 'https://havenlytics.com/', 'havenlytics-realty' ) ) . '">Havenlytics.com</a>'
			);
			?>
		</div>
	<?php endif; ?>
</div>
