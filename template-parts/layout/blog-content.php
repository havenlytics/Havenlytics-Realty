<?php
/**
 * Blog loop shell — grid or list wrapper + pagination.
 *
 * @package Havenlytics_Realty
 */

$is_list      = function_exists( 'hvn_realty_get_blog_layout' ) && 'list' === hvn_realty_get_blog_layout();
$loop_classes = function_exists( 'hvn_realty_get_blog_loop_classes' )
	? hvn_realty_get_blog_loop_classes()
	: ( $is_list ? 'hvn-blog-list' : 'hvn-blog-grid hvn-cols-3' );
$grid_style   = function_exists( 'hvn_realty_get_blog_grid_style_attr' ) ? hvn_realty_get_blog_grid_style_attr() : '';
$card_slug    = function_exists( 'hvn_realty_get_blog_card_template' )
	? hvn_realty_get_blog_card_template()
	: 'grid';
?>

<?php if ( have_posts() ) : ?>

	<div class="<?php echo esc_attr( $loop_classes ); ?>"<?php echo $grid_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped in helper. ?>>
		<?php
		while ( have_posts() ) :
			the_post();
			if ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
				hvn_realty_get_blog_template_part( 'content', $card_slug );
			} else {
				get_template_part( 'templates/blog/content', $card_slug );
			}
		endwhile;
		?>
	</div>

	<?php
	if ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
		hvn_realty_get_blog_template_part( 'pagination' );
	} else {
		get_template_part( 'templates/blog/pagination' );
	}
	?>

<?php else : ?>

	<?php
	if ( function_exists( 'hvn_realty_get_blog_template_part' ) ) {
		hvn_realty_get_blog_template_part( 'content', 'none' );
	} else {
		get_template_part( 'templates/blog/content', 'none' );
	}
	?>

<?php endif; ?>
