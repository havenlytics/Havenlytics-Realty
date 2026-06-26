<?php
/**
 * Homepage hero map — floating property search card.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_show_hero_search_panel' ) || ! hvn_realty_show_hero_search_panel() ) {
	return;
}

$show_tabs = function_exists( 'hvn_realty_show_hero_department_tabs' ) && hvn_realty_show_hero_department_tabs();
$subtitle  = function_exists( 'hvn_realty_get_hero_search_subtitle' ) ? hvn_realty_get_hero_search_subtitle() : '';
?>
<div class="hvn-realty-hero-search__card">
	<?php if ( ! $show_tabs && '' === $subtitle ) : ?>
		<p class="hvn-realty-hero-search__eyebrow"><?php esc_html_e( 'Property search', 'havenlytics-realty' ); ?></p>
	<?php endif; ?>

	<h1 class="hvn-realty-hero-search__title" id="hvn-realty-hero-search-title">
		<?php echo esc_html( hvn_realty_get_hero_search_title() ); ?>
	</h1>

	<?php if ( '' !== $subtitle ) : ?>
		<p class="hvn-realty-hero-search__subtitle" id="hvn-realty-hero-search-subtitle">
			<?php echo esc_html( $subtitle ); ?>
		</p>
	<?php else : ?>
		<p class="hvn-realty-hero-search__subtitle" id="hvn-realty-hero-search-subtitle" hidden></p>
	<?php endif; ?>

	<?php get_template_part( 'template-parts/home/hero-search-tabs' ); ?>

	<?php
	get_template_part(
		'template-parts/header/property-search-form',
		null,
		array(
			'context'     => 'hero',
			'button_text' => hvn_realty_get_hero_search_button_text(),
		)
	);
	?>
</div>
