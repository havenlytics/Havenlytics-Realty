<?php
/**
 * Realty admin — post-setup dashboard overview.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Overview stat cards for the completed-setup dashboard.
 *
 * @return array<int, array<string, mixed>>
 */
function hvn_realty_get_dashboard_overview_stats() {
	$plugin_active  = function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) && hvn_realty_is_havenlytics_plugin_active();
	$plugin_version = function_exists( 'hvn_realty_get_system_plugin_version' ) ? hvn_realty_get_system_plugin_version() : '';
	$home           = function_exists( 'hvn_realty_get_system_home_page_status' ) ? hvn_realty_get_system_home_page_status() : array(
		'value'  => '—',
		'status' => 'neutral',
		'detail' => '',
	);
	$menu           = function_exists( 'hvn_realty_get_system_primary_menu_status' ) ? hvn_realty_get_system_primary_menu_status() : array(
		'value'  => '—',
		'status' => 'neutral',
		'detail' => '',
	);

	$stats = array(
		array(
			'icon'   => 'dashicons-admin-appearance',
			'label'  => __( 'Theme Version', 'havenlytics-realty' ),
			'value'  => defined( 'HVN_REALTY_VERSION' ) ? HVN_REALTY_VERSION : '—',
			'status' => 'success',
			'badge'  => '',
		),
		array(
			'icon'   => 'dashicons-admin-plugins',
			'label'  => __( 'Plugin Version', 'havenlytics-realty' ),
			'value'  => $plugin_version ? $plugin_version : '—',
			'status' => $plugin_active && $plugin_version ? 'success' : 'warning',
			'badge'  => $plugin_active ? __( 'Active', 'havenlytics-realty' ) : __( 'Inactive', 'havenlytics-realty' ),
		),
		array(
			'icon'   => 'dashicons-building',
			'label'  => __( 'Properties', 'havenlytics-realty' ),
			'value'  => function_exists( 'hvn_realty_get_property_count' ) ? number_format_i18n( hvn_realty_get_property_count() ) : '0',
			'status' => 'neutral',
			'badge'  => '',
		),
		array(
			'icon'   => 'dashicons-groups',
			'label'  => __( 'Agents', 'havenlytics-realty' ),
			'value'  => function_exists( 'hvn_realty_get_agent_count' ) ? number_format_i18n( hvn_realty_get_agent_count() ) : '0',
			'status' => 'neutral',
			'badge'  => '',
		),
		array(
			'icon'   => 'dashicons-businessperson',
			'label'  => __( 'Agencies', 'havenlytics-realty' ),
			'value'  => function_exists( 'hvn_realty_get_agency_count' ) ? number_format_i18n( hvn_realty_get_agency_count() ) : '0',
			'status' => 'neutral',
			'badge'  => '',
		),
		array(
			'icon'   => 'dashicons-admin-home',
			'label'  => __( 'Home Page', 'havenlytics-realty' ),
			'value'  => $home['value'],
			'status' => $home['status'],
			'badge'  => $home['detail'],
		),
		array(
			'icon'   => 'dashicons-menu',
			'label'  => __( 'Primary Menu', 'havenlytics-realty' ),
			'value'  => $menu['value'],
			'status' => $menu['status'],
			'badge'  => $menu['detail'],
		),
	);

	return apply_filters( 'hvn_realty_dashboard_overview_stats', $stats );
}

/**
 * Render a single overview stat card.
 *
 * @param array<string, mixed> $stat Stat card data.
 * @return void
 */
function hvn_realty_render_dashboard_overview_stat_card( $stat ) {
	$card_tag = ! empty( $stat['url'] ) ? 'a' : 'article';
	$card_attr = ! empty( $stat['url'] )
		? ' class="hvn-realty-admin__stat-card hvn-realty-admin__stat-card--link" href="' . esc_url( (string) $stat['url'] ) . '"'
		: ' class="hvn-realty-admin__stat-card"';
	?>
	<<?php echo $card_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $card_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="hvn-realty-admin__stat-card-head">
			<span class="hvn-realty-admin__stat-card-icon dashicons <?php echo esc_attr( $stat['icon'] ?? 'dashicons-chart-bar' ); ?>" aria-hidden="true"></span>
			<?php if ( ! empty( $stat['badge'] ) && function_exists( 'hvn_realty_render_system_status_badge' ) ) : ?>
				<?php hvn_realty_render_system_status_badge( $stat['status'] ?? 'neutral', (string) $stat['badge'] ); ?>
			<?php endif; ?>
		</div>
		<p class="hvn-realty-admin__stat-label"><?php echo esc_html( $stat['label'] ?? '' ); ?></p>
		<p class="hvn-realty-admin__stat-value hvn-realty-admin__stat-value--card"><?php echo esc_html( (string) ( $stat['value'] ?? '' ) ); ?></p>
		<?php if ( function_exists( 'hvn_realty_render_system_status_indicator' ) ) : ?>
			<div class="hvn-realty-admin__stat-meta">
				<?php hvn_realty_render_system_status_indicator( $stat['status'] ?? 'neutral' ); ?>
			</div>
		<?php endif; ?>
	</<?php echo $card_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
}

/**
 * Render quick action tiles.
 *
 * @return void
 */
function hvn_realty_render_dashboard_overview_quick_actions() {
	$doc_url     = 'https://havenlytics.com/documentation/';
	$support_url = 'https://havenlytics.com/support/';
	$customize   = admin_url( 'customize.php' );
	?>
	<section class="hvn-realty-admin__actions-row" aria-label="<?php esc_attr_e( 'Quick actions', 'havenlytics-realty' ); ?>">
		<a class="hvn-realty-admin__action-tile" href="<?php echo esc_url( $customize ); ?>">
			<span class="dashicons dashicons-admin-customizer" aria-hidden="true"></span>
			<span><?php esc_html_e( 'Customize', 'havenlytics-realty' ); ?></span>
		</a>
		<button type="button" class="hvn-realty-admin__action-tile hvn-realty-admin__action-tile--button" data-hvn-onboarding-open>
			<span class="dashicons dashicons-video-alt3" aria-hidden="true"></span>
			<span><?php esc_html_e( 'Watch Tutorial', 'havenlytics-realty' ); ?></span>
		</button>
		<a class="hvn-realty-admin__action-tile" href="<?php echo esc_url( $doc_url ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="dashicons dashicons-book" aria-hidden="true"></span>
			<span><?php esc_html_e( 'Documentation', 'havenlytics-realty' ); ?></span>
		</a>
		<a class="hvn-realty-admin__action-tile" href="<?php echo esc_url( $support_url ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="dashicons dashicons-sos" aria-hidden="true"></span>
			<span><?php esc_html_e( 'Support', 'havenlytics-realty' ); ?></span>
		</a>
	</section>
	<?php
}

/**
 * Render the post-setup dashboard overview.
 *
 * @return void
 */
function hvn_realty_render_dashboard_overview() {
	$stats           = hvn_realty_get_dashboard_overview_stats();
	$customize_home  = admin_url( 'customize.php?autofocus[panel]=hvn_realty_homepage_panel' );
	$system_status   = function_exists( 'hvn_realty_get_system_status_url' ) ? hvn_realty_get_system_status_url() : '';
	?>
	<div class="wrap hvn-realty-admin hvn-realty-admin--overview">
		<header class="hvn-realty-admin__hero">
			<div class="hvn-realty-admin__hero-copy">
				<p class="hvn-realty-admin__eyebrow"><?php esc_html_e( 'Havenlytics Realty', 'havenlytics-realty' ); ?></p>
				<h1 class="hvn-realty-admin__title"><?php esc_html_e( 'Your site overview', 'havenlytics-realty' ); ?></h1>
				<p class="hvn-realty-admin__lead">
					<?php esc_html_e( 'Plugin, homepage, and content at a glance. Manage your real estate site from one place.', 'havenlytics-realty' ); ?>
				</p>
			</div>
			<div class="hvn-realty-admin__hero-action">
				<a class="button button-primary button-hero" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'View website', 'havenlytics-realty' ); ?>
				</a>
			</div>
		</header>

		<div class="hvn-realty-admin__banner hvn-realty-admin__banner--success">
			<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
			<div>
				<strong><?php esc_html_e( 'Your site is ready!', 'havenlytics-realty' ); ?></strong>
				<p><?php esc_html_e( 'Plugin, demo import, and homepage configuration are complete.', 'havenlytics-realty' ); ?></p>
			</div>
		</div>

		<section class="hvn-realty-admin__overview-grid" aria-label="<?php esc_attr_e( 'Site overview', 'havenlytics-realty' ); ?>">
			<?php foreach ( $stats as $stat ) : ?>
				<?php hvn_realty_render_dashboard_overview_stat_card( $stat ); ?>
			<?php endforeach; ?>
		</section>

		<?php hvn_realty_render_dashboard_overview_quick_actions(); ?>

		<div class="hvn-realty-admin__layout hvn-realty-admin__layout--overview">
			<section class="hvn-realty-admin__panel hvn-realty-admin__panel--wide">
				<h2><?php esc_html_e( 'Quick links', 'havenlytics-realty' ); ?></h2>
				<ul class="hvn-realty-admin__links hvn-realty-admin__links--columns">
					<li><a href="<?php echo esc_url( $customize_home ); ?>"><?php esc_html_e( 'Homepage settings', 'havenlytics-realty' ); ?></a></li>
					<li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Customize theme', 'havenlytics-realty' ); ?></a></li>
					<?php if ( function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) && hvn_realty_is_havenlytics_plugin_active() ) : ?>
						<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hvnly_property' ) ); ?>"><?php esc_html_e( 'Manage properties', 'havenlytics-realty' ); ?></a></li>
						<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=hvnly_agent' ) ); ?>"><?php esc_html_e( 'Manage agents', 'havenlytics-realty' ); ?></a></li>
					<?php endif; ?>
					<li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Edit menus', 'havenlytics-realty' ); ?></a></li>
					<?php if ( $system_status ) : ?>
						<li><a href="<?php echo esc_url( $system_status ); ?>"><?php esc_html_e( 'System Status', 'havenlytics-realty' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</section>

			<aside class="hvn-realty-admin__sidebar">
				<?php if ( function_exists( 'hvn_realty_render_onboarding_tutorial_card' ) ) : ?>
					<?php hvn_realty_render_onboarding_tutorial_card(); ?>
				<?php endif; ?>

				<?php if ( function_exists( 'hvn_realty_render_admin_support_panels' ) ) : ?>
					<?php hvn_realty_render_admin_support_panels(); ?>
				<?php endif; ?>
			</aside>
		</div>

		<footer class="hvn-realty-admin__footer">
			<p>
				<?php
				printf(
					wp_kses(
						__( 'Official companion theme for the free <a href="%1$s">Havenlytics plugin</a>. <a href="%2$s">View on WordPress.org</a>.', 'havenlytics-realty' ),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url( 'https://wordpress.org/plugins/havenlytics/' ),
					esc_url( 'https://wordpress.org/themes/havenlytics-realty/' )
				);
				?>
			</p>
		</footer>

		<?php if ( function_exists( 'hvn_realty_render_onboarding_modal' ) ) : ?>
			<?php hvn_realty_render_onboarding_modal(); ?>
		<?php endif; ?>
	</div>
	<?php
}
