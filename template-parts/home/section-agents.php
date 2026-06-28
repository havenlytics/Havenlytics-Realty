<?php
/**
 * Homepage 2.0.0 — Meet our agents.
 *
 * Rebuilt premium agent card from the prototype, populated by a real
 * hvnly_agent WP_Query. Keeps only dynamic data + agent meta helpers.
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

if ( ! $hvn_query->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<section class="hvn-theme-home-section hvn-theme-home-agents" id="hvn-theme-home-agents" aria-labelledby="hvn-theme-home-agents-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-head hvn-theme-home-head--center hvn-theme-home-reveal">
			<span class="hvn-theme-home-eyebrow hvn-theme-home-eyebrow--center"><?php echo esc_html( hvn_realty_get_home_section_subtitle( 'agents', __( 'Meet Our Agents', 'havenlytics-realty' ) ) ); ?></span>
			<h2 id="hvn-theme-home-agents-title"><?php echo esc_html( hvn_realty_get_home_section_title( 'agents', __( 'Local experts, vetted and ranked on results', 'havenlytics-realty' ) ) ); ?></h2>
		</div>
		<div class="hvn-theme-home-agents__grid">
			<?php
			while ( $hvn_query->have_posts() ) :
				$hvn_query->the_post();
				$hvn_id       = get_the_ID();
				$hvn_name     = get_the_title();
				$hvn_position = (string) get_post_meta( $hvn_id, '_hvnly_agent_position', true );
				$hvn_company  = (string) get_post_meta( $hvn_id, '_hvnly_agent_company', true );
				$hvn_role     = $hvn_position ? $hvn_position : $hvn_company;
				$hvn_linkedin = (string) get_post_meta( $hvn_id, '_hvnly_agent_linkedin', true );
				$hvn_email    = (string) get_post_meta( $hvn_id, '_hvnly_agent_email', true );
				?>
				<article class="hvn-theme-home-agent-card hvn-theme-home-reveal">
					<a class="hvn-theme-home-agent-card__photo" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( $hvn_name ); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( $hvn_name ) ) ); ?>
						<?php else : ?>
							<span class="hvn-theme-home-agent-card__photo-placeholder" aria-hidden="true">
								<svg width="38" height="38" viewBox="0 0 22 22" fill="none"><circle cx="11" cy="8" r="4" stroke="currentColor" stroke-width="1.6"/><path d="M3 20C3 15.5 6.5 13 11 13C15.5 13 19 15.5 19 20" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
							</span>
						<?php endif; ?>
					</a>
					<div class="hvn-theme-home-agent-card__body">
						<h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( $hvn_name ); ?></a></h3>
						<?php if ( $hvn_role ) : ?>
							<span class="hvn-theme-home-agent-card__role"><?php echo esc_html( $hvn_role ); ?></span>
						<?php endif; ?>
						<?php if ( $hvn_linkedin || $hvn_email ) : ?>
							<div class="hvn-theme-home-agent-card__socials">
								<?php if ( $hvn_linkedin ) : ?>
									<a href="<?php echo esc_url( $hvn_linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: agent name. */ __( '%s on LinkedIn', 'havenlytics-realty' ), $hvn_name ) ); ?>">
										<svg viewBox="0 0 24 24" fill="none"><path d="M4 4H20V20H4V4Z" stroke-width="1.6"/><path d="M8 11V17M8 8V8.01M12 17V11M12 11C12 11 13 10 14.5 10C16 10 16 11.5 16 12.5V17" stroke-width="1.6" stroke-linecap="round"/></svg>
									</a>
								<?php endif; ?>
								<?php if ( $hvn_email ) : ?>
									<a href="mailto:<?php echo esc_attr( $hvn_email ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: agent name. */ __( 'Email %s', 'havenlytics-realty' ), $hvn_name ) ); ?>">
										<svg viewBox="0 0 24 24" fill="none"><path d="M4 6H20V18H4V6Z" stroke-width="1.6"/><path d="M4 6L12 13L20 6" stroke-width="1.6"/></svg>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
