<?php
/**
 * Homepage footer (Havenlytics Realty 2.0.1).
 *
 * Unified with the site-wide footer so the footer is identical on every page
 * (homepage, blog, archives, single property, search, 404). Renders the shared
 * brand / widget / footer-bottom template parts. Loaded by front-page.php and
 * the Real Estate Homepage template via get_footer( 'home' ).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<footer id="colophon" class="hvn-modern-footer hvn-theme-footer hvn-theme-footer--modern">
		<div class="hvn-modern-footer__inner hvn-theme-container">
			<div class="hvn-theme-footer-grid">
				<?php get_template_part( 'template-parts/footer/brand' ); ?>
				<?php get_template_part( 'template-parts/footer/widgets' ); ?>
			</div>
			<?php get_template_part( 'template-parts/footer/site-info' ); ?>
		</div>
	</footer>

<?php get_template_part( 'template-parts/footer/back-to-top' ); ?>

<?php wp_footer(); ?>
</body>
</html>
