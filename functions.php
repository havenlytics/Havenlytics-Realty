<?php
/**
 * Havenlytics Realty Theme functions and definitions
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'HVN_REALTY_VERSION' ) ) {
	define( 'HVN_REALTY_VERSION', '2.0.2' );
}

if ( ! defined( 'HVN_REALTY_TEMPLATE_URL' ) ) {
    define( 'HVN_REALTY_TEMPLATE_URL', get_template_directory_uri() );
}

if ( ! defined( 'HVN_REALTY_PATH' ) ) {
    define( 'HVN_REALTY_PATH', get_template_directory() );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function hvn_realty_setup() {
    load_theme_textdomain( 'havenlytics-realty', get_template_directory() . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 675, true );
    add_image_size( 'hvn-realty-blog', 800, 450, true );
    add_image_size( 'hvn-realty-featured', 1200, 600, true );

    if ( post_type_exists( 'hvnly_property' ) ) {
        add_post_type_support( 'hvnly_property', 'thumbnail' );
    }


    register_nav_menus(
        array(
            'primary'       => esc_html__( 'Primary Menu', 'havenlytics-realty' ),
            'footer'        => esc_html__( 'Footer Menu', 'havenlytics-realty' ),
            'footer-bottom' => esc_html__( 'Footer Bottom Menu (Privacy / Terms / Sitemap)', 'havenlytics-realty' ),
        )
    );

    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    add_theme_support( 'customize-selective-refresh-widgets' );

    add_theme_support(
        'custom-logo',
        array(
            'height'      => 60,
            'width'       => 200,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );

    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'appearance-tools' );

    add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'hvn_realty_setup' );

/**
 * Set the content width.
 */
function hvn_realty_content_width() {
    $container = absint( get_theme_mod( 'hvn_realty_container_width', 1280 ) );
    if ( $container < 960 ) {
        $container = 1280;
    }
    $GLOBALS['content_width'] = apply_filters( 'hvn_realty_content_width', $container );
}
add_action( 'after_setup_theme', 'hvn_realty_content_width', 0 );

/**
 * Register widget area.
 */
function hvn_realty_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__( 'Sidebar', 'havenlytics-realty' ),
            'id'            => 'sidebar-1',
            'description'   => esc_html__( 'Add widgets here.', 'havenlytics-realty' ),
            'before_widget' => '<section id="%1$s" class="hvn-theme-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="hvn-theme-widget-title">',
            'after_title'   => '</h3>',
        )
    );

    for ( $i = 1; $i <= 4; $i++ ) {
        register_sidebar(
            array(
                /* translators: %d: footer column number */
                'name'          => sprintf( esc_html__( 'Footer %d', 'havenlytics-realty' ), $i ),
                'id'            => 'footer-' . $i,
                /* translators: %d: footer column number */
                'description'   => sprintf( esc_html__( 'Add widgets here for footer column %d.', 'havenlytics-realty' ), $i ),
                'before_widget' => '<div id="%1$s" class="hvn-theme-footer-widget %2$s">',
                'after_widget'  => '</div>',
                'before_title'  => '<h4 class="hvn-theme-footer-widget-title">',
                'after_title'   => '</h4>',
            )
        );
    }
}
add_action( 'widgets_init', 'hvn_realty_widgets_init' );

function hvn_realty_font_preconnect() {
    if ( is_admin() || ! function_exists( 'hvn_realty_get_google_font_families' ) ) {
        return;
    }

    $families = hvn_realty_get_google_font_families();
    if ( empty( $families ) ) {
        return;
    }

    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'hvn_realty_font_preconnect', 1 );

/**
 * Inline preloader styles for first paint.
 *
 * @return void
 */
function hvn_realty_preloader_inline_css() {
	if ( is_admin() || ! function_exists( 'hvn_realty_show_preloader' ) || ! hvn_realty_show_preloader() ) {
		return;
	}

	echo '<style id="hvn-realty-preloader-critical">body.hvn-theme-is-loading{overflow:hidden}.hvn-theme-preloader{position:fixed;inset:0;z-index:999999;display:flex;align-items:center;justify-content:center;background:#fff}</style>' . "\n";
}
add_action( 'wp_head', 'hvn_realty_preloader_inline_css', 2 );

/**
 * Enqueue scripts and styles.
 */
function hvn_realty_scripts() {
    $font_deps = array();

    if ( function_exists( 'hvn_realty_enqueue_google_fonts' ) ) {
        $font_handle = hvn_realty_enqueue_google_fonts();
        if ( $font_handle ) {
            $font_deps[] = $font_handle;
        }
    }

    if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {
        hvn_realty_enqueue_theme_style( 'hvn-realty-theme', 'assets/css/theme.css', $font_deps );
        hvn_realty_enqueue_theme_style( 'hvn-realty-polish', 'assets/css/polish.css', array( 'hvn-realty-theme' ) );
        hvn_realty_enqueue_theme_style( 'hvn-realty-layouts', 'assets/css/layouts.css', array( 'hvn-realty-polish' ) );
        hvn_realty_enqueue_theme_style( 'hvn-realty-style', 'style.css', array( 'hvn-realty-layouts' ) );
    } else {
        wp_enqueue_style( 'hvn-realty-theme', HVN_REALTY_TEMPLATE_URL . '/assets/css/theme.css', $font_deps, HVN_REALTY_VERSION );
        wp_enqueue_style( 'hvn-realty-polish', HVN_REALTY_TEMPLATE_URL . '/assets/css/polish.css', array( 'hvn-realty-theme' ), HVN_REALTY_VERSION );
        wp_enqueue_style( 'hvn-realty-layouts', HVN_REALTY_TEMPLATE_URL . '/assets/css/layouts.css', array( 'hvn-realty-polish' ), HVN_REALTY_VERSION );
        wp_enqueue_style( 'hvn-realty-style', get_stylesheet_uri(), array( 'hvn-realty-layouts' ), HVN_REALTY_VERSION );
    }

    if ( function_exists( 'hvn_realty_enqueue_theme_script' ) ) {
        hvn_realty_enqueue_theme_script( 'hvn-realty-theme-js', 'assets/js/theme.js' );
    } else {
        wp_enqueue_script( 'hvn-realty-theme-js', HVN_REALTY_TEMPLATE_URL . '/assets/js/theme.js', array(), HVN_REALTY_VERSION, true );
    }

    wp_localize_script(
        'hvn-realty-theme-js',
        'hvnRealty',
        array(
            'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
            'toggleSubmenuText' => esc_html__( 'Toggle submenu', 'havenlytics-realty' ),
        )
    );

    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_scripts' );

/**
 * Load context-specific layout styles (blog vs page vs single).
 */
function hvn_realty_enqueue_context_layouts() {
	if ( function_exists( 'hvn_realty_is_home_design' ) && hvn_realty_is_home_design() ) {
		return;
	}

	if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {
		return;
	}

	if ( function_exists( 'hvn_realty_is_plugin_shortcode_page' ) && hvn_realty_is_plugin_shortcode_page() ) {
		return;
	}

	if ( is_page() || is_404() ) {
		if ( function_exists( 'hvn_realty_enqueue_theme_style' ) ) {
			hvn_realty_enqueue_theme_style( 'hvn-realty-page', 'assets/css/page.css', array( 'hvn-realty-layouts' ) );
		} else {
			wp_enqueue_style( 'hvn-realty-page', HVN_REALTY_TEMPLATE_URL . '/assets/css/page.css', array( 'hvn-realty-layouts' ), HVN_REALTY_VERSION );
		}
	}

	if ( is_single() ) {
		// Single post styles load via inc/blog/assets.php (blog-single.css).
	}
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_context_layouts', 20 );

/**
 * Load Google Fonts in the block editor.
 */
function hvn_realty_editor_fonts() {
	if ( function_exists( 'hvn_realty_enqueue_google_fonts' ) ) {
		hvn_realty_enqueue_google_fonts();
	}
}
add_action( 'enqueue_block_editor_assets', 'hvn_realty_editor_fonts', 5 );

/**
 * Register block styles.
 */
function hvn_realty_register_block_styles() {
    if ( ! function_exists( 'register_block_style' ) ) {
        return;
    }

    register_block_style(
        'core/quote',
        array(
            'name'         => 'hvn-realty-quote',
            'label'        => __( 'HVN Realty Quote', 'havenlytics-realty' ),
            'inline_style' => '.wp-block-quote.is-style-hvn-realty-quote { border-left-color: var(--hvn-theme-brand-primary); background: var(--hvn-theme-color-gray-100); padding: var(--hvn-theme-space-lg); border-radius: var(--hvn-theme-border-radius-md); }',
        )
    );

    register_block_style(
        'core/button',
        array(
            'name'         => 'hvn-realty-button',
            'label'        => __( 'HVN Realty Button', 'havenlytics-realty' ),
            'inline_style' => '.wp-block-button.is-style-hvn-realty-button .wp-block-button__link { background: var(--hvn-theme-brand-primary); border-radius: var(--hvn-theme-border-radius-full); padding: var(--hvn-theme-button-padding); } .wp-block-button.is-style-hvn-realty-button .wp-block-button__link:hover { background: var(--hvn-theme-brand-primary-dark); transform: translateY(-2px); }',
        )
    );
}
add_action( 'init', 'hvn_realty_register_block_styles' );

/**
 * Register block patterns.
 */
function hvn_realty_register_block_patterns() {
    if ( ! function_exists( 'register_block_pattern' ) ) {
        return;
    }

    $pattern_content = '<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"top":"4rem","bottom":"4rem"}}},"backgroundColor":"primary","textColor":"white"} -->
    <div class="wp-block-group alignwide" style="padding-top:4rem;padding-bottom:4rem;background:var(--hvn-theme-brand-primary);color:var(--hvn-theme-color-white)">
        <!-- wp:heading {"textAlign":"center","level":1} -->
        <h1 class="has-text-align-center" style="color:var(--hvn-theme-color-white)">' . esc_html__( 'Welcome to HVN Realty', 'havenlytics-realty' ) . '</h1>
        <!-- /wp:heading -->
        
        <!-- wp:paragraph {"align":"center"} -->
        <p class="has-text-align-center" style="color:var(--hvn-theme-color-white)">' . esc_html__( 'A clean, modern blog theme designed specifically for real estate professionals.', 'havenlytics-realty' ) . '</p>
        <!-- /wp:paragraph -->
        
        <!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
        <div class="wp-block-buttons">
            <!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link" style="background:var(--hvn-theme-color-white);color:var(--hvn-theme-brand-primary)">' . esc_html__( 'Get Started', 'havenlytics-realty' ) . '</a></div>
            <!-- /wp:button -->
        </div>
        <!-- /wp:buttons -->
    </div>
    <!-- /wp:group -->';

    register_block_pattern(
        'hvn-realty/hero-section',
        array(
            'title'       => __( 'HVN Realty Hero Section', 'havenlytics-realty' ),
            'description' => __( 'A hero section for the HVN Realty theme.', 'havenlytics-realty' ),
            'content'     => $pattern_content,
            'categories'  => array( 'hero', 'featured' ),
        )
    );
}
add_action( 'init', 'hvn_realty_register_block_patterns' );

/**
 * Add custom class to pagination
 */
function hvn_realty_pagination_markup( $template ) {
    return '
    <nav class="navigation hvn-theme-pagination %1$s" role="navigation" aria-label="%4$s">
        <h2 class="screen-reader-text">%2$s</h2>
        <div class="nav-links hvn-theme-pagination-links">%3$s</div>
    </nav>
    ';
}
add_filter( 'navigation_markup_template', 'hvn_realty_pagination_markup' );

/**
 * Get sidebar position setting
 * 
 * @return string
 */
function hvn_realty_get_sidebar_position() {
    return get_theme_mod( 'hvn_realty_sidebar_position', 'none' );
}

/**
 * Get blog layout setting
 * 
 * @return string
 */
function hvn_realty_get_blog_layout() {
    return get_theme_mod( 'hvn_realty_blog_layout', 'grid' );
}

/**
 * Get copyright text
 * 
 * @return string
 */
function hvn_realty_get_copyright_text() {
    $copyright = get_theme_mod( 'hvn_realty_copyright_text', '' );
    if ( empty( $copyright ) ) {
        $copyright = sprintf(
            /* translators: 1: Year, 2: Site name */
            esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'havenlytics-realty' ),
            date_i18n( 'Y' ),
            get_bloginfo( 'name' )
        );
    }
    return wp_kses_post( $copyright );
}

/**
 * Get blog columns setting - FIXED
 *
 * @return int
 */
function hvn_realty_get_blog_columns() {
    // Get the saved value from database
    $columns = get_theme_mod( 'hvn_realty_blog_columns', 3 );
    
    // If list layout, always return 1 column
    if ( 'list' === hvn_realty_get_blog_layout() ) {
        return 1;
    }
    
    // Ensure we have a valid integer between 1 and 4
    $columns = intval( $columns );
    if ( $columns < 1 ) {
        $columns = 1;
    }
    if ( $columns > 4 ) {
        $columns = 4;
    }
    
    return $columns;
}

/**
 * Get custom posts per page setting
 *
 * @return int|null
 */
function hvn_realty_get_posts_per_page() {
    $posts_per_page = get_theme_mod( 'hvn_realty_posts_per_page', '' );
    if ( ! empty( $posts_per_page ) && $posts_per_page > 0 ) {
        return absint( $posts_per_page );
    }
    return null;
}

/**
 * Get ignore sticky posts setting
 *
 * @return bool
 */
function hvn_realty_ignore_sticky_posts() {
    return (bool) get_theme_mod( 'hvn_realty_ignore_sticky_posts', false );
}

/**
 * Modify the main query to change posts per page and handle sticky posts
 *
 * @param WP_Query $query The WP_Query instance.
 */
function hvn_realty_pre_get_posts( $query ) {
    // Only modify main query on frontend
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    
    // Apply to blog home, archives, and search results
    if ( $query->is_home() || $query->is_archive() || $query->is_search() ) {
        // Posts per page
        $custom_ppp = hvn_realty_get_posts_per_page();
        if ( ! empty( $custom_ppp ) && $custom_ppp > 0 ) {
            $query->set( 'posts_per_page', $custom_ppp );
        }
        
        // Ignore sticky posts
        if ( hvn_realty_ignore_sticky_posts() ) {
            $query->set( 'ignore_sticky_posts', 1 );
        }
    }
}
add_action( 'pre_get_posts', 'hvn_realty_pre_get_posts' );

/**
 * Primary menu fallback when no menu is assigned
 *
 * @since 1.0.2
 */
function hvn_realty_primary_menu_fallback() {
	echo '<ul id="primary-menu" class="hvn-theme-nav-menu">';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'havenlytics-realty' ) . '</a></li>';

	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		wp_list_pages(
			array(
				'title_li' => '',
				'depth'    => 1,
				'number'   => 5,
			)
		);
	}

	echo '</ul>';
}

/**
 * Mobile menu fallback when no menu is assigned
 *
 * @since 1.0.2
 */
function hvn_realty_mobile_menu_fallback() {
	echo '<ul class="hvn-theme-mobile-nav-menu">';
	echo '<li class="menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'havenlytics-realty' ) . '</a></li>';

	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		wp_list_pages(
			array(
				'title_li' => '',
				'depth'    => 1,
				'number'   => 5,
			)
		);
	}

	echo '</ul>';
}

/**
 * Load required files.
 */
$theme_loader = get_template_directory() . '/inc/theme-loader.php';
if ( file_exists( $theme_loader ) ) {
	require_once $theme_loader;
} else {
	/**
	 * Minimal fallback when theme-loader.php is missing from the package.
	 *
	 * @param string $relative_path Theme-relative file path.
	 * @param bool   $optional      Unused in fallback.
	 * @return bool
	 */
	function hvn_realty_record_missing_theme_file( $relative_path, $required = true ) {
		$relative_path = ltrim( str_replace( '\\', '/', (string) $relative_path ), '/' );

		if ( '' === $relative_path || ! $required ) {
			return;
		}

		if ( ! isset( $GLOBALS['hvn_realty_missing_theme_files'] ) || ! is_array( $GLOBALS['hvn_realty_missing_theme_files'] ) ) {
			$GLOBALS['hvn_realty_missing_theme_files'] = array();
		}

		if ( ! in_array( $relative_path, $GLOBALS['hvn_realty_missing_theme_files'], true ) ) {
			$GLOBALS['hvn_realty_missing_theme_files'][] = $relative_path;
		}
	}

	function hvn_realty_load_theme_file( $relative_path, $optional = true ) {
		$file = get_template_directory() . '/' . ltrim( $relative_path, '/' );
		if ( file_exists( $file ) ) {
			require_once $file;
			return true;
		}

		if ( ! $optional ) {
			hvn_realty_record_missing_theme_file( $relative_path, true );

			if ( function_exists( 'add_action' ) && ! has_action( 'admin_notices', 'hvn_realty_missing_theme_files_notice' ) ) {
				add_action( 'admin_notices', 'hvn_realty_missing_theme_files_notice' );
			}
		}

		return false;
	}

	function hvn_realty_safe_require( $relative_path, $optional = true ) {
		return hvn_realty_load_theme_file( $relative_path, $optional );
	}
}

hvn_realty_load_theme_file( 'inc/design-tokens.php', false );
hvn_realty_load_theme_file( 'inc/core/release-manifest.php', false );
hvn_realty_load_theme_file( 'inc/core/class-hvn-realty-theme-integrity.php', false );
hvn_realty_load_theme_file( 'inc/customizer/loader.php', false );
hvn_realty_load_theme_file( 'inc/plugin-hooks.php', false );

if ( ! hvn_realty_load_theme_file( 'inc/integrations/havenlytics/bootstrap.php', true ) ) {
	if ( function_exists( 'hvn_realty_missing_integration_notice' ) ) {
		add_action( 'admin_notices', 'hvn_realty_missing_integration_notice' );
	}

	$integration_files = array(
		'helpers.php',
		'carousel.php',
		'homepage-settings.php',
		'homepage.php',
		'homepage-assets.php',
		'breadcrumbs.php',
		'body-classes.php',
		'plugin-shell.php',
		'assets.php',
	);

	foreach ( $integration_files as $integration_file ) {
		hvn_realty_load_theme_file( 'inc/integrations/havenlytics/' . $integration_file, true );
	}
}

hvn_realty_load_theme_file( 'inc/integration-fallbacks.php', true );
hvn_realty_load_theme_file( 'inc/core/class-hvn-realty-migrations.php', false );
hvn_realty_load_theme_file( 'inc/core/class-hvn-realty-upgrade-manager.php', false );

if ( class_exists( 'HVN_Realty_Upgrade_Manager' ) ) {
	add_action( 'after_setup_theme', array( 'HVN_Realty_Upgrade_Manager', 'boot' ), 20 );
}

hvn_realty_load_theme_file( 'inc/setup/theme-launch.php', false );
hvn_realty_load_theme_file( 'inc/setup/theme-default-branding.php', false );
hvn_realty_load_theme_file( 'inc/setup/theme-menus.php', false );
hvn_realty_load_theme_file( 'inc/setup/theme-footer-widgets.php', false );
hvn_realty_load_theme_file( 'inc/setup/theme-property-sidebar-widgets.php', false );
hvn_realty_load_theme_file( 'inc/widgets/footer-widgets.php', false );
hvn_realty_load_theme_file( 'inc/elementor.php', true );
hvn_realty_load_theme_file( 'inc/custom-header.php', false );
hvn_realty_load_theme_file( 'inc/template-tags.php', false );
hvn_realty_load_theme_file( 'inc/layout.php', false );
hvn_realty_load_theme_file( 'inc/blog/bootstrap.php', false );
hvn_realty_load_theme_file( 'inc/template-functions.php', false );
hvn_realty_load_theme_file( 'inc/customizer.php', false );
hvn_realty_load_theme_file( 'inc/breadcrumbs.php', false );
hvn_realty_load_theme_file( 'inc/admin/realty-admin.php', true );
hvn_realty_load_theme_file( 'inc/admin/system-status.php', false );
hvn_realty_load_theme_file( 'inc/admin/dashboard-overview.php', false );
hvn_realty_load_theme_file( 'inc/admin/realty-onboarding.php', false );
hvn_realty_load_theme_file( 'inc/admin/theme-welcome-notice.php', true );

if ( defined( 'JETPACK__VERSION' ) ) {
	hvn_realty_load_theme_file( 'inc/jetpack.php', true );
}

hvn_realty_load_theme_file( 'inc/class-hvn-realty-walker-nav-menu.php', false );