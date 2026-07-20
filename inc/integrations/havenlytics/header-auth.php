<?php
/**
 * Smart header authentication + favorites integration with Havenlytics.
 *
 * Reuses Workspace URLs and Favorites APIs. No duplicate auth or favorites store.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Agent Workspace surfaces are available.
 *
 * @return bool
 */
function hvn_realty_workspace_available() {
	if ( ! function_exists( 'hvn_realty_has_havenlytics' ) || ! hvn_realty_has_havenlytics() ) {
		return false;
	}

	return class_exists( '\HvnlyNab\Workspace\WorkspaceAvailability' )
		&& \HvnlyNab\Workspace\WorkspaceAvailability::is_available()
		&& class_exists( '\HvnlyNab\Workspace\WorkspaceSettings' );
}

/**
 * Whether Workspace URL helpers can be resolved (plugin active).
 *
 * Differs from hvn_realty_workspace_available(): URLs can be built whenever
 * WorkspaceSettings exists so Login never falls back to wp-login.php
 * while the plugin is installed and active.
 *
 * @return bool
 */
function hvn_realty_can_build_workspace_urls() {
	if ( ! function_exists( 'hvn_realty_has_havenlytics' ) || ! hvn_realty_has_havenlytics() ) {
		return false;
	}

	return class_exists( '\HvnlyNab\Workspace\WorkspaceSettings' );
}

/**
 * Build a Workspace deep-link URL.
 *
 * @param string               $route Route slug.
 * @param array<string, mixed> $query Optional query args.
 * @return string
 */
function hvn_realty_get_workspace_route_url( $route = '', $query = array() ) {
	if ( ! hvn_realty_can_build_workspace_urls() ) {
		return '';
	}

	$url = \HvnlyNab\Workspace\WorkspaceSettings::route_url( (string) $route, (array) $query );

	return is_string( $url ) ? $url : '';
}

/**
 * Whether favorites feature is available for theme surfaces.
 *
 * @return bool
 */
function hvn_realty_favorites_available() {
	if ( ! function_exists( 'hvn_realty_has_havenlytics' ) || ! hvn_realty_has_havenlytics() ) {
		return false;
	}

	if ( function_exists( 'hvnly_is_favorites_enabled' ) ) {
		return (bool) hvnly_is_favorites_enabled();
	}

	return class_exists( '\HvnlyNab\Favorites\FavoritesSchema' );
}

/**
 * Whether the smart authenticated header UI should render.
 *
 * Requires plugin + Workspace so Login / Account links resolve,
 * or favorites so the header heart can render.
 *
 * @return bool
 */
function hvn_realty_should_show_smart_header_auth() {
	$show = hvn_realty_can_build_workspace_urls() || hvn_realty_favorites_available();

	/**
	 * Filter whether the smart header auth UI is shown.
	 *
	 * @param bool $show Whether to show smart auth.
	 */
	return (bool) apply_filters( 'hvn_realty_should_show_smart_header_auth', $show );
}

/**
 * Workspace login URL (or empty).
 *
 * @return string
 */
function hvn_realty_get_workspace_login_url() {
	$url = hvn_realty_get_workspace_route_url( 'login' );

	return $url ? $url : '';
}

/**
 * Workspace registration URL (or empty).
 *
 * @return string
 */
function hvn_realty_get_workspace_register_url() {
	$url = hvn_realty_get_workspace_route_url( 'register' );

	return $url ? $url : '';
}

/**
 * Saved Properties URL (Workspace) or empty.
 *
 * @return string
 */
function hvn_realty_get_saved_properties_url() {
	$url = hvn_realty_get_workspace_route_url( 'saved-properties' );

	return $url ? $url : '';
}

/**
 * Prefer Workspace login for the theme Sign In URL when the plugin is active.
 *
 * Never returns wp-login.php / wp-admin while Havenlytics is active.
 *
 * @param string $url Existing URL.
 * @return string
 */
function hvn_realty_filter_signin_url_for_workspace( $url ) {
	if ( ! function_exists( 'hvn_realty_has_havenlytics' ) || ! hvn_realty_has_havenlytics() ) {
		return $url;
	}

	$workspace_login = hvn_realty_get_workspace_login_url();
	if ( $workspace_login ) {
		return $workspace_login;
	}

	if ( class_exists( '\HvnlyNab\Workspace\WorkspaceSettings' ) ) {
		$base = \HvnlyNab\Workspace\WorkspaceSettings::get_base_url();
		if ( is_string( $base ) && '' !== $base ) {
			return $base;
		}
	}

	return $url;
}
add_filter( 'hvn_realty_signin_url', 'hvn_realty_filter_signin_url_for_workspace' );

/**
 * Logged-in favorites count (server-side). Guests are hydrated in JS.
 *
 * @return int
 */
function hvn_realty_get_favorites_count() {
	if ( ! is_user_logged_in() || ! hvn_realty_favorites_available() ) {
		return 0;
	}

	if ( ! class_exists( '\HvnlyNab\Favorites\FavoritesSchema' )
		|| ! \HvnlyNab\Favorites\FavoritesSchema::table_exists()
		|| ! class_exists( '\HvnlyNab\Favorites\FavoritesRepository' ) ) {
		return 0;
	}

	static $count = null;
	if ( null !== $count ) {
		return $count;
	}

	$repository = new \HvnlyNab\Favorites\FavoritesRepository();
	$count      = (int) $repository->count( get_current_user_id() );

	return $count;
}

/**
 * Account dropdown menu items for logged-in users.
 *
 * @return array<int, array{label: string, url: string, type?: string}>
 */
function hvn_realty_get_account_menu_items() {
	$items = array();

	if ( ! is_user_logged_in() || ! hvn_realty_workspace_available() ) {
		return $items;
	}

	$routes = array(
		'dashboard'        => __( 'Dashboard', 'havenlytics-realty' ),
		'saved-properties' => __( 'Saved Properties', 'havenlytics-realty' ),
		'properties'       => __( 'My Listings', 'havenlytics-realty' ),
		'profile'          => __( 'Profile', 'havenlytics-realty' ),
	);

	foreach ( $routes as $route => $label ) {
		$url = hvn_realty_get_workspace_route_url( $route );
		if ( '' === $url ) {
			continue;
		}
		$items[] = array(
			'label' => $label,
			'url'   => $url,
			'type'  => 'link',
		);
	}

	$logout_url = wp_logout_url( home_url( '/' ) );
	if ( class_exists( '\HvnlyNab\Workspace\WorkspaceSettings' ) ) {
		$redirect = \HvnlyNab\Workspace\WorkspaceSettings::get_logout_redirect_url();
		if ( is_string( $redirect ) && '' !== $redirect ) {
			$logout_url = wp_logout_url( $redirect );
		}
	}

	$items[] = array(
		'label' => __( 'Logout', 'havenlytics-realty' ),
		'url'   => $logout_url,
		'type'  => 'logout',
	);

	/**
	 * Filter My Account dropdown items.
	 *
	 * @param array<int, array<string, string>> $items Menu items.
	 */
	return apply_filters( 'hvn_realty_account_menu_items', $items );
}

/**
 * Preview favorites for the header dropdown (logged-in only, max 3).
 *
 * @param int $limit Max items.
 * @return array<int, array{id: int, title: string, url: string, thumb: string}>
 */
function hvn_realty_get_header_favorite_previews( $limit = 3 ) {
	$limit = max( 1, min( 5, (int) $limit ) );
	$out   = array();

	if ( ! is_user_logged_in() || ! hvn_realty_favorites_available() ) {
		return $out;
	}

	if ( ! class_exists( '\HvnlyNab\Favorites\FavoritesSchema' )
		|| ! \HvnlyNab\Favorites\FavoritesSchema::table_exists()
		|| ! class_exists( '\HvnlyNab\Favorites\FavoritesService' ) ) {
		return $out;
	}

	$service = new \HvnlyNab\Favorites\FavoritesService();
	$ids     = array_slice( $service->get_ids( get_current_user_id() ), 0, $limit );

	if ( empty( $ids ) ) {
		return $out;
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'hvnly_property',
			'post__in'               => $ids,
			'orderby'                => 'post__in',
			'posts_per_page'         => count( $ids ),
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	while ( $query->have_posts() ) {
		$query->the_post();
		$thumb = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
		$out[] = array(
			'id'    => (int) get_the_ID(),
			'title' => get_the_title(),
			'url'   => get_permalink(),
			'thumb' => is_string( $thumb ) ? $thumb : '',
		);
	}
	wp_reset_postdata();

	return $out;
}

/**
 * Whether Customizer still uses default Sign In (not a custom secondary label).
 *
 * @return bool
 */
function hvn_realty_header_uses_default_signin() {
	$secondary_label = trim( (string) get_theme_mod( 'hvn_realty_header_secondary_label', '' ) );

	return '' === $secondary_label;
}

/**
 * Whether the current header context is the homepage (including mobile on home).
 *
 * @param string $context Visual context.
 * @return bool
 */
function hvn_realty_is_homepage_header_context( $context = 'default' ) {
	$context = sanitize_key( (string) $context );

	if ( 'home' === $context ) {
		return true;
	}

	if ( 'mobile' === $context ) {
		if ( function_exists( 'hvn_realty_is_home_design' ) && hvn_realty_is_home_design() ) {
			return true;
		}
		if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {
			return true;
		}
		return is_front_page();
	}

	return false;
}

/**
 * Strip the secondary Sign In / Login button when smart auth owns that role.
 *
 * @param array<int, array<string, string>> $buttons  Buttons.
 * @param string                            $context Context.
 * @return array<int, array<string, string>>
 */
function hvn_realty_filter_header_buttons_for_smart_auth( $buttons, $context = 'default' ) {
	unset( $context );

	if ( ! hvn_realty_can_build_workspace_urls() || ! hvn_realty_header_uses_default_signin() ) {
		return $buttons;
	}

	$filtered = array();
	foreach ( (array) $buttons as $button ) {
		if ( ! is_array( $button ) ) {
			continue;
		}
		$variant = isset( $button['variant'] ) ? $button['variant'] : '';
		$label   = isset( $button['label'] ) ? (string) $button['label'] : '';
		if ( 'secondary' === $variant && (
			__( 'Sign In', 'havenlytics-realty' ) === $label
			|| __( 'Login', 'havenlytics-realty' ) === $label
		) ) {
			continue;
		}
		$filtered[] = $button;
	}

	return $filtered;
}
add_filter( 'hvn_realty_header_action_buttons', 'hvn_realty_filter_header_buttons_for_smart_auth', 20, 2 );

/**
 * Show the List a Property primary CTA on the homepage only.
 *
 * @param array<int, array<string, string>> $buttons  Buttons.
 * @param string                            $context Context.
 * @return array<int, array<string, string>>
 */
function hvn_realty_filter_header_buttons_homepage_cta( $buttons, $context = 'default' ) {
	if ( hvn_realty_is_homepage_header_context( $context ) ) {
		return $buttons;
	}

	$filtered = array();
	foreach ( (array) $buttons as $button ) {
		if ( ! is_array( $button ) ) {
			continue;
		}
		$variant = isset( $button['variant'] ) ? $button['variant'] : '';
		if ( 'primary' === $variant ) {
			continue;
		}
		$filtered[] = $button;
	}

	return $filtered;
}
add_filter( 'hvn_realty_header_action_buttons', 'hvn_realty_filter_header_buttons_homepage_cta', 25, 2 );

/**
 * Load plugin favorites script on theme surfaces that render favorite buttons.
 *
 * @param bool $should Whether to enqueue.
 * @return bool
 */
function hvn_realty_maybe_enqueue_favorites_assets( $should ) {
	if ( $should ) {
		return true;
	}

	if ( ! hvn_realty_favorites_available() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_home_design' ) && hvn_realty_is_home_design() ) {
		return true;
	}

	if ( hvn_realty_should_show_smart_header_auth() && hvn_realty_favorites_available() ) {
		return true;
	}

	return false;
}
add_filter( 'hvnly_favorites_should_enqueue', 'hvn_realty_maybe_enqueue_favorites_assets' );

/**
 * Enqueue Font Awesome (plugin) + lightweight header account script when needed.
 *
 * @return void
 */
function hvn_realty_enqueue_header_auth_assets() {
	if ( is_admin() ) {
		return;
	}

	$need_auth = hvn_realty_should_show_smart_header_auth();
	$need_fav  = hvn_realty_favorites_available()
		&& (
			$need_auth
			|| ( function_exists( 'hvn_realty_is_home_design' ) && hvn_realty_is_home_design() )
		);

	if ( ! $need_auth && ! $need_fav ) {
		return;
	}

	if ( $need_fav && defined( 'HVNLYNAB_ASSETS_URL' ) && defined( 'HVNLYNAB_VERSION' ) ) {
		if ( ! wp_style_is( 'hvnly-fontawesome-all-frontend', 'registered' ) ) {
			wp_register_style(
				'hvnly-fontawesome-all-frontend',
				HVNLYNAB_ASSETS_URL . '/admin/css/fontawesome-all.min.css',
				array(),
				HVNLYNAB_VERSION
			);
		}
		wp_enqueue_style( 'hvnly-fontawesome-all-frontend' );
	}

	if ( ! $need_auth ) {
		return;
	}

	$deps = array();
	if ( wp_script_is( 'hvnly-favorites', 'registered' ) || wp_script_is( 'hvnly-favorites', 'enqueued' ) ) {
		$deps[] = 'hvnly-favorites';
	}

	if ( function_exists( 'hvn_realty_enqueue_theme_script' ) ) {
		hvn_realty_enqueue_theme_script( 'hvn-realty-header-account', 'assets/js/header-account.js', $deps );
	} else {
		wp_enqueue_script(
			'hvn-realty-header-account',
			HVN_REALTY_TEMPLATE_URL . '/assets/js/header-account.js',
			$deps,
			HVN_REALTY_VERSION,
			true
		);
	}

	wp_localize_script(
		'hvn-realty-header-account',
		'hvnRealtyHeaderAccount',
		array(
			'storageKey' => 'hvnly_guest_favorites',
			'homeUrl'    => home_url( '/' ),
			'i18n'       => array(
				'favorites' => __( 'Favorites', 'havenlytics-realty' ),
				'property'  => __( 'Saved property', 'havenlytics-realty' ),
				'openMenu'  => __( 'Open account menu', 'havenlytics-realty' ),
				'closeMenu' => __( 'Close account menu', 'havenlytics-realty' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'hvn_realty_enqueue_header_auth_assets', 30 );
