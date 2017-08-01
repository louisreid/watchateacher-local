<?php
/*
 * The base class from the modules
 * Author: Markus Froehlich
 */
if(!defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('wpcf7_post_fields_module') )
{
    class wpcf7_post_fields_module
    {
        /*
         * Data Fields
         */
        protected $label_tags = array('%title%', '%date%', '%time%', '%excerpt%', '%slug%', '%id%');
        protected $post_status = array('publish', 'pending', 'draft', 'future');

        /*
         * Get Post Values
         */
        protected function get_post_values($tag)
        {
            /*
             * Post Variables from the Field
             */
            $atts = array();
            $atts['post_type'] = $tag->get_option('post-type', '', true);
            $atts['value-field'] = $tag->get_option('value-field', '', true);
            $atts['label'] = (string) reset( $tag->values );
            $atts['orderby'] = $tag->get_option('orderby', '', true);
            $atts['order'] = $tag->get_option('order', '', true);

            // $post_taxonomies = array();
            $labels = array();
            $values = array();
            $ids    = array();

            if(!empty($atts['post_type']))
            {
                // General post_args
                $post_args = array(
                    'post_type'     => $atts['post_type'],
                    'nopaging'      => true
                );

                // Set post_status to WP Query
                foreach($this->post_status as $status)
                {
                    if($tag->has_option($status)) {
                        $post_args['post_status'][] = $status;
                    }
                }

                // Set orderby to WP Query
                if(isset($atts['orderby']))
                {
                    $post_args['orderby'] = $atts['orderby'];
                    $post_args['order'] = (isset($atts['order']) && $atts['order'] === 'DESC') ? 'DESC' : 'ASC';
                }

                // Get taxonomies from post type
                $object_taxonomies = get_object_taxonomies($atts['post_type'], 'names');

                if(count($object_taxonomies) > 0)
                {
                    $tax_queries = array();
                    foreach($object_taxonomies as $taxonomy)
                    {
                        // Term slugs from the field
                        if($terms = $tag->get_option($taxonomy, '', true))
                        {
                            $tax_queries[] = array(
                                'taxonomy' => $taxonomy,
                                'field'    => 'slug',
                                'terms'    => explode('|', $terms)
                            );
                        }
                    }

                    // Set tax_query to WP Query
                    if(count($tax_queries) > 0)
                    {
                        $relation = $tag->get_option('category-relation', '', true);

                        $post_args['tax_query'] = $tax_queries;
                        $post_args['tax_query']['relation'] = ($relation) ? $relation : 'OR';
                    }
                }

                // Field-Filter for custom WP Query
                $post_args = apply_filters($tag->name.'_get_posts', $post_args, $tag, $atts);

                // Get all posts from Post Type
                $select_posts = get_posts($post_args);

                foreach ($select_posts as $post)
                {
                    $ids[]      = $post->ID;
                    $labels[]   = !empty($atts['label']) ? $this->replace_label_tags($atts['label'], $post) : $post->post_title;

                    // Set Value Field
                    switch ($atts['value-field'])
                    {
                        case 'title':
                            $value = $post->post_title;
                            break;
                        case 'slug':
                            $value = $post->post_name;
                            break;
                        case 'id':
                            $value = $post->ID;
                            break;
                        default:
                            $value = $post->post_title;
                            break;
                    }

                    $values[] = $value;
                }

                wp_reset_query();
            }

            return array(
                'labels'        => $labels,
                'values'        => $values,
                'ids'           => $ids
            );
        }

        /*
         * Replace post attributes and meta_key  tags from the label string
         */
        private function replace_label_tags($label, $post)
        {
            // Get the default post attributes
            $default_post_atts = array(
                $post->post_title,          // %title%
                get_the_date('',  $post),   // %date%
                get_the_time('',  $post),   // %time%
                $post->post_excerpt,        // %excerpt%
                $post->post_name,           // %slug%
                $post->ID                   // %id%
            );

            // Replace all default post tags in the field label
            $new_label = str_replace($this->label_tags, $default_post_atts, $label);

            // There are still label tags available
            if(substr_count($new_label, '%') >= 2)
            {
                // Get all meta keys from the current post
                $all_meta_keys = get_post_custom_keys($post->ID);

                // Loop all post meta keys
                foreach($all_meta_keys as $meta_key)
                {
                    $meta_key_tag = '%'.$meta_key.'%';

                    // Search for a post meta keys in the field label
                    if(strpos($new_label, $meta_key_tag) !== false)
                    {
                        $meta_value = get_post_meta($post->ID, $meta_key, true);

                        // Check if meta value is sequential array
                        if(is_array($meta_value) && count($meta_value) > 0)
                        {
                            // Check if the array is assoc
                            $is_assoc_array = array_keys($meta_value) !== range(0, count($meta_value) - 1);

                            // Change the array meta value in a string list
                            $meta_value = !$is_assoc_array ? implode(', ', $meta_value) : '';
                        }

                        // Replace the post meta keys in the field label
                        $new_label = str_replace($meta_key_tag, $meta_value, $new_label);
                    }
                }
            }

            return $new_label;
        }

        /*
         * Template for the Post Field Selection in the Table
         */
        public function get_post_generator_template($args)
        {
            ?>
            <tr>
                <th scope="row"><?php echo esc_html( __( 'Post type', 'cf7-post-fields' ) ); ?></th>
                <td id="<?php echo esc_attr( $args['content'] . '-post-type' ); ?>">
                    <?php
                        $first_post_type = '';
                        foreach(get_post_types(array('public' => true), 'objects') as $post_type)
                        {
                            if(empty($first_post_type))
                                $first_post_type = $post_type->name;

                            $count_posts = wp_count_posts($post_type->name);
                            $label = '<b>'.$post_type->label.'</b> ('.__('Published').': '.$count_posts->publish.')';

                            echo '
                                <label>
                                    <input type="radio" name="post-type" class="option" value="'.$post_type->name.'" '.checked('post', $post_type->name, false).'>'.$label.'
                                </label>
                                <br>';
                        }
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php echo esc_html( _x( 'Post', 'post type singular name' ).' '.__( 'Categories' ) ); ?></th>
                <td>
                    <fieldset id="<?php echo esc_attr( $args['content'] . '-post-taxonomies' ); ?>">
                        <?php
                        if(!empty($first_post_type))
                        {
                            $object_taxonomies = get_object_taxonomies($first_post_type, 'object');

                            if(count($object_taxonomies) > 0)
                            {
                                foreach($object_taxonomies as $taxonomy) {
                                    echo '<input type="text" value="" class="oneline option" name="'.$taxonomy->name.'" placeholder="'.$taxonomy->label.'"><br>';
                                }

                                _e('Relationship').':';
                                ?>
                                <label><input type="radio" name="category-relation" class="option" value="OR" checked /> <?php echo esc_html( __('OR') ); ?></label>
                                <label><input type="radio" name="category-relation" class="option" value="AND" /> <?php echo esc_html( __('AND') ); ?></label>
                                <?php
                            }
                            else
                            {
                                _e('No categories found.');
                            }
                        }
                        ?>
                    </fieldset>
                    <span class="description">
                        <?php _e('Use pipe-separated term slugs (e.g. united-states|germany|austria) per field.', 'cf7-post-fields'); ?>
                    </span>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-label' ); ?>"><?php echo esc_html( __( 'Label format', 'cf7-post-fields' ) ); ?></label></th>
                <td>
                    <input type="text" name="values" value="%title%" class="oneline" id="<?php echo esc_attr( $args['content'] . '-label' ); ?>" />
                    <br>
                    <span class="description">
                        <?php echo __('Attributes').': <code>'.implode('</code> <code>', $this->label_tags).'</code> <code>%meta_key%</code>'; ?>
                    </span>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php echo esc_html( __( 'Value field', 'cf7-post-fields' ) ); ?></th>
                <td>
                    <label><input type="radio" name="value-field" class="option" value="title" checked /> <?php echo esc_html( __('Title') ); ?></label>
                    <label><input type="radio" name="value-field" class="option" value="slug" /> <?php echo esc_html( __('Slug') ); ?></label>
                    <label><input type="radio" name="value-field" class="option" value="id" /> <?php echo esc_html( __('ID') ); ?></label>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php echo esc_html( __( 'Status' ) ); ?></th>
                <td>
                    <label><input type="checkbox" name="publish" class="option" checked /> <?php echo esc_html( _x( 'Published', 'post status' ) ); ?></label>
                    <label><input type="checkbox" name="pending" class="option" /> <?php echo esc_html( _x( 'Pending', 'post status' ) ); ?></label>
                    <label><input type="checkbox" name="draft" class="option" /> <?php echo esc_html( _x( 'Draft', 'post status' ) ); ?></label>
                    <label><input type="checkbox" name="future" class="option" /> <?php echo esc_html( _x( 'Scheduled', 'post status' ) ); ?></label>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php echo esc_html( __( 'Sort order', 'cf7-post-fields' ) ); ?></th>
                <td>
                    <label><input type="radio" name="orderby" class="option" value="title" checked /> <?php echo esc_html( __('Title') ); ?></label><br>
                    <label><input type="radio" name="orderby" class="option" value="date" /> <?php echo esc_html( __('Date/Time') ); ?></label><br>
                    <label><input type="radio" name="orderby" class="option" value="author" /> <?php echo esc_html(__('Author') ); ?></label><br>
                    <label><input type="radio" name="orderby" class="option" value="rand" /> <?php echo esc_html( __('Random') ); ?></label><br>
                    <label><input type="radio" name="orderby" class="option" value="menu_order" /> <?php echo esc_html( __('Menu order') ); ?></label><br>
                    <label><input type="radio" name="orderby" class="option" value="none"  /> <?php echo esc_html( __('None') ); ?></label>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-order' ); ?>"><?php echo esc_html( __( 'Order' ) ); ?></label></th>
                <td>
                    <label><input type="radio" name="order" class="option" value="DESC" checked /> <?php echo esc_html( __('Descending') ); ?></label>
                    <label><input type="radio" name="order" class="option" value="ASC" /> <?php echo esc_html(__('Ascending') ); ?></label>
                </td>
            </tr>
            <?php
        }

        /*
         * Javascript for the Post Field Selection in the Table
         */
        public function enqueue_post_field_javascript($args)
        {
            ?>
            <script type="text/javascript">
                jQuery(function($) {

                    $('#<?php echo esc_attr( $args['content'] . '-post-type' ); ?> input[type=radio][name=post-type]').change(function() {

                        var post_type = $(this).val();

                        var tg_name_field = $('#<?php echo esc_attr( $args['content'] . '-name' ); ?>');
                        var tg_tax_fieldset = $('#<?php echo esc_attr( $args['content'] . '-post-taxonomies' ); ?>');

                        // Empty taxonomy fieldset
                        tg_tax_fieldset.empty();

                        // Empty tag field
                        tg_name_field.trigger('change');

                        // Show loader
                        tg_tax_fieldset.html('<span class="spinner is-active" style="float:none;"></span>');

                        // Ajax request to get all taxonomies from a post type
                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                action: 'wpcf7_post_fields_get_taxonomies',
                                security : '<?php echo wp_create_nonce('wpcf7-post-field-tax-nonce'); ?>',
                                post_type: post_type
                            },
                            success: function(result)
                            {
                                tg_tax_fieldset.empty();

                                if(result.success == true)
                                {
                                    var count_tax = 0;
                                    $.each( result.taxonomies, function( key, value ) {

                                        var tax_field = $("<input type='text' value=''>").attr("class", "oneline option").attr("name", key).attr("placeholder", value);

                                        // Append the text field to the taxonomy fieldset
                                        tg_tax_fieldset.append(tax_field).append('<br />');

                                        // Hack to trigger the change event from the contact form 7 base
                                        tax_field.change(function() {
                                            tg_name_field.trigger('change');
                                        });

                                        count_tax++;
                                    });

                                    // Categories found
                                    if(count_tax > 0) {
                                        // Add Relationship radios
                                        tg_tax_fieldset.append('<?php echo __('Relationship').': '; ?>');
                                        tg_tax_fieldset.append($('<label>').append($("<input type='radio'>").attr("name", "category-relation").attr("class", "option").attr("value", 'OR').attr("checked", 'checked')).append('OR'));
                                        tg_tax_fieldset.append('&nbsp;');
                                        tg_tax_fieldset.append($('<label>').append($("<input type='radio'>").attr("name", "category-relation").attr("class", "option").attr("value", 'AND')).append('AND'));

                                        // Register the change event
                                        tg_tax_fieldset.find("input[name='category-relation']").change(function() {
                                            tg_name_field.trigger('change');
                                        });

                                        // Trigger the change event now to set the category-relation
                                        tg_name_field.trigger('change');
                                    }
                                    else {
                                        tg_tax_fieldset.html('<?php  _e('No categories found.'); ?>');
                                    }
                                }
                            },
                            error: function() {
                                tg_tax_fieldset.html('<?php  _e('An unknown error occurred'); ?>');
                            }
                        });
                    });
                });
            </script>
            <?php
        }
    }
}