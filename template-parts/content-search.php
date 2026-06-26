<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Havenlytics_Realty
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('hvn-theme-page-content'); ?>>
    
    <?php if (has_post_thumbnail()) : ?>
        <div class="hvn-theme-page-featured-image">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>

    <div class="hvn-theme-page-content-inner">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'havenlytics-realty'),
                'after'  => '</div>',
            )
        );
        ?>
    </div>

    <?php if (get_edit_post_link()) : ?>
        <footer class="hvn-theme-page-footer">
            <?php edit_post_link(esc_html__('Edit', 'havenlytics-realty'), '<span class="edit-link">', '</span>'); ?>
        </footer>
    <?php endif; ?>
</article>