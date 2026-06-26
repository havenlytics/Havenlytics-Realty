<?php
/**
 * Blog template tags — markup helpers for grid and list cards.
 *
 * @package Havenlytics_Realty
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Primary category for a post (first assigned category).
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function hvn_realty_blog_get_primary_category( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cats    = get_the_category( $post_id );

	if ( empty( $cats ) || is_wp_error( $cats ) ) {
		return null;
	}

	return $cats[0];
}

/**
 * Output category badge markup.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function hvn_realty_blog_the_category_badge( $post_id = 0 ) {
	$term = hvn_realty_blog_get_primary_category( $post_id );

	if ( ! $term instanceof WP_Term ) {
		return;
	}

	printf(
		'<a class="hvn-blog-card__category" href="%1$s" rel="category tag">%2$s</a>',
		esc_url( get_category_link( $term->term_id ) ),
		esc_html( $term->name )
	);
}

/**
 * Output post meta row (date + author).
 *
 * @return void
 */
function hvn_realty_blog_the_entry_meta() {
	?>
	<div class="hvn-blog-card__meta">
		<time class="hvn-blog-card__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
			<?php echo esc_html( get_the_date() ); ?>
		</time>
		<span class="hvn-blog-card__meta-separator" aria-hidden="true"></span>
		<span class="hvn-blog-card__author">
			<?php
			printf(
				/* translators: %s: post author display name */
				esc_html__( 'By %s', 'havenlytics-realty' ),
				'<a class="hvn-blog-card__author-link" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
			);
			?>
		</span>
	</div>
	<?php
}

/**
 * Output featured image for blog cards.
 *
 * @param string $size   Image size.
 * @param string $class  Image class attribute.
 * @return bool True when a thumbnail was output.
 */
function hvn_realty_blog_the_thumbnail( $size = 'hvn-realty-blog', $class = 'hvn-blog-card__image' ) {
	if ( ! has_post_thumbnail() ) {
		return false;
	}

	the_post_thumbnail(
		$size,
		array(
			'class'   => $class,
			'alt'     => the_title_attribute( array( 'echo' => false ) ),
			'loading' => 'lazy',
		)
	);

	return true;
}
