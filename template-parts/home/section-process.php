<?php
/**
 * Homepage 2.2 — How it works / process section.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hvn_steps = function_exists( 'hvn_realty_get_home_process_steps' )
	? hvn_realty_get_home_process_steps()
	: array();

if ( empty( $hvn_steps ) ) {
	return;
}

$hvn_eyebrow  = (string) get_theme_mod( 'hvn_realty_home_process_eyebrow', __( 'How It Works', 'havenlytics-realty' ) );
$hvn_title    = (string) get_theme_mod( 'hvn_realty_home_process_title', __( 'A clearer path from search to keys', 'havenlytics-realty' ) );
$hvn_subtitle = (string) get_theme_mod( 'hvn_realty_home_process_subtitle', __( 'Whether you are buying or selling, every step is guided by local expertise and transparent market data.', 'havenlytics-realty' ) );
?>
<section class="hvn-theme-home-section hvn-theme-home-process" id="hvn-theme-home-process" aria-labelledby="hvn-theme-home-process-title">
	<div class="hvn-theme-home-container">
		<div class="hvn-theme-home-head hvn-theme-home-head--center hvn-theme-home-reveal">
			<?php if ( $hvn_eyebrow ) : ?>
				<span class="hvn-theme-home-eyebrow hvn-theme-home-eyebrow--center"><?php echo esc_html( $hvn_eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( $hvn_title ) : ?>
				<h2 id="hvn-theme-home-process-title"><?php echo esc_html( $hvn_title ); ?></h2>
			<?php endif; ?>
			<?php if ( $hvn_subtitle ) : ?>
				<p><?php echo esc_html( $hvn_subtitle ); ?></p>
			<?php endif; ?>
		</div>
		<ol class="hvn-theme-home-process__grid">
			<?php
			$hvn_step_n = 0;
			foreach ( $hvn_steps as $hvn_step ) :
				$hvn_step_title = isset( $hvn_step['title'] ) ? (string) $hvn_step['title'] : '';
				$hvn_step_text  = isset( $hvn_step['text'] ) ? (string) $hvn_step['text'] : '';
				$hvn_step_url   = isset( $hvn_step['url'] ) ? (string) $hvn_step['url'] : '';
				if ( '' === $hvn_step_title && '' === $hvn_step_text ) {
					continue;
				}
				++$hvn_step_n;
				$hvn_tag = '' !== $hvn_step_url ? 'a' : 'div';
				?>
				<li class="hvn-theme-home-process__step hvn-theme-home-reveal">
					<<?php echo esc_attr( $hvn_tag ); ?> class="hvn-theme-home-process__card"<?php echo '' !== $hvn_step_url ? ' href="' . esc_url( $hvn_step_url ) . '"' : ''; ?>>
						<span class="hvn-theme-home-process__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) $hvn_step_n, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php if ( $hvn_step_title ) : ?>
							<h3><?php echo esc_html( $hvn_step_title ); ?></h3>
						<?php endif; ?>
						<?php if ( $hvn_step_text ) : ?>
							<p><?php echo esc_html( $hvn_step_text ); ?></p>
						<?php endif; ?>
					</<?php echo esc_attr( $hvn_tag ); ?>>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
