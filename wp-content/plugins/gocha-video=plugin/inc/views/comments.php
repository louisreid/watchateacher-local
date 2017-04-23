<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

?>

<div
    class="gocha-video-commentarea gocha-focus-el <?php echo $customClass; ?>"
    id="<?php echo $commentclass; ?>"
    data-gocha-video="<?php echo $matches[1]; ?>"
    data-gocha-mintimediff="<?php echo $this->options['mintimediff']; ?>"
    data-gocha-commentopen="<?php echo $this->options['commentopen']; ?>"
    data-gocha-commentdisplay="<?php echo $this->options['commentdisplay']; ?>"
    data-gocha-commentdisplaymode="<?php echo $this->options['commentdisplaymode']; ?>"
    data-page-id="">
    <div class="gocha-video-player-wrapper">
        <?php echo $player; ?>
    </div>

    <?php if (comments_open()) : ?>
    <div class="gocha-video-controls" data-mode="<?php echo $this->options['mode']; ?>">
        <div class="clearfix-sm"></div>
        <div class="gocha-video-add-area g-active">
            <?php if($this->options['mode'] === 'range') : ?>
                <a href="#change" class="gocha-video-st gocha-video-start">
                    <span class="gtimetitle"><?php echo $this->lang['start']; ?></span>
                    <span class="gtimechange"><?php echo $this->lang['change']; ?></span>
                    <span class="gtimenumber">00:00</span>
                </a>
                <button class="gocha-video-add" disabled><?php echo $this->lang['form_add_' . $this->options['mode']]; ?></button>
                <a href="#change" class="gocha-video-st gocha-video-stop">
                    <span class="gtimenumber">00:00</span>
                    <span class="gtimetitle"><?php echo $this->lang['stop']; ?></span>
                    <span class="gtimechange"><?php echo $this->lang['change']; ?></span>
                </a>
            <?php else : ?>
                <button class="gocha-video-add">
                    <?php echo $this->lang['form_add_' . $this->options['mode']]; ?>
                </button>
            <?php endif; ?>
        </div>

        <button class="gocha-video-show buttons">
            <?php if('' === $comments_list) : ?>
                <?php echo $this->lang['comments_empty']; ?>
            <?php else : ?>
                <?php echo $this->lang['comments_show']; ?>
            <?php endif; ?>
        </button>
    </div>
    <?php else : ?>
    <div
        class="gocha-video-controls">
        <p class="gocha-video-comment-close">
            <?php _e('Comments are closed.', 'gocha-video-plugin' ); ?>
        </p>
    </div>
    <?php endif; ?>

    <?php if(!$this->options['hidetimeline']) : ?>
    <div class="gocha-video-bar-area">
        <div class="gocha-dynamic-seeker" data-video-duration="">
            <div class="gocha-bar">
                <div class="gocha-bar-time"></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="gocha-video-commentform">
        <?php echo $comments_form; ?>
    </div>

    <div class="gocha-video-commentbox <?php echo $commentclass; ?>">
        <ul>
            <?php echo $comments_list; ?>
        </ul>
    </div>
</div>
