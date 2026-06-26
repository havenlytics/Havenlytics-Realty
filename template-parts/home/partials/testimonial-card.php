<?php
/**
 * Homepage testimonial card partial.
 *
 * Expects $item (canonical testimonial array) and optional $index, $show_stars.
 *
 * @package Havenlytics_Realty
 */

// WordPress 6.9+ passes template args via $args (no extract); support both patterns.
if ( ( ! isset( $item ) || ! is_array( $item ) ) && isset( $args ) && is_array( $args ) ) {
	if ( isset( $args['item'] ) && is_array( $args['item'] ) ) {
		$item = $args['item'];
	}
	if ( ! isset( $index ) && isset( $args['index'] ) ) {
		$index = $args['index'];
	}
	if ( ! isset( $show_stars ) && isset( $args['show_stars'] ) ) {
		$show_stars = $args['show_stars'];
	}
}

if ( ! isset( $item ) || ! is_array( $item ) ) {
	return;
}

$index      = isset( $index ) ? absint( $index ) : 0;
$show_stars = isset( $show_stars ) ? (bool) $show_stars : true;

$name     = isset( $item['name'] ) ? (string) $item['name'] : '';
$location = isset( $item['location'] ) ? (string) $item['location'] : '';
$text     = isset( $item['text'] ) ? (string) $item['text'] : '';
$rating   = isset( $item['rating'] ) ? max( 1, min( 5, absint( $item['rating'] ) ) ) : 5;

if ( '' === $name || '' === $text ) {
	return;
}

$avatar_url = function_exists( 'hvn_realty_get_testimonial_avatar_url' )
	? hvn_realty_get_testimonial_avatar_url( $item )
	: '';
$initial = function_exists( 'hvn_realty_get_testimonial_avatar_initial' )
	? hvn_realty_get_testimonial_avatar_initial( $name )
	: '';
?>
<li class="hvn-realty-testimonials__slide" role="listitem" id="hvn-realty-testimonial-<?php echo esc_attr( (string) ( $index + 1 ) ); ?>">
	<article class="hvn-realty-testimonials__card" data-testimonial-source="<?php echo esc_attr( (string) ( $item['source'] ?? 'theme' ) ); ?>">
		<span class="hvn-realty-testimonials__quote" aria-hidden="true">&ldquo;</span>

		<?php
		if ( function_exists( 'hvn_realty_render_home_testimonial_stars' ) ) {
			hvn_realty_render_home_testimonial_stars( $rating, $show_stars );
		}
		?>

		<blockquote class="hvn-realty-testimonials__text">
			<p><?php echo esc_html( $text ); ?></p>
		</blockquote>

		<footer class="hvn-realty-testimonials__author">
			<?php if ( $avatar_url ) : ?>
				<img
					class="hvn-realty-testimonials__avatar"
					src="<?php echo esc_url( $avatar_url ); ?>"
					alt=""
					loading="lazy"
					decoding="async"
					width="48"
					height="48"
				/>
			<?php else : ?>
				<span class="hvn-realty-testimonials__avatar hvn-realty-testimonials__avatar--placeholder" aria-hidden="true">
					<?php echo esc_html( $initial ); ?>
				</span>
			<?php endif; ?>

			<div class="hvn-realty-testimonials__meta">
				<cite class="hvn-realty-testimonials__name"><?php echo esc_html( $name ); ?></cite>
				<?php if ( '' !== $location ) : ?>
					<p class="hvn-realty-testimonials__position"><?php echo esc_html( $location ); ?></p>
				<?php endif; ?>
			</div>
		</footer>
	</article>
</li>
