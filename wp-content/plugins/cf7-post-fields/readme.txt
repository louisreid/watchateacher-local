=== Plugin Name ===
Contributors: markusfroehlich
Donate link: http://bit.ly/2e9Bhcw
Tags: Contact Form 7, Contact, Contact Form, dynamic, post, post type, custom post types, custom post type, post fields, post field, select, radio, checkbox, post drop-down-menu, post checkboxes, post radio buttons, post select, post radio
Requires at least: 4.0
Tested up to: 4.7.3
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides a dynamic post selection, radio and checkbox field to your CF7 forms.

== Description ==

Contact Form 7 is a fantastic plugin for form. The post-fields extension enables you to create drop-down-menues, checkboxes and radio-buttons based on posts or other kinds of content (custom post types).

= Features of post fields =

* selection of the post type (posts, pages, custom post types)
* selection and limitation of categories (taxonomies)
* customized/individual formatting of the label
* configuring the value attribute
* customized sorting of the post type
* limitation of the post type based on its particular status (published, draft etc.)
* The standard value of the field can easily be selected by using $_GET or $_POST variables (see FAQ).

= Future developments =

* display of post images
* image radio-buttons/checkboxes

= Required Plugin =

* [Contact Form 7](https://wordpress.org/plugins/contact-form-7/) by Takayuki Miyoshi - Contact Form 7 can manage multiple contact forms, plus you can customize the form and the mail contents flexibly with simple markup.

= Languages =

* English
* German

== Installation ==

1. Download and install the required Contact Form 7 Plugin available at http://wordpress.org/extend/plugins/contact-form-7/
2. Upload 'contact-form-7-post-fields' to the '/wp-content/plugins/' directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. You will now have a "Post select", "post radio" and "post checkbox" tag option in the Contact Form 7 tag generator.

== Frequently Asked Questions ==

= Where can I find the new post fields and how can I use them? =

1. Make sure that you have installed and activated the required plugin [Contact Form 7](https://wordpress.org/plugins/contact-form-7/).
2. In the menu, navigate to the item "Contact", create a new or edit an existing form.
3. You can find 3 new fields in the tab "Form" now: "post drop-down-menu", "post checkboxes" and "post radio buttons".

= Why can't I find my own type of content (custom post type) in the list? =

The only types of content displayed are those declared as public.
See [register post type](https://codex.wordpress.org/Function_Reference/register_post_type).

= How can I make my form in the front end use a standard value automatically? =

This can easily be done by using $_GET or $_POST variables.

1. In the post field shortcode, add the option "default:get" or "default:post" ([instructions](http://contactform7.com/checkboxes-radio-buttons-and-menus/)), e.g. [post_select post_select-1 publish default:get post-type:post value-field:title orderby:title order:DESC "%title%"]
2. On your website, move to the form with the following $_GET parameters: http://www.yourdomain.at/contact/?field_name=post_id

= What kind of post meta keys can be used for the label  =

1. Single text meta keys
2. Sequential arrays will be changed in a string list (comma seperated)
3. Associative arrays are no supported

= I found a bug, what shall I do? =

If you have found a bug in my plugin, please send me an email with a short description.
I will fix the bug as soon as possible.

= You like my plugin and you'd like to support me? =

Thank you very much!
In case you want to show how much you appreciate my work, I'd be very grateful if you could give me positive rating with Wordpress-Page and/or donate a small amount to me.

== Screenshots ==

1. The post fields in action
2. Post select field generator

== Changelog ==

= 1.3 =
* Post meta keys available in the label formatting

= 1.2 =
* Changed the deprecated function wpcf7_add_shortcode to wpcf7_add_form_tag
* Translation fixes

= 1.1 =
* Translation fixes

= 1.0 =
* Initial Release

== Upgrade Notice ==

= 1.0 =
Check compatibility with latest Contact Form 7 and WordPress Version