<?php
/**
 * Homepage 3.0 — Featured properties.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'hvnly_property' ) ) {
	return;
}

$hvn_count = function_exists( 'hvn_realty_get_home_featured_count' )
	? hvn_realty_get_home_featured_count()
	: max( 3, min( 24, (int) get_theme_mod( 'hvn_realty_home_featured_count', 6 ) ) );

$hvn_total = function_exists( 'hvn_realty_get_property_count' ) ? hvn_realty_get_property_count() : 0;

$hvn_tabs_enabled = function_exists( 'hvn_realty_home_featured_tabs_enabled' )
	? hvn_realty_home_featured_tabs_enabled()
	: true;

$hvn_departments = array();
if ( $hvn_tabs_enabled ) {
	$hvn_departments = function_exists( 'hvn_realty_get_home_featured_tab_terms' )
		? hvn_realty_get_home_featured_tab_terms()
		: ( function_exists( 'hvn_realty_get_property_departments' ) ? hvn_realty_get_property_departments() : array() );
}

$hvn_active_tab_pref = function_exists( 'hvn_realty_get_home_featured_active_tab' )
	? hvn_realty_get_home_featured_active_tab()
	: 'all';

$hvn_first_dept_slug = '';
foreach ( $hvn_departments as $hvn_dept_probe ) {
	if ( $hvn_dept_probe instanceof WP_Term && '' !== $hvn_dept_probe->slug ) {
		$hvn_first_dept_slug = $hvn_dept_probe->slug;
		break;
	}
}

$hvn_initial_filter = ( 'first' === $hvn_active_tab_pref && '' !== $hvn_first_dept_slug )
	? $hvn_first_dept_slug
	: 'all';

// Single query path driven by Customizer source (default: all published).
if ( function_exists( 'hvn_realty_get_home_featured_query' ) ) {
	$hvn_query = hvn_realty_get_home_featured_query();
} else {
	// Safe fallback if helper file is missing.
	$hvn_query = new WP_Query(
		array(
			'post_type'           => 'hvnly_property',
			'posts_per_page'      => $hvn_count,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
		)
	);
}

// Front-end: hide only when there are truly no listings. In the Customizer,
// keep the section shell so selective refresh / visibility toggles stay intact.
if ( ! $hvn_query->have_posts() && ! is_customize_preview() ) {
	wp_reset_postdata();
	return;
}

$hvn_description = function_exists( 'hvn_realty_get_home_featured_description' )
	? hvn_realty_get_home_featured_description()
	: (string) get_theme_mod( 'hvn_realty_home_featured_description', '' );

$hvn_show_view_all = function_exists( 'hvn_realty_home_featured_show_view_all' )
	? hvn_realty_home_featured_show_view_all()
	: true;

$hvn_view_all = function_exists( 'hvn_realty_get_home_featured_view_all_url' )
	? hvn_realty_get_home_featured_view_all_url()
	: ( function_exists( 'hvn_realty_get_property_search_url' ) ? hvn_realty_get_property_search_url() : '' );

$hvn_view_all_label = function_exists( 'hvn_realty_get_home_featured_view_all_text' )
	? hvn_realty_get_home_featured_view_all_text( $hvn_total )
	: __( 'View All Listings', 'havenlytics-realty' );

$hvn_columns = function_exists( 'hvn_realty_get_home_featured_columns' )
	? hvn_realty_get_home_featured_columns()
	: array( 'desktop' => 3, 'tablet' => 2, 'mobile' => 1 );

$hvn_pin_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 6-9 12-9 12S3 16 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
$hvn_bed_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 9v9M2 13h20M2 9h18a2 2 0 0 1 2 2v7M6 13V7a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v6"/></svg>';
$hvn_bath_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 12h16M6 12V6a2 2 0 0 1 2-2h2m6 8v6M4 12v6M20 12v6"/></svg>';
$hvn_area_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>';
$hvn_arrow_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
$hvn_heart_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>';
?>
<section
	id="hvn-theme-home-properties"
	class="hvn-realty-home-properties"
	aria-labelledby="hvn-theme-home-properties-title"
	style="<?php echo esc_attr( sprintf( '--hvn-prop-cols-d:%d;--hvn-prop-cols-t:%d;--hvn-prop-cols-m:%d;', (int) $hvn_columns['desktop'], (int) $hvn_columns['tablet'], (int) $hvn_columns['mobile'] ) ); ?>"
>
	<div class="hvn-realty-wrap">
		<div class="hvn-realty-section-head-row hvn-realty-reveal">
			<div class="hvn-realty-section-head" style="margin-bottom:0;">
				<?php
				if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
					hvn_realty_render_home_eyebrow( hvn_realty_get_home_section_subtitle( 'featured', __( 'Featured Properties', 'havenlytics-realty' ) ) );
				}
				?>
				<h2 id="hvn-theme-home-properties-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'featured', __( 'Handpicked homes worth a closer look', 'havenlytics-realty' ) ) ); ?></h2>
				<?php if ( '' !== trim( $hvn_description ) ) : ?>
					<p class="hvn-realty-section-desc"><?php echo esc_html( $hvn_description ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $hvn_tabs_enabled ) : ?>
				<div class="hvn-realty-chip-row" role="tablist" aria-label="<?php esc_attr_e( 'Filter listings', 'havenlytics-realty' ); ?>">
					<?php
					$hvn_all_active = ( 'all' === $hvn_initial_filter );
					?>
					<button
						type="button"
						class="hvn-realty-chip<?php echo $hvn_all_active ? ' hvn-realty-is-active' : ''; ?>"
						data-filter="all"
						role="tab"
						aria-selected="<?php echo $hvn_all_active ? 'true' : 'false'; ?>"
						tabindex="<?php echo $hvn_all_active ? '0' : '-1'; ?>"
					><?php esc_html_e( 'All', 'havenlytics-realty' ); ?></button>
					<?php foreach ( $hvn_departments as $hvn_dept ) : ?>
						<?php
						if ( ! $hvn_dept instanceof WP_Term ) {
							continue;
						}
						$hvn_is_active = ( $hvn_initial_filter === $hvn_dept->slug );
						?>
						<button
							type="button"
							class="hvn-realty-chip<?php echo $hvn_is_active ? ' hvn-realty-is-active' : ''; ?>"
							data-filter="<?php echo esc_attr( $hvn_dept->slug ); ?>"
							role="tab"
							aria-selected="<?php echo $hvn_is_active ? 'true' : 'false'; ?>"
							tabindex="<?php echo $hvn_is_active ? '0' : '-1'; ?>"
						><?php echo esc_html( $hvn_dept->name ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="hvn-realty-prop-grid" id="hvnRealtyPropGrid" data-hvn-initial-filter="<?php echo esc_attr( $hvn_initial_filter ); ?>">
			<?php
			$hvn_rendered = 0;
			while ( $hvn_query->have_posts() ) :
				$hvn_query->the_post();
				// Bind card data to the queried property ID only. Nested helpers
				// (agent avatar/excerpt resolution) may call wp_reset_postdata()
				// and replace global $post with the main query post (e.g. "Hello world!").
				$hvn_id        = (int) get_the_ID();
				$hvn_title     = get_the_title( $hvn_id );
				$hvn_permalink = get_permalink( $hvn_id );

				$hvn_price_raw = get_post_meta( $hvn_id, '_hvnly_property_price', true );
				$hvn_price     = '';
				if ( '' !== $hvn_price_raw && null !== $hvn_price_raw ) {
					$hvn_price = function_exists( 'hvnly_format_price' ) ? hvnly_format_price( $hvn_price_raw ) : esc_html( number_format_i18n( (float) $hvn_price_raw ) );
				}

				$hvn_beds  = get_post_meta( $hvn_id, '_hvnly_property_bedrooms', true );
				$hvn_baths = get_post_meta( $hvn_id, '_hvnly_property_bathrooms', true );
				$hvn_area  = get_post_meta( $hvn_id, '_hvnly_property_sqft', true );

				$hvn_status_name = function_exists( 'hvn_realty_get_property_status_name' )
					? hvn_realty_get_property_status_name( $hvn_id )
					: '';
				$hvn_badge_class = 'hvn-realty-prop-badge';
				if ( $hvn_status_name ) {
					if ( false !== stripos( $hvn_status_name, 'new' ) ) {
						$hvn_badge_class .= ' hvn-realty-badge-new';
					} elseif ( false !== stripos( $hvn_status_name, 'open' ) ) {
						$hvn_badge_class .= ' hvn-realty-badge-open';
					} elseif ( false !== stripos( $hvn_status_name, 'exclusive' ) ) {
						$hvn_badge_class .= ' hvn-realty-badge-exclusive';
					} elseif ( false !== stripos( $hvn_status_name, 'sold' ) ) {
						$hvn_badge_class .= ' hvn-realty-badge-sold';
					} elseif ( false !== stripos( $hvn_status_name, 'pending' ) ) {
						$hvn_badge_class .= ' hvn-realty-badge-pending';
					} elseif ( false !== stripos( $hvn_status_name, 'rent' ) ) {
						$hvn_badge_class .= ' hvn-realty-badge-rent';
					}
				}

				$hvn_dept_slugs = function_exists( 'hvn_realty_get_property_department_slugs' )
					? hvn_realty_get_property_department_slugs( $hvn_id )
					: array();
				$hvn_dept_attr  = ! empty( $hvn_dept_slugs ) ? implode( ' ', $hvn_dept_slugs ) : 'all';

				$hvn_loc_terms = get_the_terms( $hvn_id, 'hvnly_prop_locations' );
				$hvn_loc_name  = ( $hvn_loc_terms && ! is_wp_error( $hvn_loc_terms ) ) ? $hvn_loc_terms[0]->name : '';

				$hvn_agent      = function_exists( 'hvnly_get_primary_property_agent' ) ? hvnly_get_primary_property_agent( $hvn_id ) : array();
				$hvn_agent_name = '';
				$hvn_agent_img  = '';
				if ( is_array( $hvn_agent ) && ! empty( $hvn_agent['name'] ) ) {
					$hvn_agent_name = (string) $hvn_agent['name'];
					if ( ! empty( $hvn_agent['avatar'] ) ) {
						$hvn_agent_img = (string) $hvn_agent['avatar'];
					} elseif ( ! empty( $hvn_agent['photo'] ) ) {
						$hvn_agent_img = (string) $hvn_agent['photo'];
					} elseif ( ! empty( $hvn_agent['image'] ) ) {
						$hvn_agent_img = (string) $hvn_agent['image'];
					}
				}

				// Restore this property as global $post after nested helpers.
				// Do not call wp_reset_postdata() here — that restores the main query.
				if ( method_exists( $hvn_query, 'reset_postdata' ) ) {
					$hvn_query->reset_postdata();
				}

				$hvn_card_hidden = ( 'all' !== $hvn_initial_filter && ! in_array( $hvn_initial_filter, $hvn_dept_slugs, true ) );
				++$hvn_rendered;
				?>
				<article
					class="hvn-realty-prop-card hvn-realty-reveal"
					data-cat="<?php echo esc_attr( $hvn_dept_attr ); ?>"
					<?php echo $hvn_card_hidden ? ' hidden' : ''; ?>
					style="<?php echo $hvn_card_hidden ? 'display:none;' : ''; ?>"
				>
					<div class="hvn-realty-prop-media">
						<?php if ( has_post_thumbnail( $hvn_id ) ) : ?>
							<?php echo get_the_post_thumbnail( $hvn_id, 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( $hvn_title ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes attributes. ?>
						<?php endif; ?>
						<?php if ( $hvn_status_name ) : ?>
							<span class="<?php echo esc_attr( $hvn_badge_class ); ?>"><?php echo esc_html( $hvn_status_name ); ?></span>
						<?php endif; ?>
						<?php if ( function_exists( 'hvn_realty_favorites_available' ) && hvn_realty_favorites_available() ) : ?>
							<?php
							$hvn_is_favorited = function_exists( 'hvnly_is_property_favorited' ) ? hvnly_is_property_favorited( $hvn_id ) : false;
							$hvn_fav_label    = $hvn_is_favorited
								? __( 'Remove from favorites', 'havenlytics-realty' )
								: __( 'Save property', 'havenlytics-realty' );
							$hvn_toast        = function_exists( 'hvnly_get_favorite_toast_data' )
								? hvnly_get_favorite_toast_data( $hvn_id )
								: array( 'title' => $hvn_title, 'thumb' => '' );
							?>
							<button
								type="button"
								class="hvn-realty-fav-btn hvnly-property--grid-list--favorite<?php echo $hvn_is_favorited ? ' hvn-realty-is-active is-favorited' : ''; ?>"
								data-hvnly-favorite="1"
								data-property-id="<?php echo esc_attr( (string) $hvn_id ); ?>"
								data-property-title="<?php echo esc_attr( isset( $hvn_toast['title'] ) ? $hvn_toast['title'] : $hvn_title ); ?>"
								data-property-thumb="<?php echo esc_url( isset( $hvn_toast['thumb'] ) ? $hvn_toast['thumb'] : '' ); ?>"
								aria-pressed="<?php echo $hvn_is_favorited ? 'true' : 'false'; ?>"
								aria-label="<?php echo esc_attr( $hvn_fav_label ); ?>"
							><?php echo $hvn_heart_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
							<?php
							// Compare must always sit beside Favorite (Pro Compare field template).
							if ( function_exists( 'hvnly_render_field' ) ) {
								hvnly_render_field( 'compare', $hvn_id, array(), 'default' );
							}
							?>
						<?php endif; ?>
						<?php if ( $hvn_price ) : ?>
							<span class="hvn-realty-prop-price"><?php echo wp_kses_post( $hvn_price ); ?></span>
						<?php endif; ?>
					</div>
					<div class="hvn-realty-prop-body">
						<?php if ( $hvn_loc_name ) : ?>
							<div class="hvn-realty-prop-loc"><?php echo $hvn_pin_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $hvn_loc_name ); ?></div>
						<?php endif; ?>
						<h3 class="hvn-realty-prop-title"><a href="<?php echo esc_url( $hvn_permalink ); ?>"><?php echo esc_html( $hvn_title ); ?></a></h3>
						<?php if ( '' !== $hvn_beds || '' !== $hvn_baths || '' !== $hvn_area ) : ?>
							<div class="hvn-realty-prop-meta">
								<?php if ( '' !== $hvn_beds ) : ?>
									<div class="hvn-realty-prop-meta-item"><?php echo $hvn_bed_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( _n( '%s bd', '%s bd', (int) $hvn_beds, 'havenlytics-realty' ), number_format_i18n( (float) $hvn_beds, 1 ) ) ); ?></div>
								<?php endif; ?>
								<?php if ( '' !== $hvn_baths ) : ?>
									<div class="hvn-realty-prop-meta-item"><?php echo $hvn_bath_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( _n( '%s ba', '%s ba', (int) $hvn_baths, 'havenlytics-realty' ), number_format_i18n( (float) $hvn_baths, 1 ) ) ); ?></div>
								<?php endif; ?>
								<?php if ( '' !== $hvn_area ) : ?>
									<div class="hvn-realty-prop-meta-item"><?php echo $hvn_area_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( __( '%s sqft', 'havenlytics-realty' ), number_format_i18n( (float) $hvn_area ) ) ); ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( $hvn_agent_name ) : ?>
							<div class="hvn-realty-prop-agent">
								<div class="hvn-realty-prop-agent-who">
									<?php if ( $hvn_agent_img ) : ?>
										<img src="<?php echo esc_url( $hvn_agent_img ); ?>" alt="<?php echo esc_attr( $hvn_agent_name ); ?>" loading="lazy" decoding="async">
									<?php endif; ?>
									<span><?php echo esc_html( $hvn_agent_name ); ?></span>
								</div>
								<a class="hvn-realty-prop-agent-view" href="<?php echo esc_url( $hvn_permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: property title. */ __( 'View %s', 'havenlytics-realty' ), $hvn_title ) ); ?>"><?php echo $hvn_arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
							</div>
						<?php endif; ?>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>

			<?php if ( 0 === $hvn_rendered && is_customize_preview() ) : ?>
				<p class="hvn-realty-prop-empty"><?php esc_html_e( 'No properties match the current Featured Properties settings.', 'havenlytics-realty' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $hvn_show_view_all && $hvn_view_all ) : ?>
			<div class="hvn-realty-grid-foot hvn-realty-reveal">
				<a href="<?php echo esc_url( $hvn_view_all ); ?>" class="hvn-realty-btn hvn-realty-btn-ghost">
					<?php echo esc_html( $hvn_view_all_label ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
