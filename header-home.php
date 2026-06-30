<?php
/**
 * Homepage header (Havenlytics Realty 2.0.0).
 *
 * Rebuilt from the havenlytics-realty.html prototype. Reuses only dynamic
 * WordPress functionality: the custom logo / site identity, the primary nav
 * menu location, mobile navigation, and theme hooks. Loaded exclusively by
 * front-page.php via get_header( 'home' ) so the rest of the site keeps its
 * existing header.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_theme_home_walker = function_exists( 'hvn_realty_get_nav_menu_walker' ) ? hvn_realty_get_nav_menu_walker() : null;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/header/preloader' ); ?>

<a class="skip-link screen-reader-text" href="#primary">
	<?php esc_html_e( 'Skip to content', 'havenlytics-realty' ); ?>
</a>

<header class="hvn-theme-home-header" id="hvn-theme-home-header">
	<div class="hvn-theme-home-container hvn-theme-home-header__inner">
		<?php if ( has_custom_logo() ) : ?>
			<div class="hvn-theme-home-logo"><?php the_custom_logo(); ?></div>
		<?php else : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hvn-theme-home-logo" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<svg class="hvn-theme-home-logo__mark" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M5 16L17 6L29 16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M8 14V28H26V14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M14 28V20H20V28" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<?php bloginfo( 'name' ); ?>
			</a>
		<?php endif; ?>

		<nav id="site-navigation" class="hvn-theme-home-nav" aria-label="<?php esc_attr_e( 'Primary', 'havenlytics-realty' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'hvn-theme-home-menu hvn-theme-nav-menu',
						'fallback_cb'    => '__return_false',
						'depth'          => 3,
						'walker'         => $hvn_theme_home_walker,
					)
				);
			}
			?>
		</nav>

		<div class="hvn-theme-home-header__actions">
			<?php get_template_part( 'template-parts/header/header-actions', null, array( 'context' => 'home' ) ); ?>
			<button type="button" class="hvn-theme-home-burger" id="hvn-theme-home-burger" aria-label="<?php esc_attr_e( 'Open menu', 'havenlytics-realty' ); ?>" aria-expanded="false" aria-controls="hvn-theme-home-mobile" aria-haspopup="true">
				<span></span>
			</button>
		</div>
	</div>
</header>

<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>
