<?php
/**
 * Havenlytics plugin integration helpers (read-only).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Option: theme-created home page ID. */
define( 'HVN_REALTY_HOME_PAGE_OPTION', 'hvn_realty_home_page_id' );

/** Option: theme launch pack completed. */
define( 'HVN_REALTY_LAUNCH_COMPLETE_OPTION', 'hvn_realty_launch_complete' );

/** Option: theme-created primary menu ID. */
define( 'HVN_REALTY_PRIMARY_MENU_OPTION', 'hvn_realty_primary_menu_id' );

/** Page template slug for the real estate homepage. */
define( 'HVN_REALTY_HOME_TEMPLATE', 'templates/template-realty-home.php' );

/**
 * Whether the current view is the real estate homepage.
 *
 * @return bool
 */
function hvn_realty_is_realty_homepage() {
	if ( ! is_front_page() ) {
		return false;
	}

	$page_id = (int) get_option( 'page_on_front', 0 );
	if ( $page_id <= 0 ) {
		return false;
	}

	$stored_home = (int) get_option( HVN_REALTY_HOME_PAGE_OPTION, 0 );
	if ( $stored_home > 0 && $page_id === $stored_home ) {
		return true;
	}

	return HVN_REALTY_HOME_TEMPLATE === get_page_template_slug( $page_id );
}

/**
 * Whether to render the real estate homepage on the front URL.
 *
 * @return bool
 */
function hvn_realty_should_show_realty_home() {
	if ( ! is_front_page() ) {
		return false;
	}

	if ( hvn_realty_is_realty_homepage() ) {
		return true;
	}

	if ( hvn_realty_is_havenlytics_plugin_active() && get_option( 'hvnly_demo_properties_imported', false ) ) {
		return true;
	}

	return false;
}

/**
 * Whether the Homepage 2.0.0 design is being rendered.
 *
 * True on the realty front page and on any page assigned the
 * "Real Estate Homepage" template. Used to gate homepage assets and the
 * homepage body class.
 *
 * @return bool
 */
function hvn_realty_is_home_design() {
	if ( function_exists( 'hvn_realty_should_show_realty_home' ) && hvn_realty_should_show_realty_home() ) {
		return true;
	}

	if ( function_exists( 'is_page_template' ) && is_page_template( 'templates/template-realty-home.php' ) ) {
		return true;
	}

	return false;
}

/**
 * Render a Havenlytics shortcode when the plugin is active.
 *
 * @param string               $tag  Shortcode tag without brackets.
 * @param array<string, mixed> $atts Attributes.
 * @return string
 */
function hvn_realty_render_shortcode( $tag, $atts = array() ) {
	if ( ! hvn_realty_is_havenlytics_plugin_active() || empty( $tag ) ) {
		return '';
	}

	if ( function_exists( 'shortcode_exists' ) && ! shortcode_exists( sanitize_key( $tag ) ) ) {
		return '';
	}

	$parts = array();
	foreach ( $atts as $key => $value ) {
		if ( null === $value || '' === $value ) {
			continue;
		}
		$parts[] = sanitize_key( $key ) . '="' . esc_attr( (string) $value ) . '"';
	}

	$shortcode = '[' . sanitize_key( $tag );
	if ( ! empty( $parts ) ) {
		$shortcode .= ' ' . implode( ' ', $parts );
	}
	$shortcode .= ']';

	return do_shortcode( $shortcode );
}

/**
 * Permalink for a plugin page by key.
 *
 * @param string $page_key Page key.
 * @return string
 */
function hvn_realty_get_plugin_page_url( $page_key ) {
	$page_id = hvn_realty_get_plugin_page_id( $page_key );
	if ( $page_id <= 0 ) {
		return home_url( '/' );
	}

	$url = get_permalink( $page_id );

	return is_string( $url ) ? $url : home_url( '/' );
}

/**
 * Property search / archive URL (main listings page).
 *
 * @return string
 */
function hvn_realty_get_property_search_url() {
	$url = hvn_realty_get_plugin_page_url( 'property_search' );

	if ( ! $url || home_url( '/' ) === $url ) {
		$url = hvn_realty_get_plugin_page_url( 'property_grid' );
	}

	return is_string( $url ) ? $url : home_url( '/' );
}

/**
 * Property grid URL filtered by department slug.
 *
 * @param string $department Department slug (e.g. sale, rent).
 * @return string
 */
function hvn_realty_get_grid_url_for_department( $department ) {
	$url = hvn_realty_get_plugin_page_url( 'property_grid' );

	return add_query_arg( 'department', sanitize_key( $department ), $url );
}

/**
 * Published property count.
 *
 * @return int
 */
function hvn_realty_get_property_count() {
	if ( ! post_type_exists( 'hvnly_property' ) ) {
		return 0;
	}

	$counts = wp_count_posts( 'hvnly_property' );

	return absint( $counts->publish ?? 0 );
}

/**
 * Published agent count.
 *
 * @return int
 */
function hvn_realty_get_agent_count() {
	if ( ! post_type_exists( 'hvnly_agent' ) ) {
		return 0;
	}

	$counts = wp_count_posts( 'hvnly_agent' );

	return absint( $counts->publish ?? 0 );
}

/**
 * Agency term count.
 *
 * @return int
 */
function hvn_realty_get_agency_count() {
	if ( ! taxonomy_exists( 'hvnly_agent_agency' ) ) {
		return 0;
	}

	$count = wp_count_terms(
		array(
			'taxonomy'   => 'hvnly_agent_agency',
			'hide_empty' => false,
		)
	);

	return is_wp_error( $count ) ? 0 : absint( $count );
}

/**
 * Homepage hero title.
 *
 * @return string
 */
function hvn_realty_get_home_hero_title() {
	$title = get_theme_mod(
		'hvn_realty_home_hero_title',
		__( 'Find Your Perfect Property', 'havenlytics-realty' )
	);

	return apply_filters( 'hvn_realty_home_hero_title', $title );
}

/**
 * Homepage hero subtitle.
 *
 * @return string
 */
function hvn_realty_get_home_hero_subtitle() {
	$subtitle = get_theme_mod(
		'hvn_realty_home_hero_subtitle',
		__( 'Search thousands of listings, connect with agents, and discover your next home.', 'havenlytics-realty' )
	);

	return apply_filters( 'hvn_realty_home_hero_subtitle', $subtitle );
}

/**
 * Whether automatic homepage setup runs after demo import.
 *
 * @return bool
 */
function hvn_realty_is_home_auto_setup_enabled() {
	return (bool) get_theme_mod( 'hvn_realty_home_auto_setup', true );
}

/**
 * CTA banner primary button label.
 *
 * @return string
 */
function hvn_realty_get_home_cta_primary_text() {
	$text = get_theme_mod(
		'hvn_realty_home_cta_primary_text',
		__( 'Browse Listings', 'havenlytics-realty' )
	);

	return apply_filters( 'hvn_realty_home_cta_primary_text', $text );
}

/**
 * Footer CTA headline text.
 *
 * @return string
 */
function hvn_realty_get_home_footer_cta_text() {
	$text = get_theme_mod(
		'hvn_realty_home_footer_cta_text',
		__( 'Start your property search today.', 'havenlytics-realty' )
	);

	return apply_filters( 'hvn_realty_home_footer_cta_text', $text );
}

/**
 * CTA banner headline.
 *
 * @return string
 */
function hvn_realty_get_home_cta_headline() {
	$headline = get_theme_mod(
		'hvn_realty_home_cta_headline',
		__( 'Ready to find your next property?', 'havenlytics-realty' )
	);

	return apply_filters( 'hvn_realty_home_cta_headline', $headline );
}

/**
 * Whether a homepage section is enabled.
 *
 * @param string $section Section slug.
 * @return bool
 */
function hvn_realty_is_home_section_enabled( $section ) {
	$defaults = array(
		'hero'                 => true,
		'search'               => true,
		'features'             => true,
		'newsletter'           => true,
		'hero-map'             => true,
		'featured-properties'  => true,
		'department-tabs'      => true,
		'latest-properties'    => false,
		'property-taxonomies'  => true,
		'property-types'       => false,
		'property-locations'   => true,
		'property-categories'  => true,
		'featured-agents'      => true,
		'featured-agencies'    => true,
		'statistics'           => false,
		'cta-banner'           => true,
		'latest-posts'         => true,
		'testimonials'         => false,
		'footer-cta'           => false,
		'hero-search'          => false,
	);

	$defaults = apply_filters( 'hvn_realty_home_section_defaults', $defaults );

	$key = 'hvn_realty_home_show_' . sanitize_key( str_replace( '-', '_', $section ) );

	if ( 'department-tabs' === $section && null === get_theme_mod( $key, null ) ) {
		$key = 'hvn_realty_home_show_latest_properties';
	}

	if ( in_array( $section, array( 'property-taxonomies', 'property-locations', 'property-categories' ), true ) ) {
		if ( null !== get_theme_mod( 'hvn_realty_home_show_property_taxonomies', null ) ) {
			$key = 'hvn_realty_home_show_property_taxonomies';
		} elseif ( null !== get_theme_mod( 'hvn_realty_home_show_property_locations', null ) ) {
			$key = 'hvn_realty_home_show_property_locations';
		} else {
			$key = 'hvn_realty_home_show_property_categories';
		}
	}

	$default = ! empty( $defaults[ $section ] );

	return (bool) get_theme_mod( $key, $default );
}

/**
 * Property-related taxonomies registered by Havenlytics.
 *
 * @return string[]
 */
function hvn_realty_get_property_taxonomies() {
	return array(
		'hvnly_prop_types',
		'hvnly_prop_depts',
		'hvnly_prop_locations',
		'hvnly_prop_status',
		'hvnly_prop_features',
		'hvnly_prop_badges',
		'hvnly_prop_tags',
	);
}

/**
 * Whether the current view is a property archive or property taxonomy.
 *
 * @return bool
 */
function hvn_realty_is_property_taxonomy_context() {
	if ( ! is_tax() ) {
		return false;
	}

	$term = get_queried_object();
	if ( ! $term || empty( $term->taxonomy ) ) {
		return false;
	}

	return in_array( $term->taxonomy, hvn_realty_get_property_taxonomies(), true );
}

/**
 * Extended property context (single, archive, taxonomies).
 *
 * @return bool
 */
function hvn_realty_is_property_view() {
	return hvn_realty_is_property_context()
		|| is_post_type_archive( 'hvnly_property' )
		|| hvn_realty_is_property_taxonomy_context();
}

/**
 * Whether the current view is an agent single or archive.
 *
 * @return bool
 */
function hvn_realty_is_agent_context() {
	return is_singular( 'hvnly_agent' ) || is_post_type_archive( 'hvnly_agent' );
}

/**
 * Whether the current view is an agency taxonomy page.
 *
 * @return bool
 */
function hvn_realty_is_agency_context() {
	return is_tax( 'hvnly_agent_agency' );
}

/**
 * Map of plugin page keys to option names.
 *
 * @return array<string, string>
 */
function hvn_realty_get_plugin_page_map() {
	return array(
		'property_search'   => 'hvnly_property_search_page_id',
		'property_grid'     => 'hvnly_property_grid_page_id',
		'property_lists'    => 'hvnly_property_list_page_id',
		'property_agents'   => 'hvnly_property_agents_page_id',
		'property_agencies' => 'hvnly_property_agencies_page_id',
	);
}

/**
 * Whether the current page is a Havenlytics plugin shortcode page.
 *
 * @param int $post_id Optional post ID.
 * @return bool
 */
function hvn_realty_is_plugin_shortcode_page( $post_id = 0 ) {
	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return false;
	}

	$post_id = $post_id > 0 ? $post_id : get_queried_object_id();
	if ( $post_id <= 0 ) {
		return false;
	}

	foreach ( hvn_realty_get_plugin_page_map() as $option_key ) {
		if ( (int) get_option( $option_key, 0 ) === $post_id ) {
			return true;
		}
	}

	$home_id = (int) get_option( HVN_REALTY_HOME_PAGE_OPTION, 0 );
	if ( $home_id > 0 && $post_id === $home_id ) {
		return true;
	}

	$post = get_post( $post_id );
	if ( ! $post || 'page' !== $post->post_type ) {
		return false;
	}

	$shortcodes = array(
		'hvnly_property_search',
		'hvnly_property_grid',
		'hvnly_property_lists',
		'hvnly_property_agents',
		'hvnly_property_agencies',
		'hvnly_featured_properties',
	);

	foreach ( $shortcodes as $shortcode ) {
		if ( has_shortcode( $post->post_content, $shortcode ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Whether any Havenlytics plugin front-end view is active.
 *
 * @return bool
 */
function hvn_realty_is_havenlytics_view() {
	if ( ! hvn_realty_is_havenlytics_plugin_active() ) {
		return false;
	}

	return hvn_realty_is_property_view()
		|| hvn_realty_is_agent_context()
		|| hvn_realty_is_agency_context()
		|| hvn_realty_is_plugin_shortcode_page();
}

/**
 * Whether theme breadcrumbs should render on a plugin template.
 *
 * @return bool
 */
function hvn_realty_show_theme_breadcrumbs_on_plugin_view() {
	if ( hvn_realty_should_show_realty_home() ) {
		return false;
	}

	if ( ! hvn_realty_is_havenlytics_view() ) {
		return false;
	}

	if ( is_singular( 'hvnly_property' ) && function_exists( 'hvnly_is_breadcrumb_enabled' ) && hvnly_is_breadcrumb_enabled() ) {
		return false;
	}

	if ( is_singular( 'hvnly_agent' ) && function_exists( 'hvnly_is_breadcrumb_enabled' ) && hvnly_is_breadcrumb_enabled() ) {
		return false;
	}

	return true;
}

/**
 * Section heading helper.
 *
 * @param string $title    Title.
 * @param string $subtitle Optional subtitle.
 * @param string $class    Extra class.
 * @param string $title_id Optional heading ID for aria-labelledby.
 */
function hvn_realty_home_section_heading( $title, $subtitle = '', $class = '', $title_id = '' ) {
	$id_attr = $title_id ? ' id="' . esc_attr( $title_id ) . '"' : '';
	?>
	<header class="hvn-realty-section__header <?php echo esc_attr( $class ); ?>">
		<h2 class="hvn-realty-section__title"<?php echo $id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $title ); ?></h2>
		<?php if ( $subtitle ) : ?>
			<p class="hvn-realty-section__subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
	</header>
	<?php
}
