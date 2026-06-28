<?php
/**
 * Homepage fallback when the Havenlytics plugin is inactive.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_install_url = function_exists( 'hvn_realty_get_plugin_install_url' )
	? hvn_realty_get_plugin_install_url()
	: admin_url( 'plugin-install.php?s=havenlytics&tab=search&type=term' );
?>
<section class="hvn-theme-home-section hvn-theme-home-fallback">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-fallback__inner">
			<h1 class="hvn-theme-home-fallback__title">
				<?php esc_html_e( 'Havenlytics Realty', 'havenlytics-realty' ); ?>
			</h1>
			<p class="hvn-theme-home-fallback__text">
				<?php esc_html_e( 'Install and activate the Havenlytics plugin to unlock the full real estate homepage with property search, listings, agents, and more.', 'havenlytics-realty' ); ?>
			</p>
			<?php if ( current_user_can( 'install_plugins' ) ) : ?>
				<p class="hvn-theme-home-fallback__actions">
					<a class="hvn-theme-home-btn hvn-theme-home-btn--primary" href="<?php echo esc_url( $hvn_install_url ); ?>">
						<?php esc_html_e( 'Install Havenlytics Plugin', 'havenlytics-realty' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
