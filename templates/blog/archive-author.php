<?php
/**
 * Author archive header — avatar, name, bio, post count.
 *
 * @package Havenlytics_Realty
 */

$author_id = (int) get_queried_object_id();

if ( $author_id <= 0 ) {
	return;
}

$author_name = get_the_author_meta( 'display_name', $author_id );
$author_bio  = get_the_author_meta( 'description', $author_id );
$post_count  = count_user_posts( $author_id, 'post', true );
?>
<div class="hvn-blog-archive-author">
	<div class="hvn-blog-archive-author__avatar">
		<?php echo get_avatar( $author_id, 96, '', '', array( 'class' => 'hvn-blog-archive-author__avatar-image' ) ); ?>
	</div>
	<div class="hvn-blog-archive-author__body">
		<h1 class="hvn-theme-blog-title hvn-blog-archive-author__name">
			<?php echo esc_html( $author_name ); ?>
		</h1>
		<?php if ( $post_count > 0 ) : ?>
			<p class="hvn-blog-archive-author__count">
				<?php
				printf(
					/* translators: %s: number of published posts */
					esc_html( _n( '%s published post', '%s published posts', $post_count, 'havenlytics-realty' ) ),
					esc_html( number_format_i18n( $post_count ) )
				);
				?>
			</p>
		<?php endif; ?>
		<?php if ( $author_bio ) : ?>
			<div class="hvn-blog-archive-author__bio">
				<?php echo wp_kses_post( wpautop( $author_bio ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
