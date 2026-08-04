<?php
/**
 * Homepage 3.0 — Meet our agents.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_exists( 'hvnly_agent' ) ) {
	return;
}

$hvn_count = (int) get_theme_mod( 'hvn_realty_home_agents_count', 4 );
$hvn_count = max( 3, min( 8, $hvn_count ) );

$hvn_query = new WP_Query(
	array(
		'post_type'           => 'hvnly_agent',
		'posts_per_page'      => $hvn_count,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $hvn_query->have_posts() && ! is_customize_preview() ) {
	wp_reset_postdata();
	return;
}

$hvn_linkedin_svg = '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5zM3 9h4v12H3zM9 9h3.8v1.7h.05c.53-1 1.83-2 3.77-2 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.6c0-1.34 0-3.06-1.87-3.06-1.87 0-2.16 1.46-2.16 2.96V21H9z"/></svg>';
$hvn_email_svg    = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></svg>';
?>
<section id="hvn-theme-home-agents" aria-labelledby="hvn-theme-home-agents-title">
	<div class="hvn-realty-wrap">
		<div class="hvn-realty-section-head hvn-realty-reveal">
			<?php
			if ( function_exists( 'hvn_realty_render_home_eyebrow' ) ) {
				hvn_realty_render_home_eyebrow( hvn_realty_get_home_section_subtitle( 'agents', __( 'Meet Our Agents', 'havenlytics-realty' ) ) );
			}
			?>
			<h2 id="hvn-theme-home-agents-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'agents', __( 'Local experts, vetted and ranked on results', 'havenlytics-realty' ) ) ); ?></h2>
		</div>
		<div class="hvn-realty-agent-grid">
			<?php
			while ( $hvn_query->have_posts() ) :
				$hvn_query->the_post();
				$hvn_id       = get_the_ID();
				$hvn_name     = get_the_title( $hvn_id );
				$hvn_permalink = get_permalink( $hvn_id );
				$hvn_position = (string) get_post_meta( $hvn_id, '_hvnly_agent_position', true );
				$hvn_company  = (string) get_post_meta( $hvn_id, '_hvnly_agent_company', true );
				$hvn_role     = $hvn_position ? $hvn_position : $hvn_company;
				$hvn_linkedin = (string) get_post_meta( $hvn_id, '_hvnly_agent_linkedin', true );
				$hvn_email    = (string) get_post_meta( $hvn_id, '_hvnly_agent_email', true );
				$hvn_rating   = get_post_meta( $hvn_id, '_hvnly_agent_rating', true );
				$hvn_rating   = ( '' !== $hvn_rating && null !== $hvn_rating ) ? (string) $hvn_rating : '—';
				$hvn_exp      = get_post_meta( $hvn_id, '_hvnly_agent_experience', true );
				if ( '' === $hvn_exp || null === $hvn_exp ) {
					$hvn_exp = get_post_meta( $hvn_id, '_hvnly_agent_years', true );
				}

				$hvn_sold_count = 0;
				if ( function_exists( 'hvnly_get_agent_properties_query' ) ) {
					$hvn_agent_props = hvnly_get_agent_properties_query( $hvn_id );
					if ( $hvn_agent_props instanceof WP_Query ) {
						$hvn_sold_count = (int) $hvn_agent_props->found_posts;
						// Restore the outer agents loop global post (do not wp_reset_postdata mid-loop).
						if ( method_exists( $hvn_query, 'reset_postdata' ) ) {
							$hvn_query->reset_postdata();
						}
					}
				} else {
					$hvn_sold_query = new WP_Query(
						array(
							'post_type'      => 'hvnly_property',
							'post_status'    => 'publish',
							'posts_per_page' => 1,
							'fields'         => 'ids',
							'no_found_rows'  => false,
							'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
								array(
									'key'   => '_hvnly_property_agent',
									'value' => (string) $hvn_id,
								),
							),
						)
					);
					$hvn_sold_count = (int) $hvn_sold_query->found_posts;
					// found_posts only — no the_post(); keep outer loop intact.
				}
				?>
				<div class="hvn-realty-agent-card hvn-realty-reveal">
					<div class="hvn-realty-agent-photo">
						<?php if ( has_post_thumbnail( $hvn_id ) ) : ?>
							<?php echo get_the_post_thumbnail( $hvn_id, 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( $hvn_name ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes attributes. ?>
						<?php endif; ?>
						<?php if ( $hvn_linkedin || $hvn_email ) : ?>
							<div class="hvn-realty-agent-social">
								<?php if ( $hvn_linkedin ) : ?>
									<a href="<?php echo esc_url( $hvn_linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: agent name. */ __( '%s on LinkedIn', 'havenlytics-realty' ), $hvn_name ) ); ?>"><?php echo $hvn_linkedin_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
								<?php endif; ?>
								<?php if ( $hvn_email ) : ?>
									<a href="mailto:<?php echo esc_attr( $hvn_email ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: agent name. */ __( 'Email %s', 'havenlytics-realty' ), $hvn_name ) ); ?>"><?php echo $hvn_email_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="hvn-realty-agent-info">
						<h3><a href="<?php echo esc_url( $hvn_permalink ); ?>"><?php echo esc_html( $hvn_name ); ?></a></h3>
						<?php if ( $hvn_role ) : ?>
							<div class="hvn-realty-agent-role"><?php echo esc_html( $hvn_role ); ?></div>
						<?php endif; ?>
						<div class="hvn-realty-agent-stats">
							<div><b><?php echo esc_html( number_format_i18n( $hvn_sold_count ) ); ?></b><span><?php esc_html_e( 'Sold', 'havenlytics-realty' ); ?></span></div>
							<div><b><?php echo esc_html( $hvn_rating ); ?></b><span><?php esc_html_e( 'Rating', 'havenlytics-realty' ); ?></span></div>
							<?php if ( '' !== $hvn_exp && null !== $hvn_exp ) : ?>
								<div><b><?php echo esc_html( sprintf( __( '%syr', 'havenlytics-realty' ), number_format_i18n( (float) $hvn_exp ) ) ); ?></b><span><?php esc_html_e( 'Experience', 'havenlytics-realty' ); ?></span></div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
