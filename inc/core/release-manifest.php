<?php
/**
 * Canonical release manifest — shared by integrity checks and build verification.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) && ! defined( 'HVN_REALTY_MANIFEST_CLI' ) ) {
	exit;
}

/**
 * Required files for a safe production release.
 *
 * @return array<string, string> Relative path => category label.
 */
function hvn_realty_get_release_manifest() {
	static $manifest = null;

	if ( null !== $manifest ) {
		return $manifest;
	}

	$manifest = array(
		// WordPress.org / activation core.
		'style.css'                                      => 'core',
		'functions.php'                                  => 'core',
		'readme.txt'                                     => 'core',
		'screenshot.png'                                 => 'wordpress-org',
		'theme.json'                                     => 'core',
		'index.php'                                      => 'templates',
		'front-page.php'                                 => 'templates',
		'header.php'                                     => 'templates',
		'footer.php'                                     => 'templates',
		'sidebar.php'                                    => 'templates',
		'comments.php'                                   => 'templates',
		'404.php'                                        => 'templates',
		'archive.php'                                    => 'templates',
		'home.php'                                       => 'templates',
		'page.php'                                       => 'templates',
		'single.php'                                     => 'templates',
		'search.php'                                     => 'templates',
		'templates/template-realty-home.php'             => 'templates',

		// Safe loader and integrity.
		'inc/theme-loader.php'                           => 'loader',
		'inc/core/release-manifest.php'                  => 'core-inc',
		'inc/core/class-hvn-realty-theme-integrity.php'  => 'core-inc',
		'inc/design-tokens.php'                          => 'core-inc',
		'inc/core/class-hvn-realty-migrations.php'       => 'core-inc',
		'inc/core/class-hvn-realty-upgrade-manager.php'  => 'core-inc',
		'inc/integration-fallbacks.php'                  => 'loader',
		'inc/plugin-hooks.php'                           => 'core-inc',
		'inc/customizer/loader.php'                      => 'customizer',

		// Customizer modules.
		'inc/customizer/helpers.php'                     => 'customizer',
		'inc/customizer/customizer-ui.php'               => 'customizer',
		'inc/customizer/controls.php'                    => 'customizer',
		'inc/customizer/sections.php'                    => 'customizer',
		'inc/customizer/sections-homepage.php'           => 'customizer',
		'inc/customizer/class-hvn-realty-testimonials-control.php' => 'customizer',
		'inc/customizer/class-hvn-realty-section-order-control.php' => 'customizer',
		'inc/customizer/panel-integration.php'           => 'customizer',
		'inc/customizer/selective-refresh.php'           => 'customizer',
		'inc/customizer/css-output.php'                  => 'customizer',
		'inc/customizer/homepage-css-output.php'         => 'customizer',
		'inc/customizer.php'                             => 'customizer',

		// Havenlytics integration.
		'inc/integrations/havenlytics/bootstrap.php'       => 'integration',
		'inc/integrations/havenlytics/helpers.php'       => 'integration',
		'inc/integrations/havenlytics/hero-map.php'        => 'integration',
		'inc/integrations/havenlytics/carousel.php'        => 'integration',
		'inc/integrations/havenlytics/homepage-settings.php' => 'integration',
		'inc/integrations/havenlytics/homepage.php'        => 'integration',
		'inc/integrations/havenlytics/homepage-section-order.php' => 'integration',
		'inc/integrations/havenlytics/homepage-property-types.php' => 'integration',
		'inc/integrations/havenlytics/homepage-testimonials.php' => 'integration',
		'inc/integrations/havenlytics/homepage-assets.php' => 'integration',
		'inc/integrations/havenlytics/breadcrumbs.php'   => 'integration',
		'inc/integrations/havenlytics/body-classes.php'    => 'integration',
		'inc/integrations/havenlytics/plugin-shell.php'  => 'integration',
		'inc/integrations/havenlytics/assets.php'          => 'integration',

		// Required theme includes.
		'inc/setup/theme-launch.php'                     => 'core-inc',
		'inc/setup/theme-default-branding.php'           => 'core-inc',
		'inc/setup/theme-menus.php'                      => 'core-inc',
		'inc/setup/theme-footer-widgets.php'             => 'core-inc',
		'inc/setup/theme-property-sidebar-widgets.php'   => 'core-inc',
		'inc/custom-header.php'                          => 'core-inc',
		'inc/template-tags.php'                          => 'core-inc',
		'inc/layout.php'                                 => 'core-inc',
		'inc/template-functions.php'                     => 'core-inc',
		'inc/breadcrumbs.php'                            => 'core-inc',
		'inc/class-hvn-realty-walker-nav-menu.php'       => 'core-inc',
		'inc/elementor.php'                              => 'optional-inc',
		'inc/jetpack.php'                                => 'optional-inc',
		'inc/admin/realty-admin.php'                     => 'core-inc',
		'inc/admin/system-status.php'                    => 'core-inc',
		'inc/admin/dashboard-overview.php'               => 'core-inc',
		'inc/admin/realty-onboarding.php'                => 'core-inc',
		'inc/admin/theme-welcome-notice.php'             => 'optional-inc',

		// Header / footer / layout partials.
		'template-parts/header/branding.php'             => 'partials',
		'template-parts/header/navigation.php'           => 'partials',
		'template-parts/header/actions.php'              => 'partials',
		'template-parts/header/mobile-menu.php'          => 'partials',
		'template-parts/header/preloader.php'            => 'partials',
		'template-parts/header/property-search-panel.php' => 'partials',
		'template-parts/header/property-search-form.php' => 'partials',
		'template-parts/footer/widgets.php'              => 'partials',
		'template-parts/footer/site-info.php'            => 'partials',
		'template-parts/footer/back-to-top.php'          => 'partials',
		'template-parts/layout/blog-content.php'         => 'partials',
		'templates/blog/content-grid.php'                => 'partials',
		'templates/blog/content-list.php'                => 'partials',
		'templates/blog/content-single.php'              => 'partials',
		'templates/blog/content-none.php'                => 'partials',
		'templates/blog/pagination.php'                  => 'partials',
		'template-parts/blog/content-grid.php'           => 'partials',
		'template-parts/blog/content-list.php'           => 'partials',
		'template-parts/blog/content-single.php'         => 'partials',
		'template-parts/blog/content-none.php'           => 'partials',
		'template-parts/content-grid.php'                => 'partials',
		'template-parts/content-list.php'                => 'partials',

		// Blog module.
		'inc/blog/bootstrap.php'                         => 'blog',
		'inc/blog/layout.php'                            => 'blog',
		'inc/blog/assets.php'                            => 'blog',
		'inc/blog/template-tags.php'                     => 'blog',
		'inc/blog/shell.php'                             => 'blog',
		'inc/blog/templates.php'                         => 'blog',
		'inc/blog/pagination.php'                        => 'blog',
		'inc/blog/a11y.php'                              => 'blog',
		'template-parts/content.php'                     => 'partials',
		'template-parts/content-page.php'                => 'partials',
		'template-parts/content-single.php'              => 'partials',
		'template-parts/content-search.php'              => 'partials',
		'template-parts/content-none.php'                => 'partials',

		// Homepage sections.
		'template-parts/home/plugin-inactive.php'        => 'homepage',
		'template-parts/home/hero-map.php'               => 'homepage',
		'template-parts/home/hero-search-panel.php'      => 'homepage',
		'template-parts/home/hero-search-tabs.php'       => 'homepage',
		'template-parts/home/featured-properties.php'    => 'homepage',
		'template-parts/home/department-tabs.php'        => 'homepage',
		'template-parts/home/property-taxonomies.php'    => 'homepage',
		'template-parts/home/property-types.php'         => 'homepage',
		'template-parts/home/property-locations.php'     => 'homepage',
		'template-parts/home/featured-agents.php'        => 'homepage',
		'template-parts/home/featured-agencies.php'      => 'homepage',
		'template-parts/home/latest-posts.php'           => 'homepage',
		'template-parts/home/testimonials.php'           => 'homepage',
		'template-parts/home/cta-banner.php'             => 'homepage',
		'template-parts/home/footer-cta.php'             => 'homepage',
		'template-parts/home/statistics.php'             => 'homepage',
		'template-parts/home/property-categories.php'    => 'homepage',
		'template-parts/home/partials/card-carousel.php' => 'homepage',
		'template-parts/home/partials/blog-card.php'     => 'homepage',
		'template-parts/home/partials/testimonial-card.php' => 'homepage',

		// Core CSS.
		'assets/css/theme.css'                           => 'assets-css',
		'assets/css/polish.css'                          => 'assets-css',
		'assets/css/layouts.css'                         => 'assets-css',
		'assets/css/page.css'                            => 'assets-css',
		'assets/css/blog.css'                            => 'assets-css',
		'assets/css/blog/blog-base.css'                  => 'assets-css',
		'assets/css/blog/blog-grid.css'                  => 'assets-css',
		'assets/css/blog/blog-list.css'                  => 'assets-css',
		'assets/css/blog/blog-sidebar.css'               => 'assets-css',
		'assets/css/blog/blog-pagination.css'            => 'assets-css',
		'assets/css/blog/blog-single.css'                => 'assets-css',
		'assets/css/single.css'                          => 'assets-css',
		'assets/css/editor.css'                          => 'assets-css',
		'assets/css/admin-realty.css'                    => 'assets-css',
		'assets/admin/img/havenlytics-realty.png'        => 'assets-admin',
		'assets/admin/img/havenlytics-favicon.png'       => 'assets-admin',
		'assets/css/havenlytics-compat.css'              => 'assets-css',
		'assets/css/customizer-controls.css'             => 'assets-css',

		// Homepage modular CSS.
		'assets/css/home/base.css'                       => 'assets-css',
		'assets/css/home/hero.css'                       => 'assets-css',
		'assets/css/home/carousel.css'                   => 'assets-css',
		'assets/css/home/departments.css'                => 'assets-css',
		'assets/css/home/sections.css'                   => 'assets-css',
		'assets/css/home/taxonomies.css'                 => 'assets-css',
		'assets/css/home/property-types.css'             => 'assets-css',
		'assets/css/home/testimonials.css'               => 'assets-css',
		'assets/css/home/blog.css'                       => 'assets-css',

		// Core JS.
		'assets/js/theme.js'                             => 'assets-js',
		'assets/js/customizer.js'                        => 'assets-js',
		'assets/js/customizer-controls-framework.js'     => 'assets-js',
		'assets/js/customizer-controls.js'               => 'assets-js',
		'assets/js/customizer-testimonials-control.js'   => 'assets-js',
		'assets/js/customizer-section-order-control.js'  => 'assets-js',
		'assets/js/havenlytics-home.js'                  => 'assets-js',
		'assets/js/hero-search.js'                       => 'assets-js',
		'assets/js/admin-realty-onboarding.js'           => 'assets-js',
		'search.php'                                     => 'templates',
		'search' . 'form.php'                            => 'search-form',
	);

	if ( function_exists( 'apply_filters' ) ) {
		$manifest = apply_filters( 'hvn_realty_release_manifest', $manifest );
	}

	return $manifest;
}

/**
 * Backward-compatible alias for build scripts.
 *
 * @return array<string, string>
 */
function hvn_realty_release_manifest() {
	return hvn_realty_get_release_manifest();
}

/**
 * Manifest categories that are optional (warning only).
 *
 * @return string[]
 */
function hvn_realty_get_optional_release_categories() {
	return array( 'optional-inc', 'wordpress-org', 'search-form' );
}
