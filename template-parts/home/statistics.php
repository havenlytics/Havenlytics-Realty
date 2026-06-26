<?php
/**
 * Homepage: Statistics section.
 *
 * @package Havenlytics_Realty
 */

$stats = array(
	array(
		'value' => hvn_realty_get_property_count(),
		'label' => __( 'Properties', 'havenlytics-realty' ),
	),
	array(
		'value' => hvn_realty_get_agent_count(),
		'label' => __( 'Agents', 'havenlytics-realty' ),
	),
	array(
		'value' => hvn_realty_get_agency_count(),
		'label' => __( 'Agencies', 'havenlytics-realty' ),
	),
);

$stats = apply_filters( 'hvn_realty_home_statistics', $stats );
?>
<section class="hvn-realty-home-section hvn-realty-home-stats" aria-labelledby="hvn-realty-stats-title">
	<div class="hvn-theme-container">
		<h2 id="hvn-realty-stats-title" class="screen-reader-text">
			<?php esc_html_e( 'Site statistics', 'havenlytics-realty' ); ?>
		</h2>
		<ul class="hvn-realty-home-stats__grid">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="hvn-realty-home-stats__item">
					<span class="hvn-realty-home-stats__value"><?php echo esc_html( number_format_i18n( (int) $stat['value'] ) ); ?></span>
					<span class="hvn-realty-home-stats__label"><?php echo esc_html( $stat['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
