<?php
/**
 * Homepage 3.0 — Call to action band.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_title    = (string) get_theme_mod( 'hvn_realty_home_cta_title', __( 'Ready to see what your home is really worth?', 'havenlytics-realty' ) );
$hvn_subtitle = (string) get_theme_mod( 'hvn_realty_home_cta_subtitle', __( 'Get a free, data-backed valuation from a local Havenlytics agent within 24 hours.', 'havenlytics-realty' ) );
$hvn_p_label  = (string) get_theme_mod( 'hvn_realty_home_cta_primary_label', __( 'Get a Free Valuation', 'havenlytics-realty' ) );
$hvn_p_url    = (string) get_theme_mod( 'hvn_realty_home_cta_primary_url', '#hvn-theme-home-agents' );
$hvn_s_label  = (string) get_theme_mod( 'hvn_realty_home_cta_secondary_label', __( 'Talk to an Agent', 'havenlytics-realty' ) );
$hvn_s_url    = (string) get_theme_mod( 'hvn_realty_home_cta_secondary_url', '#hvn-theme-home-agents' );

if ( '#hvn-theme-home-footer' === $hvn_p_url ) {
	$hvn_p_url = '#hvn-theme-home-agents';
}

if ( '' === $hvn_title && '' === $hvn_subtitle ) {
	return;
}

$hvn_bg_url = '';
if ( function_exists( 'hvn_realty_get_home_cta_bg_image_id' ) ) {
	$hvn_bg_id = hvn_realty_get_home_cta_bg_image_id();
	if ( $hvn_bg_id > 0 ) {
		$hvn_bg_url = wp_get_attachment_image_url( $hvn_bg_id, 'full' );
	}
}

if ( ! $hvn_bg_url ) {
	$hvn_hero_image = absint( get_theme_mod( 'hvn_realty_home_hero_image_a', 0 ) );
	if ( $hvn_hero_image > 0 ) {
		$hvn_bg_url = wp_get_attachment_image_url( $hvn_hero_image, 'full' );
	}
}

if ( ! $hvn_bg_url && post_type_exists( 'hvnly_property' ) ) {
	$hvn_first_prop = get_posts(
		array(
			'post_type'           => 'hvnly_property',
			'posts_per_page'      => 1,
			'post_status'         => 'publish',
			'meta_key'            => '_thumbnail_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( ! empty( $hvn_first_prop[0] ) ) {
		$hvn_bg_url = get_the_post_thumbnail_url( $hvn_first_prop[0], 'full' );
	}
}

$hvn_bg_style = $hvn_bg_url ? ' style="background-image:url(' . esc_url( $hvn_bg_url ) . ')"' : '';
?>
<section id="hvn-theme-home-cta" class="hvn-realty-cta-section" aria-labelledby="hvn-theme-home-cta-title">
	<div class="hvn-realty-cta-band hvn-realty-reveal">
		<div class="hvn-realty-cta-band-bg"<?php echo $hvn_bg_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>></div>
		<div class="hvn-realty-cta-inner">
			<?php if ( $hvn_title ) : ?>
				<h2 id="hvn-theme-home-cta-title"><?php echo esc_html( $hvn_title ); ?></h2>
			<?php endif; ?>
			<?php if ( $hvn_subtitle ) : ?>
				<p><?php echo esc_html( $hvn_subtitle ); ?></p>
			<?php endif; ?>
			<div class="hvn-realty-cta-btns">
				<?php if ( $hvn_p_label ) : ?>
					<a href="<?php echo esc_url( $hvn_p_url ); ?>" class="hvn-realty-btn hvn-realty-btn-gold"><?php echo esc_html( $hvn_p_label ); ?></a>
				<?php endif; ?>
				<?php if ( $hvn_s_label ) : ?>
					<a href="<?php echo esc_url( $hvn_s_url ); ?>" class="hvn-realty-btn hvn-realty-btn-outline-light"><?php echo esc_html( $hvn_s_label ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
