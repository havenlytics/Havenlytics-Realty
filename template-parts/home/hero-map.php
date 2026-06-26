<?php
/**
 * Homepage: Full-width property map hero.
 *
 * @package Havenlytics_Realty
 */

$map_posts = absint( get_theme_mod( 'hvn_realty_home_map_posts', 500 ) );
$map_posts = max( 12, min( 500, $map_posts ) );

$hero_map = hvn_realty_render_home_hero_map( $map_posts );

if ( '' === $hero_map ) {
	return;
}

$show_hero_search = function_exists( 'hvn_realty_show_hero_search_panel' ) && hvn_realty_show_hero_search_panel();
$hero_classes     = 'hvn-realty-section hvn-realty-section--hero';

if ( $show_hero_search ) {
	$hero_classes .= ' hvn-realty-section--hero-has-search';
}

$hero_section_attrs = $show_hero_search
	? ' aria-labelledby="hvn-realty-hero-search-title"'
	: ' aria-label="' . esc_attr__( 'Interactive property map', 'havenlytics-realty' ) . '"';
?>
<section id="hvn-realty-section-hero" class="<?php echo esc_attr( $hero_classes ); ?>"<?php echo $hero_section_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( ! $show_hero_search && function_exists( 'hvn_realty_get_hero_search_title' ) ) : ?>
		<h1 class="screen-reader-text"><?php echo esc_html( hvn_realty_get_hero_search_title() ); ?></h1>
	<?php endif; ?>
	<?php if ( $show_hero_search ) : ?>
		<div class="hvn-realty-hero-search" id="hvn-realty-hero-search" aria-labelledby="hvn-realty-hero-search-title">
			<?php get_template_part( 'template-parts/home/hero-search-panel' ); ?>
		</div>
	<?php endif; ?>

	<div class="hvn-realty-hero__map hvn-realty-home-map-embed" id="hvn-realty-hero-map">
		<?php echo $hero_map; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
</section>
