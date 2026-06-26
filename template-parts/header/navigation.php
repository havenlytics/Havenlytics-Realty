<?php
/**
 * Primary navigation template part.
 *
 * @package Havenlytics_Realty
 */
?>
<nav id="site-navigation" class="hvn-theme-nav" aria-label="<?php esc_attr_e( 'Main Navigation', 'havenlytics-realty' ); ?>">
	<?php
	if ( has_nav_menu( 'primary' ) ) {
		$walker = function_exists( 'hvn_realty_get_nav_menu_walker' ) ? hvn_realty_get_nav_menu_walker() : null;

		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'hvn-theme-nav-menu',
				'container'      => false,
				'fallback_cb'    => '__return_false',
				'depth'          => 3,
				'walker'         => $walker,
			)
		);
	} else {
		hvn_realty_primary_menu_fallback();
	}
	?>
</nav>
