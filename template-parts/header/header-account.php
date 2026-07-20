<?php
/**
 * Smart header auth + favorites (Havenlytics Workspace integration).
 *
 * Guest: Login (Workspace). Heart only when favorites exist.
 * Logged in: My Account dropdown + Heart when favorites exist.
 *
 * @package Havenlytics_Realty
 *
 * @var array $args {
 *     @type string $context Visual context: default|home|mobile.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'hvn_realty_should_show_smart_header_auth' ) || ! hvn_realty_should_show_smart_header_auth() ) {
	return;
}

$hvn_context = isset( $args['context'] ) ? sanitize_key( $args['context'] ) : 'default';
$hvn_logged  = is_user_logged_in();
$hvn_is_home = ( 'home' === $hvn_context );
$hvn_is_mob  = ( 'mobile' === $hvn_context );

$hvn_btn_ghost = $hvn_is_home ? 'hvn-theme-home-btn hvn-theme-home-btn--ghost' : 'hvn-theme-btn hvn-theme-btn-outline';
if ( $hvn_is_mob ) {
	$hvn_btn_ghost = 'hvn-theme-btn hvn-theme-btn-outline hvn-theme-btn-block';
}

$hvn_login_url   = function_exists( 'hvn_realty_get_workspace_login_url' ) ? hvn_realty_get_workspace_login_url() : '';
$hvn_saved_url   = function_exists( 'hvn_realty_get_saved_properties_url' ) ? hvn_realty_get_saved_properties_url() : '';
$hvn_workspace   = function_exists( 'hvn_realty_workspace_available' ) && hvn_realty_workspace_available();
$hvn_can_auth    = function_exists( 'hvn_realty_can_build_workspace_urls' ) && hvn_realty_can_build_workspace_urls();
$hvn_show_fav    = function_exists( 'hvn_realty_favorites_available' ) && hvn_realty_favorites_available();
$hvn_fav_count   = ( $hvn_logged && function_exists( 'hvn_realty_get_favorites_count' ) ) ? hvn_realty_get_favorites_count() : 0;
$hvn_fav_preview = ( $hvn_logged && function_exists( 'hvn_realty_get_header_favorite_previews' ) ) ? hvn_realty_get_header_favorite_previews( 3 ) : array();
$hvn_menu_items  = ( $hvn_logged && function_exists( 'hvn_realty_get_account_menu_items' ) ) ? hvn_realty_get_account_menu_items() : array();
$hvn_user        = $hvn_logged ? wp_get_current_user() : null;
$hvn_display     = ( $hvn_user && $hvn_user->exists() ) ? $hvn_user->display_name : '';
$hvn_fav_visible = $hvn_fav_count > 0;

if ( ! $hvn_show_fav && ! $hvn_can_auth ) {
	return;
}
?>
<div class="hvn-theme-header-account hvn-theme-header-account--<?php echo esc_attr( $hvn_context ); ?>" data-hvn-header-account<?php echo $hvn_logged ? ' data-hvn-logged-in="1"' : ''; ?>>
	<?php if ( $hvn_show_fav ) : ?>
		<div class="hvn-theme-header-fav" data-hvn-header-fav<?php echo $hvn_fav_visible ? '' : ' hidden'; ?>>
			<button
				type="button"
				class="hvn-theme-header-fav__toggle"
				aria-expanded="false"
				aria-controls="hvn-theme-header-fav-panel-<?php echo esc_attr( $hvn_context ); ?>"
				aria-label="<?php esc_attr_e( 'Favorites', 'havenlytics-realty' ); ?>"
				data-hvn-header-fav-toggle
			>
				<span class="hvn-theme-header-fav__icon" aria-hidden="true">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
				</span>
				<span class="hvn-theme-header-fav__count" data-hvn-header-fav-count><?php echo $hvn_fav_visible ? esc_html( (string) $hvn_fav_count ) : ''; ?></span>
			</button>
			<div
				id="hvn-theme-header-fav-panel-<?php echo esc_attr( $hvn_context ); ?>"
				class="hvn-theme-header-fav__panel"
				role="region"
				aria-label="<?php esc_attr_e( 'Saved properties', 'havenlytics-realty' ); ?>"
				hidden
				data-hvn-header-fav-panel
			>
				<p class="hvn-theme-header-fav__heading"><?php esc_html_e( 'Saved Properties', 'havenlytics-realty' ); ?></p>
				<ul class="hvn-theme-header-fav__list" data-hvn-header-fav-list>
					<?php foreach ( $hvn_fav_preview as $hvn_item ) : ?>
						<li>
							<a href="<?php echo esc_url( $hvn_item['url'] ); ?>">
								<?php if ( ! empty( $hvn_item['thumb'] ) ) : ?>
									<img src="<?php echo esc_url( $hvn_item['thumb'] ); ?>" alt="" width="40" height="40" loading="lazy" decoding="async">
								<?php endif; ?>
								<span><?php echo esc_html( $hvn_item['title'] ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $hvn_saved_url ) : ?>
					<a class="hvn-theme-header-fav__all" href="<?php echo esc_url( $hvn_saved_url ); ?>"><?php esc_html_e( 'View All Saved Properties', 'havenlytics-realty' ); ?></a>
				<?php elseif ( ! $hvn_logged && $hvn_login_url ) : ?>
					<a class="hvn-theme-header-fav__all" href="<?php echo esc_url( $hvn_login_url ); ?>"><?php esc_html_e( 'Login to sync favorites', 'havenlytics-realty' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $hvn_workspace && $hvn_logged && ! empty( $hvn_menu_items ) ) : ?>
		<div class="hvn-theme-header-user" data-hvn-header-user>
			<button
				type="button"
				class="hvn-theme-header-user__toggle <?php echo esc_attr( $hvn_btn_ghost ); ?>"
				aria-expanded="false"
				aria-haspopup="true"
				aria-controls="hvn-theme-header-user-menu-<?php echo esc_attr( $hvn_context ); ?>"
				data-hvn-header-user-toggle
			>
				<span class="hvn-theme-header-user__label"><?php esc_html_e( 'My Account', 'havenlytics-realty' ); ?></span>
				<?php if ( $hvn_display && ! $hvn_is_mob ) : ?>
					<span class="screen-reader-text"><?php echo esc_html( $hvn_display ); ?></span>
				<?php endif; ?>
				<svg class="hvn-theme-header-user__caret" width="12" height="12" viewBox="0 0 12 12" aria-hidden="true"><path d="M2.5 4.5L6 8l3.5-3.5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<ul
				id="hvn-theme-header-user-menu-<?php echo esc_attr( $hvn_context ); ?>"
				class="hvn-theme-header-user__menu"
				role="menu"
				hidden
				data-hvn-header-user-menu
			>
				<?php foreach ( $hvn_menu_items as $hvn_item ) : ?>
					<li role="none">
						<a
							role="menuitem"
							class="hvn-theme-header-user__item<?php echo ( isset( $hvn_item['type'] ) && 'logout' === $hvn_item['type'] ) ? ' hvn-theme-header-user__item--logout' : ''; ?>"
							href="<?php echo esc_url( $hvn_item['url'] ); ?>"
						><?php echo esc_html( $hvn_item['label'] ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php elseif ( $hvn_can_auth && $hvn_login_url ) : ?>
		<a class="<?php echo esc_attr( $hvn_btn_ghost ); ?> hvn-theme-header-account__link" href="<?php echo esc_url( $hvn_login_url ); ?>"><?php esc_html_e( 'Login', 'havenlytics-realty' ); ?></a>
	<?php endif; ?>
</div>
