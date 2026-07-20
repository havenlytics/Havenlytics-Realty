<?php
/**
 * Global mobile menu (slide-in panel).
 *
 * One mobile menu shared across the entire theme — homepage, property pages,
 * search, blog, archives, 404 and every other page. Loaded by both the homepage
 * header (header-home.php) and the internal header (header.php) so the markup,
 * styling and behaviour are identical everywhere. Auth / CTA actions render
 * below the nav when enabled (smart auth + List a Property).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_mobile_walker = function_exists( 'hvn_realty_get_nav_menu_walker' ) ? hvn_realty_get_nav_menu_walker() : null;
?>
<div class="hvn-theme-home-mobile-overlay" id="hvn-theme-home-mobile-overlay" aria-hidden="true" hidden></div>
<div class="hvn-theme-home-mobile" id="hvn-theme-home-mobile" aria-hidden="true">
	<div class="hvn-theme-home-mobile__top">
		<?php if ( has_custom_logo() ) : ?>
			<div class="hvn-theme-home-logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hvn-theme-home-logo" rel="home"><?php bloginfo( 'name' ); ?></a>
		<?php endif; ?>
		<button class="hvn-theme-home-mobile__close" id="hvn-theme-home-mobile-close" type="button" aria-label="<?php esc_attr_e( 'Close menu', 'havenlytics-realty' ); ?>">
			<svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M2 2L16 16M16 2L2 16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
		</button>
	</div>
	<nav class="hvn-theme-home-mobile__nav" aria-label="<?php esc_attr_e( 'Mobile', 'havenlytics-realty' ); ?>">
		<?php
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'hvn-theme-home-mobile__links hvn-theme-home-mobile-nav-menu',
					'fallback_cb'    => '__return_false',
					'depth'          => 3,
					'walker'         => $hvn_mobile_walker,
				)
			);
		} elseif ( function_exists( 'hvn_realty_mobile_menu_fallback' ) ) {
			hvn_realty_mobile_menu_fallback();
		}
		?>
	</nav>
	<div class="hvn-theme-home-mobile__actions">
		<?php get_template_part( 'template-parts/header/header-actions', null, array( 'context' => 'mobile' ) ); ?>
	</div>
	<?php get_template_part( 'template-parts/footer/social-links', null, array( 'context' => 'mobile' ) ); ?>
</div>
