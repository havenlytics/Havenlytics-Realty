<?php
/**
 * Homepage: Our agents carousel.
 *
 * @package Havenlytics_Realty
 */

$agent_count = hvn_realty_get_agent_count();
$per_page    = $agent_count > 0 ? $agent_count : 100;
$agents_url  = hvn_realty_get_plugin_page_url( 'property_agents' );
?>
<section id="hvn-realty-section-agents" class="hvn-realty-section hvn-realty-section--agents" aria-labelledby="hvn-realty-agents-title">
	<div class="hvn-realty-container">
		<?php
		hvn_realty_home_section_heading(
			hvn_realty_get_home_section_title( 'agents', __( 'Our Agents', 'havenlytics-realty' ) ),
			hvn_realty_get_home_section_subtitle( 'agents', __( 'Connect with experienced local professionals.', 'havenlytics-realty' ) ),
			'hvn-realty-section__header',
			'hvn-realty-agents-title'
		);
		?>

		<div class="hvn-realty-section__body">
			<?php
			get_template_part(
				'template-parts/home/partials/card-carousel',
				null,
				array(
					'type'           => 'agents',
					'shortcode'      => 'hvnly_property_agents',
					'carousel_id'    => 'hvn-realty-agents-carousel',
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
			<a class="hvn-realty-btn hvn-realty-btn--outline" href="<?php echo esc_url( $agents_url ); ?>">
				<?php esc_html_e( 'View All Agents', 'havenlytics-realty' ); ?>
			</a>
		</footer>
	</div>
</section>
