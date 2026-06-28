<?php
/**
 * Footer brand block — logo, description and social icons.
 *
 * Renders as the first footer column. Everything is dynamic: the custom logo /
 * site identity, a Customizer description (falls back to the site tagline) and
 * the reusable social links component.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_footer_description = (string) get_theme_mod( 'hvn_realty_footer_description', '' );
if ( '' === trim( $hvn_footer_description ) ) {
	$hvn_footer_description = (string) get_bloginfo( 'description' );
}
?>
<div class="hvn-theme-footer-col hvn-theme-footer-brand">
	<?php if ( has_custom_logo() ) : ?>
		<div class="hvn-theme-footer-brand__logo"><?php the_custom_logo(); ?></div>
	<?php else : ?>
		<a class="hvn-theme-footer-brand__title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php bloginfo( 'name' ); ?>
		</a>
	<?php endif; ?>

	<?php if ( '' !== trim( $hvn_footer_description ) ) : ?>
		<p class="hvn-theme-footer-brand__text"><?php echo wp_kses_post( $hvn_footer_description ); ?></p>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/footer/social-links', null, array( 'context' => 'footer' ) ); ?>
</div>
