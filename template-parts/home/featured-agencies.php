<?php
/**
 * Homepage: Agencies carousel.
 *
 * @package Havenlytics_Realty
 */

$agency_count = hvn_realty_get_agency_count();
$per_page     = $agency_count > 0 ? $agency_count : 100;
$agencies_url = hvn_realty_get_plugin_page_url( 'property_agencies' );
?>
<section id="hvn-realty-section-agencies" class="hvn-realty-section hvn-realty-section--agencies" aria-labelledby="hvn-realty-agencies-title">
	<div class="hvn-realty-container">
		<?php
		hvn_realty_home_section_heading(
			hvn_realty_get_home_section_title( 'agencies', __( 'Agencies', 'havenlytics-realty' ) ),
			hvn_realty_get_home_section_subtitle( 'agencies', __( 'Trusted brokerages and property firms.', 'havenlytics-realty' ) ),
			'hvn-realty-section__header',
			'hvn-realty-agencies-title'
		);
		?>

		<div class="hvn-realty-section__body">
			<?php
			get_template_part(
				'template-parts/home/partials/card-carousel',
				null,
				array(
					'type'           => 'agencies',
					'shortcode'      => 'hvnly_property_agencies',
					'carousel_id'    => 'hvn-realty-agencies-carousel',
					'shortcode_atts' => array(
						'posts_per_page'     => $per_page,
						'columns'            => 4,
						'show_header'        => 'no',
						'show_search'        => 'no',
						'show_view_controls' => 'no',
						'class'              => 'hvn-realty-home-plugin-slim',
					),
				)
			);
			?>
		</div>

		<footer class="hvn-realty-section__footer">
			<a class="hvn-realty-btn hvn-realty-btn--outline" href="<?php echo esc_url( $agencies_url ); ?>">
				<?php esc_html_e( 'View All Agencies', 'havenlytics-realty' ); ?>
			</a>
		</footer>
	</div>
</section>
