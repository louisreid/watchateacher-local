<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

?>
<form
    action="<?php echo str_replace('/index.php', '', get_home_url()); ?>/wp-comments-post.php"
    method="post"
    class="gocha-video-comment-form">
    <div class="gocha-form-status"></div>
    <fieldset>
        <input class="gocha-video-input" name="gocha_video_input" type="hidden" />
        <a rel="nofollow" class="gocha-cancel-reply" href="#cancel" style="display: none;">
            <?php _e('Cancel reply', 'gocha-video-plugin' ); ?>
        </a>
        <h2 class="gocha-form-haader">
            <?php _e('Leave a reply', 'gocha-video-plugin'); ?>
        </h2>

        <?php if (!is_user_logged_in()) : ?>
            <p style="margin-top:0px">
                <input
                    class="round m input"
                    name="author"
                    type="text"
                    id="author"
                    value=""
                    tabindex="1"
                    style="width:97%"
                    placeholder="<?php _e('Name', 'gocha-video-plugin' ); ?>" />
            </p>

            <p style="margin-top:0px">
                <input
                    class="round m input"
                    name="email"
                    type="text"
                    id="email"
                    value=""
                    tabindex="2"
                    style="width:97%"
                    placeholder="<?php _e('Email', 'gocha-video-plugin'); ?>" />
            </p>

            <p style="margin-top:0px">
                <input
                    class="round m input"
                    name="url"
                    type="text"
                    id="url"
                    value=""
                    tabindex="3"
                    style="width:97%"
                    placeholder="<?php _e('Website', 'gocha-video-plugin' ); ?>" />
            </p>
        <?php endif; ?>

        <p style="margin-top:20px">
            <textarea
                name="comment"
                cols="40"
                rows="4"
                id="comment"
                tabindex="4"
                style="width:97%"
                placeholder="<?php _e('Message*', 'gocha-video-plugin'); ?>"></textarea>
            <br/>
            <input
                name="submit"
                type="submit"
                id="submit"
                value="<?php _e('submit', 'gocha-video-plugin'); ?>"
                tabindex="5" />&nbsp;
        </p>
        <input
            type="hidden"
            name="comment_post_ID"
            value="<?php echo get_the_ID(); ?>"
            class="gocha_comment_post_ID">
        <input
            type="hidden"
            name="comment_parent"
            class="gocha-comment-parent"
            value="0">
    </fieldset>
</form>
