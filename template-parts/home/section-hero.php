<?php
/**
 * Homepage 3.0 — Hero section.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_eyebrow      = (string) get_theme_mod( 'hvn_realty_home_hero_eyebrow', __( 'Data-Backed Real Estate', 'havenlytics-realty' ) );
$hvn_title_before = (string) get_theme_mod( 'hvn_realty_home_hero_title_before', __( 'Find a home that', 'havenlytics-realty' ) );
$hvn_title_em     = (string) get_theme_mod( 'hvn_realty_home_hero_title_highlight', __( 'holds its value', 'havenlytics-realty' ) );
$hvn_title_after  = (string) get_theme_mod( 'hvn_realty_home_hero_title_after', __( ', not just your attention.', 'havenlytics-realty' ) );
$hvn_subtitle     = (string) get_theme_mod( 'hvn_realty_home_hero_subtitle', __( 'Havenlytics pairs licensed local agents with transparent market data, so every offer you make is grounded in evidence — not guesswork.', 'havenlytics-realty' ) );

// Back-compat: read unused button/float theme_mods without rendering them.
$unused_primary_label = (string) get_theme_mod( 'hvn_realty_home_hero_primary_label', '' );
$unused_primary_url   = (string) get_theme_mod( 'hvn_realty_home_hero_primary_url', '' );
$unused_ghost_label   = (string) get_theme_mod( 'hvn_realty_home_hero_ghost_label', '' );
$unused_ghost_url     = (string) get_theme_mod( 'hvn_realty_home_hero_ghost_url', '' );
$unused_float_title   = (string) get_theme_mod( 'hvn_realty_home_hero_float_title', '' );
$unused_float_sub     = (string) get_theme_mod( 'hvn_realty_home_hero_float_subtitle', '' );
unset( $unused_primary_label, $unused_primary_url, $unused_ghost_label, $unused_ghost_url, $unused_float_title, $unused_float_sub );

$hvn_stats = array(
	array(
		'value'  => absint( get_theme_mod( 'hvn_realty_home_hero_stat1_value', 2400 ) ),
		'suffix' => (string) get_theme_mod( 'hvn_realty_home_hero_stat1_suffix', '' ),
		'label'  => (string) get_theme_mod( 'hvn_realty_home_hero_stat1_label', __( 'Homes Sold', 'havenlytics-realty' ) ),
	),
	array(
		'value'  => absint( get_theme_mod( 'hvn_realty_home_hero_stat2_value', 98 ) ),
		'suffix' => (string) get_theme_mod( 'hvn_realty_home_hero_stat2_suffix', '%' ),
		'label'  => (string) get_theme_mod( 'hvn_realty_home_hero_stat2_label', __( 'Client Satisfaction', 'havenlytics-realty' ) ),
	),
	array(
		'value'  => absint( get_theme_mod( 'hvn_realty_home_hero_stat3_value', 17 ) ),
		'suffix' => (string) get_theme_mod( 'hvn_realty_home_hero_stat3_suffix', '' ),
		'label'  => (string) get_theme_mod( 'hvn_realty_home_hero_stat3_label', __( 'Years of Data', 'havenlytics-realty' ) ),
	),
);

$hvn_bg_mode = function_exists( 'hvn_realty_sanitize_home_hero_bg_mode' )
	? hvn_realty_sanitize_home_hero_bg_mode( get_theme_mod( 'hvn_realty_home_hero_bg_mode', 'static' ) )
	: 'static';

$hvn_carousel_count = function_exists( 'hvn_realty_sanitize_home_hero_carousel_count' )
	? hvn_realty_sanitize_home_hero_carousel_count( get_theme_mod( 'hvn_realty_home_hero_carousel_count', 5 ) )
	: 5;

$hvn_carousel_autoplay = function_exists( 'hvn_realty_sanitize_home_hero_carousel_autoplay' )
	? hvn_realty_sanitize_home_hero_carousel_autoplay( get_theme_mod( 'hvn_realty_home_hero_carousel_autoplay', 5000 ) )
	: 5000;

$hvn_carousel_transition = function_exists( 'hvn_realty_sanitize_home_hero_carousel_transition' )
	? hvn_realty_sanitize_home_hero_carousel_transition( get_theme_mod( 'hvn_realty_home_hero_carousel_transition', 900 ) )
	: 900;

$hvn_carousel_loop         = (bool) get_theme_mod( 'hvn_realty_home_hero_carousel_loop', true );
$hvn_carousel_pause_hover  = (bool) get_theme_mod( 'hvn_realty_home_hero_carousel_pause_hover', true );
$hvn_carousel_autoplay_on  = (bool) get_theme_mod( 'hvn_realty_home_hero_carousel_autoplay_enabled', true );
$hvn_hero_zoom             = (bool) get_theme_mod( 'hvn_realty_home_hero_zoom', true );
$hvn_transition_effect     = function_exists( 'hvn_realty_sanitize_home_hero_transition_effect' )
	? hvn_realty_sanitize_home_hero_transition_effect( get_theme_mod( 'hvn_realty_home_hero_transition_effect', 'fade_zoom' ) )
	: 'fade_zoom';

$hvn_slides = array();
if ( 'carousel' === $hvn_bg_mode && function_exists( 'hvn_realty_get_home_hero_carousel_slides' ) ) {
	$hvn_slides = hvn_realty_get_home_hero_carousel_slides( $hvn_carousel_count );
}

$hvn_use_carousel = ( 'carousel' === $hvn_bg_mode && count( $hvn_slides ) > 1 );

$hvn_image_a = absint( get_theme_mod( 'hvn_realty_home_hero_image_a', 0 ) );
$hvn_img_a   = $hvn_image_a ? wp_get_attachment_image_url( $hvn_image_a, 'large' ) : '';

if ( ! $hvn_use_carousel ) {
	if ( ! $hvn_img_a && ! empty( $hvn_slides[0]['url'] ) ) {
		$hvn_img_a = (string) $hvn_slides[0]['url'];
	}

	if ( ! $hvn_img_a && post_type_exists( 'hvnly_property' ) ) {
		$hvn_hero_props = get_posts(
			array(
				'post_type'           => 'hvnly_property',
				'posts_per_page'      => 1,
				'post_status'         => 'publish',
				'meta_key'            => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ( ! empty( $hvn_hero_props[0] ) ) {
			$hvn_img_a = get_the_post_thumbnail_url( $hvn_hero_props[0], 'large' );
		}
	}
}

$hvn_bg_style = ( ! $hvn_use_carousel && $hvn_img_a ) ? ' style="background-image:url(' . esc_url( $hvn_img_a ) . ')"' : '';

$hvn_has_stats = false;
foreach ( $hvn_stats as $hvn_stat_check ) {
	if ( '' !== $hvn_stat_check['label'] ) {
		$hvn_has_stats = true;
		break;
	}
}

$hvn_rendered_slides = array();
if ( $hvn_use_carousel ) {
	foreach ( $hvn_slides as $hvn_slide ) {
		$hvn_slide_url = isset( $hvn_slide['url'] ) ? (string) $hvn_slide['url'] : '';
		if ( '' !== $hvn_slide_url ) {
			$hvn_rendered_slides[] = $hvn_slide_url;
		}
	}
	if ( count( $hvn_rendered_slides ) < 2 ) {
		$hvn_use_carousel = false;
	}
}

$hvn_effect_zoom = $hvn_hero_zoom && in_array( $hvn_transition_effect, array( 'fade_zoom', 'ken_burns', 'soft_scale' ), true );
?>
<section class="hvn-realty-hero<?php echo $hvn_hero_zoom ? ' hvn-realty-hero--zoom' : ''; ?>" id="hvn-theme-home-hero" aria-labelledby="hvn-theme-home-hero-title">
	<?php if ( $hvn_use_carousel ) : ?>
		<div
			class="hvn-realty-hero-slider hvn-realty-hero-effect--<?php echo esc_attr( $hvn_transition_effect ); ?>"
			id="hvnRealtyHeroBg"
			data-autoplay="<?php echo esc_attr( $hvn_carousel_autoplay_on ? (string) $hvn_carousel_autoplay : '0' ); ?>"
			data-transition="<?php echo esc_attr( (string) $hvn_carousel_transition ); ?>"
			data-loop="<?php echo $hvn_carousel_loop ? '1' : '0'; ?>"
			data-pause-hover="<?php echo $hvn_carousel_pause_hover ? '1' : '0'; ?>"
			data-zoom="<?php echo $hvn_hero_zoom ? '1' : '0'; ?>"
			data-effect="<?php echo esc_attr( $hvn_transition_effect ); ?>"
			aria-hidden="true"
		>
			<?php foreach ( $hvn_rendered_slides as $hvn_slide_i => $hvn_slide_url ) : ?>
				<?php
				$hvn_slide_classes = 'hvn-realty-hero-slide';
				if ( 0 === (int) $hvn_slide_i ) {
					$hvn_slide_classes .= ' hvn-realty-is-active';
					if ( $hvn_effect_zoom && 'fade' !== $hvn_transition_effect ) {
						$hvn_slide_classes .= ( 'soft_scale' === $hvn_transition_effect )
							? ' hvn-realty-hero-soft-scale'
							: ' hvn-realty-hero-ken-burns';
					}
				}
				?>
				<div class="<?php echo esc_attr( $hvn_slide_classes ); ?>" style="background-image:url(<?php echo esc_url( $hvn_slide_url ); ?>)"></div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<div class="hvn-realty-hero-bg<?php echo $hvn_hero_zoom ? ' hvn-realty-hero-ken-burns' : ''; ?>" id="hvnRealtyHeroBg"<?php echo $hvn_bg_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="img" aria-hidden="true"></div>
	<?php endif; ?>
	<div class="hvn-realty-wrap hvn-realty-hero-content">
		<?php if ( $hvn_eyebrow ) : ?>
			<div class="hvn-realty-hero-eyebrow"><span class="hvn-realty-hero-eyebrow-dot" aria-hidden="true"></span><span class="hvn-realty-eyebrow-text"><?php echo esc_html( $hvn_eyebrow ); ?></span></div>
		<?php endif; ?>
		<h1 id="hvn-theme-home-hero-title">
			<?php echo esc_html( $hvn_title_before ); ?>
			<?php if ( $hvn_title_em ) : ?>
				<span class="hvn-realty-accent-serif"><?php echo esc_html( $hvn_title_em ); ?></span>
			<?php endif; ?>
			<?php echo esc_html( $hvn_title_after ); ?>
		</h1>
		<?php if ( $hvn_subtitle ) : ?>
			<p class="hvn-realty-hero-sub"><?php echo esc_html( $hvn_subtitle ); ?></p>
		<?php endif; ?>
		<?php if ( $hvn_has_stats ) : ?>
			<div class="hvn-realty-hero-stats" role="group" aria-label="<?php esc_attr_e( 'Key statistics', 'havenlytics-realty' ); ?>">
				<?php foreach ( $hvn_stats as $hvn_stat ) : ?>
					<?php if ( '' === $hvn_stat['label'] ) { continue; } ?>
					<div class="hvn-realty-hero-stat">
						<b data-hvn-theme-counter="<?php echo esc_attr( (string) $hvn_stat['value'] ); ?>"<?php echo $hvn_stat['suffix'] ? ' data-hvn-theme-suffix="' . esc_attr( $hvn_stat['suffix'] ) . '"' : ''; ?>>0<?php echo esc_html( $hvn_stat['suffix'] ); ?></b>
						<span><?php echo esc_html( $hvn_stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php if ( $hvn_use_carousel ) : ?>
		<nav class="hvn-realty-hero-rail" id="hvnRealtyHeroNav" aria-label="<?php esc_attr_e( 'Hero slides', 'havenlytics-realty' ); ?>">
			<button type="button" class="hvn-realty-hero-nav__btn" id="hvnRealtyHeroPrev" aria-label="<?php esc_attr_e( 'Previous slide', 'havenlytics-realty' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
			</button>
			<div class="hvn-realty-hero-rail__dots" id="hvnRealtyHeroDots" role="group" aria-label="<?php esc_attr_e( 'Slide indicators', 'havenlytics-realty' ); ?>">
				<?php foreach ( $hvn_rendered_slides as $hvn_dot_i => $hvn_dot_url ) : ?>
					<button
						type="button"
						class="hvn-realty-hero-dot<?php echo 0 === (int) $hvn_dot_i ? ' hvn-realty-is-active' : ''; ?>"
						data-hvn-hero-slide="<?php echo esc_attr( (string) $hvn_dot_i ); ?>"
						aria-label="<?php echo esc_attr( sprintf( /* translators: %d: slide number */ __( 'Go to slide %d', 'havenlytics-realty' ), (int) $hvn_dot_i + 1 ) ); ?>"
						aria-current="<?php echo 0 === (int) $hvn_dot_i ? 'true' : 'false'; ?>"
					></button>
				<?php endforeach; ?>
			</div>
			<button type="button" class="hvn-realty-hero-nav__btn" id="hvnRealtyHeroNext" aria-label="<?php esc_attr_e( 'Next slide', 'havenlytics-realty' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
			</button>
		</nav>
	<?php endif; ?>
	<a class="hvn-realty-scroll-cue" href="#hvn-theme-home-search">
		<span class="hvn-realty-scroll-cue-line" aria-hidden="true"></span><?php esc_html_e( 'Scroll', 'havenlytics-realty' ); ?>
		<span class="screen-reader-text"><?php esc_html_e( ' to property search', 'havenlytics-realty' ); ?></span>
	</a>
</section>
