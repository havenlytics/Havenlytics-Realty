<?php
/**
 * Footer bottom / copyright template part.
 *
 * @package Havenlytics_Realty
 */
?>
<div class="hvn-theme-footer-bottom">
    <div class="hvn-theme-copyright">
        <?php echo hvn_realty_get_copyright_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>
    <div class="hvn-theme-credit">
        <?php
		printf(
			/* translators: %s: WordPress */
			esc_html__( 'Powered by %s', 'havenlytics-realty' ),
			'<a href="' . esc_url( __( 'https://havenlytics.com/', 'havenlytics-realty' ) ) . '">Havenlytics.com</a>'
		);
		?>
        <span class="sep" aria-hidden="true"> | </span>
        <?php
		printf(
			/* translators: %s: Theme name */
			esc_html__( 'Theme: %s', 'havenlytics-realty' ),
			'<a href="' . esc_url( __( 'https://wordpress.org/themes/havenlytics-realty/', 'havenlytics-realty' ) ) . '">Havenlytics Realty</a>'
		);
		?>
    </div>
</div>