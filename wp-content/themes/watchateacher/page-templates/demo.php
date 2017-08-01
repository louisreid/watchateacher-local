<?php
/**
 * Template Name: Demo
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>



<script>
  function resizeIframe(obj) {
    obj.style.height = obj.contentWindow.document.body.scrollHeight + 1000 + 'px';
  }
</script>

<div style="padding: 10px; font-weight: 800; background-color: #fdcc52; text-align: center;">
    Demo session. To find out more about Watch A Teacher, <a style="color: #333; text-decoration: underline;" href="/find-out-more">click here.</a>
</div>

<iframe src="https://www.watchateacher.com/session/1500173327" frameborder="0" scrolling="no" onload="resizeIframe(this)" style="width: 100%;"/>

</div><!-- Wrapper end -->

<?php get_footer(); ?>