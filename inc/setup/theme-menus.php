<?php
/**
 * Theme launch — primary and footer navigation menus.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option: theme-created footer menu IDs (properties, directory). */
define( 'HVN_REALTY_FOOTER_MENUS_OPTION', 'hvn_realty_footer_menu_ids' );

/**
 * Plugin page keys mapped to menu labels for launch.
 *
 * @return array<string, string> Page key => label.
 */
function hvn_realty_launch_get_plugin_menu_pages() {
	return array(
		'property_search'   => __( 'Search', 'havenlytics-realty' ),
		'property_grid'     => __( 'Listings', 'havenlytics-realty' ),
		'property_lists'    => __( 'Lists', 'havenlytics-realty' ),
		'property_agents'   => __( 'Agents', 'havenlytics-realty' ),
		'property_agencies' => __( 'Agencies', 'havenlytics-realty' ),
	);
}

/**
 * Add a page item to a nav menu.
 *
 * @param int    $menu_id   Menu ID.
 * @param int    $page_id   Page ID.
 * @param string $title     Menu label.
 * @param int    $position  Sort position.
 * @param int    $parent_id Parent menu item ID.
 * @return int Menu item ID or 0.
 */
function hvn_realty_launch_add_page_menu_item( $menu_id, $page_id, $title, $position, $parent_id = 0 ) {
	$page_id = absint( $page_id );
	if ( $page_id <= 0 || ! get_post( $page_id ) ) {
		return 0;
	}

	$item_id = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $title,
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $page_id,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-position'  => absint( $position ),
			'menu-item-parent-id' => absint( $parent_id ),
		)
	);

	return is_wp_error( $item_id ) ? 0 : (int) $item_id;
}

/**
 * Add a custom-link item to a nav menu.
 *
 * @param int    $menu_id   Menu ID.
 * @param string $title     Menu label.
 * @param string $url       Link URL.
 * @param int    $position  Sort position.
 * @param int    $parent_id Parent menu item ID.
 * @return int Menu item ID or 0.
 */
function hvn_realty_launch_add_custom_menu_item( $menu_id, $title, $url, $position, $parent_id = 0 ) {
	if ( '' === trim( (string) $url ) ) {
		return 0;
	}

	$item_id = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $title,
			'menu-item-url'       => esc_url_raw( $url ),
			'menu-item-type'      => 'custom',
			'menu-item-status'    => 'publish',
			'menu-item-position'  => absint( $position ),
			'menu-item-parent-id' => absint( $parent_id ),
		)
	);

	return is_wp_error( $item_id ) ? 0 : (int) $item_id;
}

/**
 * Add the Search submenu children (Commercial / Let / Rent / Sale).
 *
 * Runs only while creating a fresh primary menu. Children point at the plugin
 * Property Search page filtered by department so they behave like normal nav
 * items and remain fully editable in Appearance → Menus and the Customizer.
 *
 * @param int    $menu_id        Menu ID.
 * @param int    $parent_item_id Search menu item ID.
 * @param string $search_url     Property Search page URL.
 * @return void
 */
function hvn_realty_launch_add_search_children( $menu_id, $parent_item_id, $search_url ) {
	if ( $parent_item_id <= 0 || '' === trim( (string) $search_url ) ) {
		return;
	}

	$children = array(
		'commercial' => __( 'Commercial', 'havenlytics-realty' ),
		'let'        => __( 'Let', 'havenlytics-realty' ),
		'rent'       => __( 'Rent', 'havenlytics-realty' ),
		'sale'       => __( 'Sale', 'havenlytics-realty' ),
	);

	/**
	 * Filter the Search submenu children (slug => label).
	 *
	 * @param array<string, string> $children Department slug => label.
	 */
	$children = apply_filters( 'hvn_realty_search_menu_children', $children );

	$position = 1;
	foreach ( $children as $slug => $label ) {
		$url = add_query_arg( 'department', sanitize_key( $slug ), $search_url );
		hvn_realty_launch_add_custom_menu_item( $menu_id, $label, $url, $position, $parent_item_id );
		++$position;
	}
}

/**
 * Resolve Search submenu "?department=" links to real page permalinks.
 *
 * The launch routine stores the Search children as custom links that point at
 * the Property Search page with a ?department=<slug> argument. When a matching
 * published WordPress page exists (for example /commercial/, /rent/, /sale/ or
 * /let/) this filter swaps the link for that page's permalink at render time,
 * resolved dynamically by slug — no hardcoded URLs and no query parameter.
 *
 * Nothing is written to the database, so the menu items remain fully editable
 * in Appearance → Menus, and the original "?department=" URL is preserved as a
 * fallback whenever no matching page is published. Items without the parameter
 * (every other menu item, footer menus, etc.) are skipped untouched.
 *
 * @param array<int, object> $items Menu item objects.
 * @param object             $args  wp_nav_menu arguments (unused).
 * @return array<int, object>
 */
function hvn_realty_resolve_search_menu_department_urls( $items, $args = null ) {
	unset( $args );

	if ( empty( $items ) || ! is_array( $items ) ) {
		return $items;
	}

	foreach ( $items as $item ) {
		if ( empty( $item->url ) || false === strpos( $item->url, 'department=' ) ) {
			continue;
		}

		$query = (string) wp_parse_url( $item->url, PHP_URL_QUERY );
		if ( '' === $query ) {
			continue;
		}

		$vars = array();
		wp_parse_str( $query, $vars );

		if ( empty( $vars['department'] ) ) {
			continue;
		}

		$slug = sanitize_title( wp_unslash( $vars['department'] ) );
		if ( '' === $slug ) {
			continue;
		}

		$page = get_page_by_path( $slug );

		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			$item->url = get_permalink( $page );
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'hvn_realty_resolve_search_menu_department_urls', 10, 2 );

/**
 * Whether a menu item URL matches the current front-end request.
 *
 * @param string $url Menu item URL.
 * @return bool
 */
function hvn_realty_menu_item_url_is_current( $url ) {
	$url = (string) $url;
	if ( '' === $url ) {
		return false;
	}

	$item_post_id = url_to_postid( $url );
	if ( $item_post_id > 0 ) {
		return is_page( $item_post_id ) || ( is_singular() && (int) get_queried_object_id() === $item_post_id );
	}

	$host = isset( $_SERVER['HTTP_HOST'] ) ? wp_unslash( $_SERVER['HTTP_HOST'] ) : '';
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';

	if ( '' === $host || '' === $uri ) {
		return false;
	}

	$current_url = set_url_scheme( ( is_ssl() ? 'https://' : 'http://' ) . $host . $uri );
	$item_url    = set_url_scheme( $url );

	return untrailingslashit( $item_url ) === untrailingslashit( $current_url );
}

/**
 * Apply current-menu-parent / current-menu-ancestor classes up the menu tree.
 *
 * @param array<int, object> $items Menu item objects.
 * @return void
 */
function hvn_realty_apply_menu_parent_active_classes( array $items ) {
	$by_id = array();

	foreach ( $items as $item ) {
		$by_id[ (int) $item->ID ] = $item;
	}

	foreach ( $items as $item ) {
		if ( empty( $item->current ) ) {
			continue;
		}

		$parent_id        = (int) $item->menu_item_parent;
		$is_direct_parent = true;

		while ( $parent_id > 0 && isset( $by_id[ $parent_id ] ) ) {
			$parent = $by_id[ $parent_id ];

			$parent->current_item_ancestor = true;
			$parent->classes               = array_merge(
				(array) $parent->classes,
				array(
					'current-menu-ancestor',
					'current-page-ancestor',
				)
			);

			if ( $is_direct_parent ) {
				$parent->current_item_parent = true;
				$parent->classes             = array_merge(
					(array) $parent->classes,
					array( 'current-menu-parent' )
				);
				$is_direct_parent            = false;
			}

			$parent_id = (int) $parent->menu_item_parent;
		}
	}
}

/**
 * Sync active menu classes after Search child URLs are resolved to permalinks.
 *
 * Launch Search children are stored as custom links with ?department= query
 * arguments. WordPress marks current items before those URLs are rewritten at
 * render time, so parent/child active classes are missing on department pages.
 *
 * @param array<int, object> $items Menu item objects.
 * @param object|null        $args  wp_nav_menu() arguments (unused).
 * @return array<int, object>
 */
function hvn_realty_sync_resolved_menu_active_states( $items, $args = null ) {
	unset( $args );

	if ( empty( $items ) || ! is_array( $items ) ) {
		return $items;
	}

	foreach ( $items as $item ) {
		if ( ! empty( $item->current ) ) {
			continue;
		}

		if ( ! hvn_realty_menu_item_url_is_current( $item->url ) ) {
			continue;
		}

		$item->current = true;
		$item->classes = array_merge( (array) $item->classes, array( 'current-menu-item' ) );
	}

	hvn_realty_apply_menu_parent_active_classes( $items );

	foreach ( $items as $item ) {
		$item->classes = array_values( array_unique( array_filter( (array) $item->classes ) ) );
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'hvn_realty_sync_resolved_menu_active_states', 20, 2 );

/**
 * Flush cached theme mods so nav menu locations are visible on the next request.
 *
 * @return void
 */
function hvn_realty_flush_nav_menu_location_cache() {
	$stylesheet = get_stylesheet();
	$template   = get_template();

	wp_cache_delete( 'theme_mods_' . $stylesheet, 'options' );

	if ( $stylesheet !== $template ) {
		wp_cache_delete( 'theme_mods_' . $template, 'options' );
	}
}

/**
 * Assign the theme primary menu to the primary theme location.
 *
 * @param int $menu_id Menu term ID. Uses theme option when 0.
 * @return bool True when primary location was saved.
 */
function hvn_realty_assign_primary_menu_location( $menu_id = 0 ) {
	$menu_id = $menu_id > 0 ? (int) $menu_id : (int) get_option( HVN_REALTY_PRIMARY_MENU_OPTION, 0 );

	if ( $menu_id <= 0 || ! is_nav_menu( $menu_id ) ) {
		return false;
	}

	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	if ( isset( $locations['primary'] ) && (int) $locations['primary'] === $menu_id ) {
		return true;
	}

	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
	hvn_realty_flush_nav_menu_location_cache();

	return true;
}

/**
 * Ensure the theme primary menu is assigned before the header renders.
 *
 * @return void
 */
function hvn_realty_ensure_primary_menu_assigned() {
	if ( is_admin() ) {
		return;
	}

	if ( has_nav_menu( 'primary' ) ) {
		return;
	}

	if ( ! function_exists( 'hvn_realty_is_havenlytics_plugin_active' ) || ! hvn_realty_is_havenlytics_plugin_active() ) {
		return;
	}

	$menu_id = defined( 'HVN_REALTY_PRIMARY_MENU_OPTION' )
		? (int) get_option( HVN_REALTY_PRIMARY_MENU_OPTION, 0 )
		: 0;

	if ( $menu_id <= 0 || ! is_nav_menu( $menu_id ) ) {
		return;
	}

	hvn_realty_assign_primary_menu_location( $menu_id );
}
add_action( 'wp', 'hvn_realty_ensure_primary_menu_assigned', 1 );

/**
 * Create primary navigation with all Havenlytics pages.
 *
 * @param int $home_id Home page ID.
 * @return void
 */
function hvn_realty_launch_create_menu( $home_id ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( ! is_array( $locations ) ) {
		$locations = array();
	}

	if ( ! empty( $locations['primary'] ) && is_nav_menu( (int) $locations['primary'] ) ) {
		return;
	}

	$existing_menu = (int) get_option( HVN_REALTY_PRIMARY_MENU_OPTION, 0 );

	if ( $existing_menu > 0 && is_nav_menu( $existing_menu ) ) {
		hvn_realty_assign_primary_menu_location( $existing_menu );
		return;
	}

	$menu_name = __( 'Primary', 'havenlytics-realty' );
	$menu_id   = wp_create_nav_menu( $menu_name );

	if ( is_wp_error( $menu_id ) ) {
		$menu_id = wp_create_nav_menu( __( 'Primary — Havenlytics', 'havenlytics-realty' ) );
	}

	if ( is_wp_error( $menu_id ) || ! $menu_id ) {
		return;
	}

	$menu_id  = (int) $menu_id;
	$position = 1;

	if ( $home_id > 0 ) {
		hvn_realty_launch_add_page_menu_item(
			$menu_id,
			$home_id,
			__( 'Home', 'havenlytics-realty' ),
			$position
		);
		++$position;
	}

	foreach ( hvn_realty_launch_get_plugin_menu_pages() as $page_key => $label ) {
		$page_id = hvn_realty_get_plugin_page_id( $page_key );
		if ( $page_id <= 0 ) {
			continue;
		}

		$item_id = hvn_realty_launch_add_page_menu_item( $menu_id, $page_id, $label, $position );
		++$position;

		if ( 'property_search' === $page_key && $item_id > 0 ) {
			hvn_realty_launch_add_search_children( $menu_id, $item_id, (string) get_permalink( $page_id ) );
		}
	}

	$blog_id = absint( get_option( 'page_for_posts', 0 ) );
	if ( $blog_id > 0 ) {
		hvn_realty_launch_add_page_menu_item(
			$menu_id,
			$blog_id,
			__( 'Blog', 'havenlytics-realty' ),
			$position
		);
	}

	update_option( HVN_REALTY_PRIMARY_MENU_OPTION, $menu_id );

	hvn_realty_assign_primary_menu_location( $menu_id );
}

/**
 * Create footer nav menus for widget areas.
 *
 * @return array{properties: int, directory: int}
 */
function hvn_realty_launch_create_footer_menus() {
	$stored = get_option( HVN_REALTY_FOOTER_MENUS_OPTION, array() );
	if ( is_array( $stored ) && ! empty( $stored['properties'] ) && is_nav_menu( (int) $stored['properties'] ) ) {
		return array(
			'properties' => (int) $stored['properties'],
			'directory'  => isset( $stored['directory'] ) ? (int) $stored['directory'] : 0,
		);
	}

	$properties_menu_id = wp_create_nav_menu( __( 'Footer — Properties', 'havenlytics-realty' ) );
	$directory_menu_id  = wp_create_nav_menu( __( 'Footer — Directory', 'havenlytics-realty' ) );

	if ( is_wp_error( $properties_menu_id ) || is_wp_error( $directory_menu_id ) ) {
		return array(
			'properties' => 0,
			'directory'  => 0,
		);
	}

	$properties_menu_id = (int) $properties_menu_id;
	$directory_menu_id  = (int) $directory_menu_id;
	$position           = 1;

	$property_keys = array( 'property_search', 'property_grid', 'property_lists' );
	foreach ( $property_keys as $page_key ) {
		$page_id = hvn_realty_get_plugin_page_id( $page_key );
		if ( $page_id <= 0 ) {
			continue;
		}

		hvn_realty_launch_add_page_menu_item(
			$properties_menu_id,
			$page_id,
			hvn_realty_launch_get_plugin_menu_pages()[ $page_key ],
			$position
		);
		++$position;
	}

	$position = 1;
	$directory_keys = array( 'property_agents', 'property_agencies' );
	foreach ( $directory_keys as $page_key ) {
		$page_id = hvn_realty_get_plugin_page_id( $page_key );
		if ( $page_id <= 0 ) {
			continue;
		}

		hvn_realty_launch_add_page_menu_item(
			$directory_menu_id,
			$page_id,
			hvn_realty_launch_get_plugin_menu_pages()[ $page_key ],
			$position
		);
		++$position;
	}

	$menu_ids = array(
		'properties' => $properties_menu_id,
		'directory'  => $directory_menu_id,
	);
	update_option( HVN_REALTY_FOOTER_MENUS_OPTION, $menu_ids );

	return $menu_ids;
}
