<?php
/**
 * Theme footer.
 *
 * @package Havenlytics_Realty
 */
?>
	</div><!-- #content -->

	<footer id="colophon" class="hvn-modern-footer hvn-theme-footer hvn-theme-footer--modern">
		<div class="hvn-modern-footer__inner hvn-theme-container">
			<div class="hvn-theme-footer-grid">
				<?php get_template_part( 'template-parts/footer/brand' ); ?>
				<?php get_template_part( 'template-parts/footer/widgets' ); ?>
			</div>
			<?php get_template_part( 'template-parts/footer/site-info' ); ?>
		</div>
	</footer>
</div><!-- #page -->

<?php get_template_part( 'template-parts/footer/back-to-top' ); ?>

<?php wp_footer(); ?>
</body>
</html>
