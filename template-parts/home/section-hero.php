<?php
/**
 * Homepage 2.0.0 — Hero section.
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

$hvn_primary_label = (string) get_theme_mod( 'hvn_realty_home_hero_primary_label', __( 'Browse Properties', 'havenlytics-realty' ) );
$hvn_primary_url   = (string) get_theme_mod( 'hvn_realty_home_hero_primary_url', '#hvn-theme-home-search' );
$hvn_ghost_label   = (string) get_theme_mod( 'hvn_realty_home_hero_ghost_label', __( 'Meet an Agent', 'havenlytics-realty' ) );
$hvn_ghost_url     = (string) get_theme_mod( 'hvn_realty_home_hero_ghost_url', '#hvn-theme-home-agents' );

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

// Resolve hero imagery: Customizer first, then recent property thumbnails, then a tinted placeholder.
$hvn_image_a = absint( get_theme_mod( 'hvn_realty_home_hero_image_a', 0 ) );
$hvn_image_b = absint( get_theme_mod( 'hvn_realty_home_hero_image_b', 0 ) );
$hvn_img_a   = $hvn_image_a ? wp_get_attachment_image_url( $hvn_image_a, 'large' ) : '';
$hvn_img_b   = $hvn_image_b ? wp_get_attachment_image_url( $hvn_image_b, 'large' ) : '';

if ( ( ! $hvn_img_a || ! $hvn_img_b ) && post_type_exists( 'hvnly_property' ) ) {
	$hvn_hero_props = get_posts(
		array(
			'post_type'           => 'hvnly_property',
			'posts_per_page'      => 2,
			'post_status'         => 'publish',
			'meta_key'            => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( ! empty( $hvn_hero_props[0] ) && ! $hvn_img_a ) {
		$hvn_img_a = get_the_post_thumbnail_url( $hvn_hero_props[0], 'large' );
	}
	if ( ! empty( $hvn_hero_props[1] ) && ! $hvn_img_b ) {
		$hvn_img_b = get_the_post_thumbnail_url( $hvn_hero_props[1], 'large' );
	}
}
?>
<section class="hvn-theme-home-hero" id="hvn-theme-home-hero" aria-label="<?php esc_attr_e( 'Introduction', 'havenlytics-realty' ); ?>">
	<div class="hvn-theme-home-container hvn-theme-home-hero__grid">
		<div class="hvn-theme-home-hero__copy">
			<?php if ( $hvn_eyebrow ) : ?>
				<span class="hvn-theme-home-eyebrow"><?php echo esc_html( $hvn_eyebrow ); ?></span>
			<?php endif; ?>
			<h1>
				<?php echo esc_html( $hvn_title_before ); ?>
				<?php if ( $hvn_title_em ) : ?>
					<em><?php echo esc_html( $hvn_title_em ); ?></em>
				<?php endif; ?>
				<?php echo esc_html( $hvn_title_after ); ?>
			</h1>
			<?php if ( $hvn_subtitle ) : ?>
				<p><?php echo esc_html( $hvn_subtitle ); ?></p>
			<?php endif; ?>
			<div class="hvn-theme-home-hero__actions">
				<?php if ( $hvn_primary_label ) : ?>
					<a href="<?php echo esc_url( $hvn_primary_url ); ?>" class="hvn-theme-home-btn hvn-theme-home-btn--gold"><?php echo esc_html( $hvn_primary_label ); ?></a>
				<?php endif; ?>
				<?php if ( $hvn_ghost_label ) : ?>
					<a href="<?php echo esc_url( $hvn_ghost_url ); ?>" class="hvn-theme-home-btn hvn-theme-home-btn--ghost"><?php echo esc_html( $hvn_ghost_label ); ?></a>
				<?php endif; ?>
			</div>
			<ul class="hvn-theme-home-hero__stats">
				<?php foreach ( $hvn_stats as $hvn_stat ) : ?>
					<?php if ( '' === $hvn_stat['label'] ) { continue; } ?>
					<li class="hvn-theme-home-hero__stat">
						<strong data-hvn-theme-counter="<?php echo esc_attr( (string) $hvn_stat['value'] ); ?>"<?php echo $hvn_stat['suffix'] ? ' data-hvn-theme-suffix="' . esc_attr( $hvn_stat['suffix'] ) . '"' : ''; ?>>0<?php echo esc_html( $hvn_stat['suffix'] ); ?></strong>
						<span><?php echo esc_html( $hvn_stat['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="hvn-theme-home-hero__visual" aria-hidden="true">
			<figure class="hvn-theme-home-hero__photo hvn-theme-home-hero__photo--a<?php echo $hvn_img_a ? '' : ' hvn-theme-home-hero__photo--placeholder'; ?>">
				<?php if ( $hvn_img_a ) : ?>
					<img src="<?php echo esc_url( $hvn_img_a ); ?>" alt="" loading="eager" decoding="async">
				<?php endif; ?>
			</figure>
			<figure class="hvn-theme-home-hero__photo hvn-theme-home-hero__photo--b<?php echo $hvn_img_b ? '' : ' hvn-theme-home-hero__photo--placeholder'; ?>">
				<?php if ( $hvn_img_b ) : ?>
					<img src="<?php echo esc_url( $hvn_img_b ); ?>" alt="" loading="lazy" decoding="async">
				<?php endif; ?>
			</figure>
			<div class="hvn-theme-home-hero__float">
				<div class="hvn-theme-home-hero__float-icon">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M3 10L7 14L17 4" stroke="#a9803f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</div>
				<div>
					<strong><?php echo esc_html( get_theme_mod( 'hvn_realty_home_hero_float_title', __( 'Valuation Verified', 'havenlytics-realty' ) ) ); ?></strong>
					<span><?php echo esc_html( get_theme_mod( 'hvn_realty_home_hero_float_subtitle', __( 'Data-backed every listing', 'havenlytics-realty' ) ) ); ?></span>
				</div>
			</div>
		</div>
	</div>
	<div class="hvn-theme-home-cue"><span></span><?php esc_html_e( 'Scroll', 'havenlytics-realty' ); ?></div>
</section>
