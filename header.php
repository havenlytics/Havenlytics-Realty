<?php
/**
 * Theme header.
 *
 * @package Havenlytics_Realty
 */

$header_classes = array( 'hvn-modern-header', 'hvn-theme-header', 'hvn-theme-header--modern' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?><?php hvn_realty_body_layout_attrs(); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/header/preloader' ); ?>

<div id="page" class="hvn-theme-site hvn-modern-site">
	<a class="skip-link screen-reader-text" href="#primary">
		<?php esc_html_e( 'Skip to content', 'havenlytics-realty' ); ?>
	</a>

	<header id="masthead" class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>">
		<div class="hvn-modern-header__inner hvn-theme-container">
			<div class="hvn-modern-header__row hvn-theme-header-row">
				<?php get_template_part( 'template-parts/header/branding' ); ?>
				<?php get_template_part( 'template-parts/header/navigation' ); ?>
				<?php get_template_part( 'template-parts/header/actions' ); ?>
			</div>
		</div>
	</header>

	<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>
	<?php get_template_part( 'template-parts/header/property-search-panel' ); ?>

	<div id="content" class="hvn-theme-content">
