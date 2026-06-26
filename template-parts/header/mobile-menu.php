<?php
/**
 * Mobile menu template part.
 *
 * @package Havenlytics_Realty
 */
?>
<div class="hvn-theme-mobile-menu" id="hvn-mobile-menu" aria-hidden="true">
	<div class="hvn-theme-mobile-menu-header">
		<div class="hvn-theme-mobile-menu-branding">
			<?php get_template_part( 'template-parts/header/branding' ); ?>
		</div>
		<button class="hvn-theme-mobile-menu-close" type="button" aria-label="<?php esc_attr_e( 'Close Menu', 'havenlytics-realty' ); ?>">
			<span aria-hidden="true">&times;</span>
			<span class="screen-reader-text"><?php esc_html_e( 'Close Menu', 'havenlytics-realty' ); ?></span>
		</button>
	</div>
	<div class="hvn-theme-mobile-menu-content">
		<nav aria-label="<?php esc_attr_e( 'Mobile Navigation', 'havenlytics-realty' ); ?>">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_class'     => 'hvn-theme-mobile-nav-menu',
					'container'      => false,
					'fallback_cb'    => '__return_false',
					'depth'          => 3,
				)
			);
		} else {
			hvn_realty_mobile_menu_fallback();
		}
		?>
		</nav>
	</div>
	<?php if ( hvn_realty_show_header_cta() && '' !== hvn_realty_get_header_cta_text() ) : ?>
		<div class="hvn-theme-mobile-menu-cta">
			<a class="hvn-theme-btn hvn-theme-btn-block hvn-theme-mobile-header-cta" href="<?php echo esc_url( hvn_realty_get_header_cta_url() ); ?>">
				<?php echo esc_html( hvn_realty_get_header_cta_text() ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
<div class="hvn-theme-mobile-overlay" aria-hidden="true"></div>
