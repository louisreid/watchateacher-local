<?php 


add_action( 'cf7_2_post_save-post', 'cf7_post_save_post',10,3);
function cf7_post_save_post($cf7_key, $submitted_data, $submitted_files){
	
}

add_filter( 'cf7_2_post_load-post', 'cf2_post_load_post',10,5);
function cf2_post_load_post( $field_value_pairs, $cf7_key, $form_fields, $form_field_options, $cf7_post_id){
  //$form_field_options options set in the form field tags
  //$cf7_post_id the cf7 form id in case you need to load the form object
  foreach($form_fields as $field=>$type){
    $field_value_pairs[$field] = 'post_title';//load your value
  }
  //if this is a saved draft form, you can set your mapped post id
  //it will be set as hidden field so you can map the (re)submission to the same post
  $field_value_pairs['map_post_id'] = $post_id;
  return $field_value_pairs;
}




