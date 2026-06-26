<?php
/**
 * Homepage fallback when Havenlytics plugin is inactive.
 *
 * @package Havenlytics_Realty
 */

$install_url = function_exists( 'hvn_realty_get_plugin_install_url' )
	? hvn_realty_get_plugin_install_url()
	: admin_url( 'plugin-install.php?s=havenlytics&tab=search&type=term' );
?>
<section class="hvn-realty-home-section hvn-realty-home-fallback">
	<div class="hvn-theme-container">
		<div class="hvn-realty-home-fallback__inner">
			<h1 class="hvn-realty-home-fallback__title">
				<?php esc_html_e( 'Havenlytics Realty', 'havenlytics-realty' ); ?>
			</h1>
			<p class="hvn-realty-home-fallback__text">
				<?php esc_html_e( 'Install and activate the Havenlytics plugin to unlock the full real estate homepage with property search, listings, agents, and more.', 'havenlytics-realty' ); ?>
			</p>
			<?php if ( current_user_can( 'install_plugins' ) ) : ?>
				<p class="hvn-realty-home-fallback__actions">
					<a class="hvn-realty-home-btn hvn-realty-home-btn--primary" href="<?php echo esc_url( $install_url ); ?>">
						<?php esc_html_e( 'Install Havenlytics Plugin', 'havenlytics-realty' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
