<?php
/**
 * Blog layout helpers (grid / list / columns / body attributes).
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current front-end view is a theme blog archive context.
 *
 * @return bool
 */
function hvn_realty_is_blog_view() {
	if ( is_admin() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_property_context' ) && hvn_realty_is_property_context() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_havenlytics_view' ) && hvn_realty_is_havenlytics_view() ) {
		return false;
	}

	if ( function_exists( 'hvn_realty_is_standalone_blog_mode' ) && hvn_realty_is_standalone_blog_mode() && is_front_page() ) {
		if ( function_exists( 'hvn_realty_is_empty_realty_front_page' ) && hvn_realty_is_empty_realty_front_page() ) {
			return true;
		}
	}

	return is_home() || is_archive() || is_search();
}

/**
 * Whether the static front page is an empty realty homepage shell.
 *
 * Used when the Havenlytics plugin is inactive so the front URL can fall back
 * to the blog index instead of rendering empty property sections.
 *
 * @return bool
 */
function hvn_realty_is_empty_realty_front_page() {
	if ( 'page' !== get_option( 'show_on_front', 'posts' ) ) {
		return false;
	}

	$page_id = (int) get_option( 'page_on_front', 0 );
	if ( $page_id <= 0 ) {
		return false;
	}

	$post = get_post( $page_id );
	if ( ! $post || 'page' !== $post->post_type ) {
		return false;
	}

	$is_realty_template = false;
	if ( defined( 'HVN_REALTY_HOME_TEMPLATE' ) && HVN_REALTY_HOME_TEMPLATE === get_page_template_slug( $page_id ) ) {
		$is_realty_template = true;
	} elseif ( function_exists( 'hvn_realty_is_realty_homepage' ) && hvn_realty_is_realty_homepage() ) {
		$is_realty_template = true;
	}

	if ( ! $is_realty_template ) {
		return false;
	}

	return '' === trim( (string) $post->post_content );
}

/**
 * Resolve which standalone template should render the front URL.
 *
 * @return string home|page
 */
function hvn_realty_get_standalone_front_template() {
	if ( 'posts' === get_option( 'show_on_front', 'posts' ) ) {
		return 'home';
	}

	if ( hvn_realty_is_empty_realty_front_page() ) {
		return 'home';
	}

	return 'page';
}

/**
 * Render the blog index loop for standalone mode (secondary query).
 *
 * @return void
 */
function hvn_realty_render_standalone_blog_index() {
	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

	$blog_query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => (int) get_option( 'posts_per_page', 10 ),
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
		)
	);

	$shell_class = function_exists( 'hvn_realty_get_blog_shell_classes' )
		? hvn_realty_get_blog_shell_classes()
		: 'hvn-theme-layout hvn-layout-blog';
	$has_sidebar  = hvn_realty_has_sidebar();
	$sidebar_layout = function_exists( 'hvn_realty_sidebar_layout_enabled' ) && hvn_realty_sidebar_layout_enabled();
	?>
	<main id="primary" class="<?php echo esc_attr( trim( $shell_class ) ); ?>" role="main">
		<div class="hvn-theme-container">
			<header class="hvn-theme-blog-header hvn-theme-blog-header--posts">
				<h1 class="hvn-theme-blog-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="hvn-theme-blog-description"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( $sidebar_layout ) : ?>
			<div class="hvn-theme-layout-row">
			<?php endif; ?>

				<div class="hvn-theme-content-area">
					<?php
					if ( $blog_query->have_posts() ) {
						$is_list      = function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout();
						$loop_classes = function_exists( 'hvn_realty_get_blog_loop_classes' )
							? hvn_realty_get_blog_loop_classes()
							: ( $is_list ? 'hvn-blog-list' : 'hvn-blog-grid hvn-cols-3' );
						$card_slug    = function_exists( 'hvn_realty_get_blog_card_template' )
							? hvn_realty_get_blog_card_template()
							: 'grid';
						?>
						<div class="<?php echo esc_attr( $loop_classes ); ?>">
							<?php
							while ( $blog_query->have_posts() ) {
								$blog_query->the_post();
								if ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
									hvn_realty_get_blog_template_part( 'content', $card_slug );
								} else {
									get_template_part( 'templates/blog/content', $card_slug );
								}
							}
							?>
						</div>
						<?php
						global $wp_query;
						$original_query = $wp_query;
						$wp_query       = $blog_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						if ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
							hvn_realty_get_blog_template_part( 'pagination' );
						} else {
							get_template_part( 'templates/blog/pagination' );
						}
						$wp_query = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						wp_reset_postdata();
					} elseif ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
						hvn_realty_get_blog_template_part( 'content', 'none' );
					} else {
						get_template_part( 'templates/blog/content', 'none' );
					}
					?>
				</div>

			<?php if ( $has_sidebar ) : ?>
				<?php hvn_realty_render_sidebar(); ?>
			<?php endif; ?>

			<?php if ( $sidebar_layout ) : ?>
			</div>
			<?php endif; ?>
		</div>
	</main>
	<?php
}

/**
 * Blog loop container class for grid mode.
 *
 * @return string
 */
function hvn_realty_get_blog_grid_class() {
	return 'hvn-blog-grid';
}

/**
 * Blog loop container class for list mode.
 *
 * @return string
 */
function hvn_realty_get_blog_list_class() {
	return 'hvn-blog-list';
}

/**
 * Raw column count from Customizer (matches live preview data-blog-cols).
 *
 * @return int
 */
function hvn_realty_get_blog_column_count() {
	if ( ! function_exists( 'hvn_realty_get_blog_columns' ) ) {
		return 3;
	}

	$columns = max( 1, min( 4, (int) hvn_realty_get_blog_columns() ) );

	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {
		return 1;
	}

	return $columns;
}

/**
 * Column class for the blog grid element.
 *
 * @return string
 */
function hvn_realty_get_blog_cols_class() {
	return 'hvn-cols-' . hvn_realty_get_blog_column_count();
}

/**
 * Combined loop wrapper classes for the active blog layout.
 *
 * @return string
 */
function hvn_realty_get_blog_loop_classes() {
	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {
		return hvn_realty_get_blog_list_class();
	}

	return trim( hvn_realty_get_blog_grid_class() . ' ' . hvn_realty_get_blog_cols_class() );
}

/**
 * Template slug for the active blog card partial.
 *
 * @return string grid|list
 */
function hvn_realty_get_blog_card_template() {
	if ( function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout() ) {
		return 'list';
	}

	return 'grid';
}

/**
 * Output data attributes on <body> for blog grid (matches Customizer value).
 *
 * @return void
 */
function hvn_realty_body_layout_attrs() {
	if ( ! hvn_realty_is_blog_view() ) {
		return;
	}

	$layout = function_exists( 'hvn_realty_get_blog_layout' ) ? hvn_realty_get_blog_layout() : 'grid';

	printf(
		' data-blog-cols="%1$s" data-blog-layout="%2$s"',
		esc_attr( (string) hvn_realty_get_blog_column_count() ),
		esc_attr( $layout )
	);
}
