<?php

/**

 * Homepage: What Our Clients Say (testimonials carousel).

 *

 * @package Havenlytics_Realty

 */



$testimonials = function_exists( 'hvn_realty_get_home_testimonials' ) ? hvn_realty_get_home_testimonials() : array();

$source       = function_exists( 'hvn_realty_get_home_testimonials_source' ) ? hvn_realty_get_home_testimonials_source() : 'none';

$show_stars   = function_exists( 'hvn_realty_show_home_testimonial_stars' ) && hvn_realty_show_home_testimonial_stars();

$autoplay     = function_exists( 'hvn_realty_home_testimonials_autoplay' ) && hvn_realty_home_testimonials_autoplay();

$speed        = function_exists( 'hvn_realty_get_home_testimonials_speed' ) ? hvn_realty_get_home_testimonials_speed() : 5000;



$title = function_exists( 'hvn_realty_get_home_section_title' )

	? hvn_realty_get_home_section_title( 'testimonials', __( 'What Our Clients Say', 'havenlytics-realty' ) )

	: __( 'What Our Clients Say', 'havenlytics-realty' );



$subtitle = function_exists( 'hvn_realty_get_home_section_subtitle' )

	? hvn_realty_get_home_section_subtitle( 'testimonials', __( 'Real stories from buyers, sellers, and renters.', 'havenlytics-realty' ) )

	: __( 'Real stories from buyers, sellers, and renters.', 'havenlytics-realty' );



if ( count( $testimonials ) < 1 ) {

	return;

}

?>

<section

	id="hvn-realty-section-testimonials"

	class="hvn-realty-section hvn-realty-section--testimonials"

	aria-labelledby="hvn-realty-testimonials-title"

	data-testimonials-source="<?php echo esc_attr( $source ); ?>"

>

	<div class="hvn-realty-container">

		<?php

		if ( function_exists( 'hvn_realty_home_section_heading' ) ) {

			hvn_realty_home_section_heading(

				$title,

				$subtitle,

				'',

				'hvn-realty-testimonials-title'

			);

		}

		?>



		<div

			class="hvn-realty-testimonials"

			id="hvn-realty-testimonials-carousel"

			data-hvn-realty-testimonials-carousel

			data-autoplay="<?php echo $autoplay ? '1' : '0'; ?>"

			data-speed="<?php echo esc_attr( (string) $speed ); ?>"

			data-testimonials-source="<?php echo esc_attr( $source ); ?>"

		>

			<button

				type="button"

				class="hvn-realty-testimonials__nav hvn-realty-testimonials__nav--prev"

				data-testimonials-prev

				aria-label="<?php esc_attr_e( 'Previous testimonial', 'havenlytics-realty' ); ?>"

				disabled

			>

				<span aria-hidden="true">&lsaquo;</span>

			</button>



			<div class="hvn-realty-testimonials__viewport" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Client testimonials', 'havenlytics-realty' ); ?>">

				<ul class="hvn-realty-testimonials__track" role="list">

					<?php foreach ( $testimonials as $index => $item ) : ?>

						<?php

						if ( function_exists( 'hvn_realty_safe_get_template_part' ) ) {

							hvn_realty_safe_get_template_part(

								'template-parts/home/partials/testimonial-card',

								null,

								array(

									'item'       => $item,

									'index'      => $index,

									'show_stars' => $show_stars,

								)

							);

						} else {
							get_template_part(
								'template-parts/home/partials/testimonial-card',
								null,
								array(
									'item'       => $item,
									'index'      => $index,
									'show_stars' => $show_stars,
								)
							);
						}

						?>

					<?php endforeach; ?>

				</ul>

			</div>



			<button

				type="button"

				class="hvn-realty-testimonials__nav hvn-realty-testimonials__nav--next"

				data-testimonials-next

				aria-label="<?php esc_attr_e( 'Next testimonial', 'havenlytics-realty' ); ?>"

			>

				<span aria-hidden="true">&rsaquo;</span>

			</button>



			<div class="hvn-realty-testimonials__dots" data-testimonials-dots hidden></div>

		</div>

	</div>

</section>


