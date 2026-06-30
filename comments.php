<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Havenlytics_Realty
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="hvn-theme-comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="hvn-theme-comments-title">
            <?php
            $hvn_realty_comment_count = get_comments_number();
            if ('1' === $hvn_realty_comment_count) {
                printf(
                    /* translators: 1: title. */
                    esc_html__('One thought on &ldquo;%1$s&rdquo;', 'havenlytics-realty'),
                    '<span>' . get_the_title() . '</span>'
                );
            } else {
                printf(
                    /* translators: 1: comment count number, 2: title. */
                    esc_html(_nx('%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $hvn_realty_comment_count, 'comments title', 'havenlytics-realty')),
                    number_format_i18n($hvn_realty_comment_count),
                    '<span>' . get_the_title() . '</span>'
                );
            }
            ?>
        </h2>

        <?php the_comments_navigation(); ?>

        <ol class="hvn-theme-comment-list">
            <?php
            wp_list_comments(
                array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 60,
                    'callback'    => null,
                )
            );
            ?>
        </ol>

        <?php the_comments_navigation(); ?>

        <?php if (!comments_open()) : ?>
            <p class="hvn-theme-no-comments"><?php esc_html_e('Comments are closed.', 'havenlytics-realty'); ?></p>
        <?php endif; ?>

    <?php endif; // Check for have_comments(). ?>

    <?php
    $commenter = wp_get_current_commenter();

    comment_form(
        array(
            'class_form'         => 'hvn-theme-comment-form',
            'title_reply'        => esc_html__('Leave a Comment', 'havenlytics-realty'),
            'title_reply_before' => '<h3 id="reply-title" class="hvn-theme-reply-title">',
            'title_reply_after'  => '</h3>',
            'class_submit'       => 'submit hvn-theme-btn hvn-theme-btn-primary hvn-theme-form-submit',
            'submit_button'      => '<button type="submit" name="%1$s" id="%2$s" class="%3$s">%4$s</button>',
            'submit_field'       => '<p class="form-submit hvn-theme-comment-form__submit">%1$s %2$s</p>',
            'comment_field'      => '<div class="form-group"><label for="comment">' . esc_html__('Comment', 'havenlytics-realty') . '</label><textarea id="comment" name="comment" class="hvn-theme-form-control" rows="6" required></textarea></div>',
            'fields'             => array(
                'author' => '<div class="form-group"><label for="author">' . esc_html__('Name', 'havenlytics-realty') . ' <span class="required">*</span></label><input id="author" name="author" type="text" class="hvn-theme-form-control" value="' . esc_attr($commenter['comment_author']) . '" required /></div>',
                'email'  => '<div class="form-group"><label for="email">' . esc_html__('Email', 'havenlytics-realty') . ' <span class="required">*</span></label><input id="email" name="email" type="email" class="hvn-theme-form-control" value="' . esc_attr($commenter['comment_author_email']) . '" required /></div>',
                'url'    => '<div class="form-group"><label for="url">' . esc_html__('Website', 'havenlytics-realty') . '</label><input id="url" name="url" type="url" class="hvn-theme-form-control" value="' . esc_attr($commenter['comment_author_url']) . '" /></div>',
            ),
        )
    );
    ?>

</div><!-- #comments -->