<?php
/**
 * Site logo / branding template part.
 *
 * @package Havenlytics_Realty
 */
?>
<div class="hvn-theme-logo">
	<?php if ( has_custom_logo() ) : ?>
		<div class="hvn-theme-custom-logo">
			<?php the_custom_logo(); ?>
		</div>
	<?php else : ?>
	<?php
	$use_site_title_h1 = is_front_page() && is_home();
	if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {
		$use_site_title_h1 = false;
	}
	if ( $use_site_title_h1 ) :
		?>
			<h1 class="hvn-theme-site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</h1>
		<?php else : ?>
			<p class="hvn-theme-site-title">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
			</p>
		<?php endif; ?>
		<?php
		$description = get_bloginfo( 'description', 'display' );
		if ( $description || is_customize_preview() ) :
			?>
			<p class="hvn-theme-site-description"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</div>
