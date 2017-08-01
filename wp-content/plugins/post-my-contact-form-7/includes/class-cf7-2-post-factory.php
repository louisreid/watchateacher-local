<?php
class Cf7_2_Post_Factory {
  /**
	 * The properties of the mapped custom post type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $post_properties    an array of properties.
	 */
  private $post_properties;
  /**
	 * The properties of the mapped custom taxonomy.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $taxonomy_properties    an array of properties with   $taxonomy_slug=>array('source'=>$taxonomy_source, 'singular_name'=>$value, 'name'=>$plural_name)
	 */
  private $taxonomy_properties;
  /**
	 * The the CF7 post ID.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $cf7_post_ID    the CF7 post ID.
	 */
  private $cf7_post_ID;
  /**
   * The the CF7 post unique key.
   *
   * @since    1.2.7
   * @access   private
   * @var      string    $cf7_key    the unique key which can be used to identfy this form.
   */
  private $cf7_key;
  /**
	 * The CF7 form fields.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $cf7_form_fields    an array containing CF7 fields, {'field name'=>'field type'}.
	 */
  private $cf7_form_fields;
  /**
	 * The CF7 form fields options.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      array    $cf7_form_fields_options    an array containing CF7 field name and its array of options, {'field name'=>array()}.
	 */
  private $cf7_form_fields_options;
  /**
	 * The CF7 form fields mapped to post fields.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $post_map_fields    an array mapped CF7 fields, to default
   * post fields {'form field name'=>'post field'}.
	 */
  private $post_map_fields;
  /**
	 * The CF7 form fields mapped to post fields.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $post_map_meta_fields    an array mapped CF7 fields, to post
   * custom meta fields  {'form field name'=>'post field'}.
	 */
  private $post_map_meta_fields;
  /**
	 * The CF7 form fields mapped to post fields.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $post_map_taxonomy    an array mapped CF7 fields, to post
   * custom taxonomy  {'form field name'=>'taxonomy slug'}.
	 */
  private $post_map_taxonomy;
  /**
	 * The CF7 form fields values to set as localize_script.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $localise_values    an array CF7 fields=>values.
	 */
  private $localise_values;
  /**
   * Default Construct a Cf7_2_Post_Factory object.
   *
   * @since    1.0.0
   * @param    int    $cf7_post_id the ID of the CF7 form.
   */
  protected function __construct($cf7_post_id){
    $this->cf7_form_fields = array();
    $this->post_map_fields = array();
    $this->post_map_taxonomy = array();
    $this->post_properties = array();
    $this->taxonomy_properties = array();
    $this->localise_values=array();
    $this->cf7_post_ID = $cf7_post_id;
    $post = get_post($cf7_post_id);
    $this->cf7_key = $post->post_name;
  }
  /**
   *Enqueue the localised script
   *This function is called by the hook in the
   * @since 1.3.0
   * @param      string    $p1     .
   * @return     string    $p2     .
  **/
  public function enqueue_localised_script($handle, $field_and_values = array()){
    $values = array_diff($field_and_values, $this->localise_values);
    wp_localize_script($handle, 'cf7_2_post_local', $values);
  }
  /**
  * Initialise the factory for an existing mapping
  * @since 1.0.0
  * @param $factory_source if this mapped post was created by the factory
  */
  protected function init_new_factory($post_type,$singular_name,$plural_name){
    //set some default values
    $this->post_properties=array(
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => false,
      'show_in_nav_menus'     => false,
      'can_export'            => true,
      'has_archive'           => true,
      'exclude_from_search'   => true,
      'publicly_queryable'    => false
    );
    $this->post_properties['map']='draft';
    $this->post_properties['type']=$post_type;
    $this->post_properties['version'] = CF7_2_POST_VERSION;
    $this->post_properties['singular_name']=$singular_name;
    $this->post_properties['plural_name']=$plural_name;
    $this->post_properties['type_source'] = 'factory';
    $this->post_properties['$cf7_title']=get_the_title($this->cf7_post_ID);

    $this->post_properties['taxonomy']=array();
    //supports
    $this->post_properties['supports'] = array(
      'title', 'editor', 'excerpt', 'author',
      'thumbnail', 'revisions', 'custom-fields' );
    //filter the support capabilities for more user customisation
    $this->post_properties['supports'] = apply_filters('cf7_2_post_supports_'.$post_type, $this->post_properties['supports']);
    //make sure we have custom-fields support
    if( !in_array('custom-fields',$this->post_properties['supports']) ){
      $this->post_properties['supports'][]='custom-fields';
    }
    //capabilities
    $reference = array(
        'edit_post' => '',
        'edit_posts' => '',
        'edit_others_posts' => '',
        'publish_posts' => '',
        'read_post' => '',
        'read_private_posts' => '',
        'delete_post' => ''
    );
    $capabilities = array_filter(apply_filters('cf7_2_post_capabilities_'.$post_type, $reference));
    $diff = array_diff_key($reference, $capabilities);
    if( empty( $diff ) ) {
      $this->post_properties['capabilities'] = $capabilities;
      $this->post_properties['map_meta_cap'] = true;
    }else{ //some keys are not set, so capabilities will not work
      //set to defaul post capabilities
      $this->post_properties['capability_type'] = 'post';
    }
  }
  /**
   * Function to display a dropdown list of taxonomies
   * Called by the dashbaord page.
   * @since 1.1.0
   * @param      string    $taxonomy_slug  slug of taxonomy to show as selected    .
   * @return     string    html select element.
  **/
  public function get_taxonomy_listing($taxonomy_slug=null){
    $result = '';
    if('publish' == $this->post_properties['map']){
      $result .= '<select disabled>';
    }else{
        $result .= '<select class="taxonomy-list">';
    }
    if('' === $taxonomy_slug){
      $result .= '<option value="" data-name="" >Choose a Taxonomy</option>';
    }
    $default_slug = sanitize_title( $this->get('singular_name') ).'_categories';
    $result .= '<option class="factory-taxonomy" value="'.$default_slug.'" data-name="New Category" class="factory-taxonomy">New Categories</option>';
    if(!empty($taxonomy_slug) &&
    isset($this->taxonomy_properties[$taxonomy_slug])){
      $taxonomy = $this->taxonomy_properties[$taxonomy_slug];
      $result .= '<option selected data-name="'.$taxonomy['singular_name'].'" value="'.$taxonomy_slug.'" class="'.$taxonomy['source'].'-taxonomy">';
      $result .= $taxonomy['name'];
      $result .= '</option>';
    }

    $system_taxonomies = get_taxonomies( array('public'=>true, '_builtin' => false), 'objects' );
    //inset the default post tags and category
    $result .= '<option value="post_tag" data-name="Post Tag" class="system-taxonomy">Post Tags</option>';
    $result .= '<option value="category" data-name="Post Category" class="system-taxonomy">Post Categories</option>';
    foreach($system_taxonomies as $taxonomy){
      if( !empty($taxonomy_slug) && $taxonomy_slug==$taxonomy->name ) continue;
      $result .= '<option value="'.$taxonomy->name.'" data-name="'.$taxonomy->labels->singular_name.'" class="system-taxonomy">';
      $result .= $taxonomy->labels->name;
      $result .= '</option>';
    }
    $result .= '</select>';

    return $result;
  }
  /**
	 * Get a factory object for a CF7 form.
	 *
	 * @since    1.0.0
   * @param  int  $cf7_post_id  cf7 post id
   * @return Cf7_2_Post_Factory  a factory oject
   */
  public static function get_factory( $cf7_post_id ){
    //check if the cf7 form already has a mapping
    $post_type = get_post_meta($cf7_post_id,'_cf7_2_post-type',true);
    $map = get_post_meta($cf7_post_id,'_cf7_2_post-map',true);
    $post_type_source = get_post_meta($cf7_post_id,'_cf7_2_post-type_source',true);
    $factory = null;
    //debug_msg('type='.$post_type);
    if(empty($post_type)){ //let's create a new one
      $factory = new self($cf7_post_id);
      $form = WPCF7_ContactForm::get_instance($cf7_post_id);
      $post_type_source = 'factory';
      $post_type = $factory->cf7_key;
      $singular_name = ucfirst( preg_replace('/[-_]+/',' ',$form->name()) );
      $plural_name = $singular_name;
      if( 's'!= substr($plural_name,-1) ) $plural_name.='s';
      $factory->init_new_factory($post_type,$singular_name,$plural_name);
    }else{

      $factory = new self($cf7_post_id);
      if('system' == $post_type_source && 'draft' == $map){
        $form = WPCF7_ContactForm::get_instance($cf7_post_id);
        $singular_name = ucfirst( preg_replace('/[-_]+/',' ',$form->name()) );
        $plural_name = $singular_name;
        $factory->init_new_factory($post_type, $singular_name, $plural_name);
      }
      $factory->post_properties['type_source']=$post_type_source;
      $factory->load_post_mapping();

     }
     return $factory;
   }
   /**
 	 * Get the CF7 post id.
 	 *
 	 * @since    1.0.0
   * @return int the cf7 form post ID
    */
   public function get_cf7_post_id(){
     return $this->cf7_post_ID;
   }
  /**
	 * Store the mapping in the CF7 post & create the custom post mapping.
	 * This is called by the plugin admin class function ajax_save_post_mapping whih is hooked to the ajax form call
	 * @since    1.0.0
   * @param   array   $data   an array containing the admin form data, $_POST
   * @param   boolean   $create_post_mapping  if false it will only save the mapping but not
   * create the custom post for saving user form inputs.  If it is a system post, this flag is ignored.
   * @return  boolean   true if successful
   */
  public function save($data, $create_post_mapping){
    //let's  update the properties
    //is this a factory post or a system post?
    if( isset($data['mapped_post_type_source']) && isset($data['mapped_post_type']) ) {
      $this->post_properties['type_source'] = $data['mapped_post_type_source'];
      $this->post_properties['type'] = $data['mapped_post_type'];
      $this->post_properties['version'] = CF7_2_POST_VERSION;

      switch($this->post_properties['type_source']){
        case 'system':
          return $this->set_system_mapping($data, $create_post_mapping);
          break;
        case 'factory':
          return $this->set_factory_mapping($data, $create_post_mapping);
          break;
      }
    }else{
      return false;
    }
  }
  /**
  * Setups the form to existing post mapping
  * @since 1.2.7
  * @param   array   $data   an array containing the admin form data, $_POST
  * @param   boolean   $create_post_mapping  if false it will only save the mapping but not
  * create the custom post for saving user form inputs.  If it is a system post, this flag is ignored.
  * @return  boolean   true if successful
  */
  protected function set_system_mapping($data, $create_post_mapping){
    //reset the properties, this is now being published
    $this->post_properties = array();
    $this->post_properties['type_source'] = $data['mapped_post_type_source'];
    $this->post_properties['type'] = $data['mapped_post_type'];
    $this->post_properties['taxonomy'] = array();

    if($create_post_mapping){
      $this->post_properties['map']='publish';
    }else{
      $this->post_properties['map']='draft';
    }
    //debug_msg($this->post_properties, 'saving system post ');
    foreach($this->post_properties as $key=>$value){
      //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
      update_post_meta($this->cf7_post_ID, '_cf7_2_post-'.$key,$value);
    }
    return true;
  }
  /**
  * Setups the form to new post mapping
  * @since 1.2.7
  * @param   array   $data   an array containing the admin form data, $_POST
  * @param   boolean   $create_post_mapping  if false it will only save the mapping but not
  * create the custom post for saving user form inputs.  If it is a system post, this flag is ignored.
  * @return  boolean   true if successful
  */
  protected function set_factory_mapping($data, $create_post_mapping){
    $is_factory_map = true;
    //check if we need to create a post
    $create_post = false;
    if( isset($data['mapped_post_type_source']) && 'factory'==$data['mapped_post_type_source'] &&
    $create_post_mapping){
      $create_post = true;
    }

    //reset, as only checked input field are submitted and will set to true
    $this->post_properties = array_merge(
      $this->post_properties,
      array(
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false
      )
  );
    //initial supports, set the rest from the admin $_POST
    $this->post_properties['supports'] = array('custom-fields' );
    //make sure the arrays are initialised
    $this->post_properties['taxonomy']=array();
    $this->post_map_taxonomy=array();
    $this->post_map_meta_fields = array();
    //lets load all fields
    $len_mapped_post = strlen('mapped_post_');
    $len_cf7_2_post_map = strlen('cf7_2_post_map-');
    $len_cf7_2_post_map_meta = strlen('cf7_2_post_map_meta_value-');
    /*$len_cf7_2_post_map_taxonomy = strlen('cf7_2_post_map_taxonomy_value-');
    $len_cf7_2_post_taxonomy_slug = strlen('cf7_2_post_map_taxonomy_slug-');
    $len_cf7_2_post_taxonomy_names = strlen('cf7_2_post_map_taxonomy_names-');*/
    $old_cf7_post_metas = get_post_meta($this->cf7_post_ID);

    foreach($data as $field => $value){
      if(empty($value)) continue;
      //debug_msg($field."=".$value,"saving...");
      switch (true){
        case ('mapped_post_menu_position'==$field && $is_factory_map): //properties:
        case ('mapped_post_type'==$field && $is_factory_map):
        case ('mapped_post_type_source'==$field && $is_factory_map):
        case ('mapped_post_singular_name'==$field && $is_factory_map):
        case ('mapped_post_plural_name'==$field && $is_factory_map):
          $this->post_properties[substr($field,$len_mapped_post)]=$value;
          break;
        case ( (0 === strpos($field,'mapped_post_')) && $is_factory_map ): //properties
          $this->post_properties[substr($field,$len_mapped_post)]=true;
          //debug_msg("PROPERTIES: ".$value."=".substr($field,$len_mapped_post).",".strpos($field,'mapped_post_'));
          break;
        case ( 0 === strpos($field,'cf7_2_post_map-') ): //cf7 form field => post field
          $post_field = substr($field,$len_cf7_2_post_map);
          $this->post_map_fields[$value]=$post_field;
          //capture the supports settings
          switch($post_field){
            case 'title':
            case 'excerpt':
            case 'author':
            case 'thumbnail':
            case 'editor':
              $this->post_properties['supports'][]=$post_field;
              break;
            default:
              break;
          }
          break;
        case (0 === strpos($field,'cf7_2_post_map_meta_value-') ): //cf7 form field => post meta field
          $this->post_map_meta_fields[$value]=substr($field,$len_cf7_2_post_map_meta);
          //debug_msg("POST FIELD: ".$value."=".substr($field,$len_cf7_2_post_map_meta));
          break;
      }
    }
    //let's save the properties if this is a factory mapping
    if($is_factory_map){
      //let's get the capabilities
      $reference = array(
          'edit_post' => '',
          'edit_posts' => '',
          'edit_others_posts' => '',
          'publish_posts' => '',
          'read_post' => '',
          'read_private_posts' => '',
          'delete_post' => ''
      );
      $capabilities = array_filter(apply_filters('cf7_2_post_capabilities_'.$this->post_properties['type'], $reference));
      $diff=array_diff_key($reference, $capabilities);
      if( empty( $diff ) ) {
        $this->post_properties['capabilities'] = $capabilities;
        $this->post_properties['map_meta_cap'] = true;
      }else{ //some keys are not set, so capabilities will not work
        //set to defaul post capabilities
        $this->post_properties['capability_type'] = 'post';
      }
      //is this a draft save?
      if($create_post){
        $this->post_properties['map']='publish';
      }else{
        $this->post_properties['map']='draft';
      }

      foreach($this->post_properties as $key=>$value){
        //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
        update_post_meta($this->cf7_post_ID, '_cf7_2_post-'.$key,$value);
        //clear previous values.
        if(isset($old_cf7_post_metas['_cf7_2_post-'.$key]) ){
          unset($old_cf7_post_metas['_cf7_2_post-'.$key]);
        }
      }
    }
    //let'save the mapping of cf7 form fields to post fields
    foreach($this->post_map_fields as $cf7_field=>$post_field){
      //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
      update_post_meta($this->cf7_post_ID, 'cf7_2_post_map-'.$post_field,$cf7_field);
      if(isset($old_cf7_post_metas['cf7_2_post_map-'.$post_field]) ){
        unset($old_cf7_post_metas['cf7_2_post_map-'.$post_field]);
      }
    }
    foreach($this->post_map_meta_fields as $cf7_field=>$post_field){
      //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
      update_post_meta($this->cf7_post_ID, 'cf7_2_post_map_meta-'.$post_field,$cf7_field);
      if(isset($old_cf7_post_metas['cf7_2_post_map_meta-'.$post_field]) ){
        unset($old_cf7_post_metas['cf7_2_post_map_meta-'.$post_field]);
      }
    }
    //clear any old values left.
    foreach($old_cf7_post_metas as $key=>$value){
      switch(true){
        case (0 === strpos($key,'_cf7_2_post-')):
        case (0 === strpos($key,'cf7_2_post_map-')):
        case (0 === strpos($key,'cf7_2_post_map_meta-')):
          delete_post_meta($this->cf7_post_ID, $key);
          //debug_msg('deleting: '.$key);
          break;
      }
    }
    //save the taxonomy mapping
    $this->save_taxonomies($data, $is_factory_map, $old_cf7_post_metas);

    return true;
  }
  /**
   * This function is called when a mapping is updated
   * called by Cf7_2_Post_Admin::ajax_save_post_mapping()
   *
   * @since 1.0.0
   * @param      string    $date     $_POST data array submitted.
   * @return     boolean    true is successful.
  **/
  public function update($data){

    if( isset($data['mapped_post_type_source']) ) {
      $this->post_properties['type_source'] = $data['mapped_post_type_source'];

      $is_factory_map = false;
      switch($this->post_properties['type_source']){
        case 'system': //reset the properties
          $this->set_system_post_properties($data['mapped_post_type']);
          break;
        case 'factory':
          $is_factory_map = true;
          //TODO usee if a new meta field has been mapped
          break;
      }
      //let's save any new taxonomies that have been added.
      return $this->save_taxonomies($data, $is_factory_map);
    }else{
      return false;
    }
  }
  /**
   * Store the taxonomy mapping in the CF7 post & creates the custom post mapping.
   *
   * @since 1.0.0
   * @param   array   $data   an array containing the admin form data, $_POST
   * @return  boolean   true if successful
  **/
  protected function save_taxonomies($data, $is_factory_map, $old_fields = array()){
    /*
    the taxonomy field names are built using the slug, such as
      cf7_2_post_map_taxonomy_names-<taxonomy_slug>
      so we need to keep track of the field name prefix length to strip the slug
    */
    $len_cf7_2_post_map_taxonomy = strlen('cf7_2_post_map_taxonomy_value-');
    $len_cf7_2_post_taxonomy_slug = strlen('cf7_2_post_map_taxonomy_slug-');
    $len_cf7_2_post_taxonomy_names = strlen('cf7_2_post_map_taxonomy_names-');
    $len_cf7_2_post_taxonomy_source = strlen('cf7_2_post_map_taxonomy_source-');
    //keep track of all the taxonomy slugs in the the slugs array
    $slugs=array();
    $post_map_taxonomy = array();
    //debug_msg($data);
    foreach($data as $field => $value){
      if(empty($value)) continue;
      //debug_msg($field."=".$value,"saving...");
      switch(true){
        case (0 === strpos($field,'cf7_2_post_map_taxonomy_source-') ): //taxonomy mapping
          $slug = substr($field,$len_cf7_2_post_taxonomy_source);
          if( !isset($this->taxonomy_properties[$slug]) ){
            $this->taxonomy_properties[$slug] =  array();
          }
          $this->taxonomy_properties[$slug]['source'] = $value;
          //debug_msg("POST FIELD: ".$value."=".substr($field,$len_cf7_2_post_map_meta));
          break;
        case (0 === strpos($field,'cf7_2_post_map_taxonomy_names-') ): //taxonomy mapping
          $slug = substr($field,$len_cf7_2_post_taxonomy_names);
          if( !isset($this->taxonomy_properties[$slug]) ){
            $this->taxonomy_properties[$slug] =  array();
          }
          $this->taxonomy_properties[$slug]['name'] = $value;
          //debug_msg("POST FIELD: ".$value."=".substr($field,$len_cf7_2_post_map_meta));
          break;
        case (0 === strpos($field,'cf7_2_post_map_taxonomy_name-') ): //taxonomy mapping
          $slug = substr($field,$len_cf7_2_post_taxonomy_slug);
          if( !isset($this->taxonomy_properties[$slug]) ){
            $this->taxonomy_properties[$slug] =  array();
          }
          $this->taxonomy_properties[$slug]['singular_name'] = $value;
          //debug_msg("POST FIELD: ".$value."=".substr($field,$len_cf7_2_post_map_meta));
          break;
        case (0 === strpos($field,'cf7_2_post_map_taxonomy_slug-') ): //taxonomy mapping
          if( !isset($this->taxonomy_properties[$value]) ){
            $this->taxonomy_properties[$value] =  array();
          }
          $slugs[]= $value;
          //debug_msg("found slug ".$value);
          //debug_msg("POST FIELD: ".$value."=".substr($field,$len_cf7_2_post_map_meta));
          break;
        case (0 === strpos($field,'cf7_2_post_map_taxonomy_value-') ): //taxonomy field mapping
          $slug = substr($field,$len_cf7_2_post_map_taxonomy);
          $post_map_taxonomy[$value] = $slug;
          //debug_msg("POST FIELD: ".$value."=".substr($field,$len_cf7_2_post_map_meta));
          break;
      }
    }
    if($is_factory_map){
      //save the taxonomy mappings
      //debug_msg($slugs);
      foreach($slugs as $slug){
        //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
        if(isset($old_fields['cf7_2_post_map_taxonomy_source-'.$slug]) ){
          unset($old_fields['cf7_2_post_map_taxonomy_source-'.$slug]);
          unset($old_fields['cf7_2_post_map_taxonomy_names-'.$slug]);
          unset($old_fields['cf7_2_post_map_taxonomy_name-'.$slug]);
        }
        update_post_meta($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_source-'.$slug,$this->taxonomy_properties[$slug]['source']);
        update_post_meta($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_names-'.$slug,$this->taxonomy_properties[$slug]['name']);
        update_post_meta($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_name-'.$slug,$this->taxonomy_properties[$slug]['singular_name']);
      }
      //clear any old values left.
      foreach($old_fields as $key=>$value){
        switch(true){
          case (0 === strpos($key,'cf7_2_post_map_taxonomy_source-')):
          case (0 === strpos($key,'cf7_2_post_map_taxonomy_names-')):
          case (0 === strpos($key,'cf7_2_post_map_taxonomy_name-')):
            delete_post_meta($this->cf7_post_ID, $key);
            //debug_msg('deleting: '.$key);
            break;
        }
      }
      //debug_msg($slugs, "saved taxonomy ");
      //save the taxonomy properties
      $this->post_properties['taxonomy'] = array_merge($this->post_properties['taxonomy'],$slugs );
      //make sure they are unique
      $this->post_properties['taxonomy'] = array_unique($this->post_properties['taxonomy']);
      update_post_meta($this->cf7_post_ID, '_cf7_2_post-taxonomy', $this->post_properties['taxonomy']);
    }
    //map the cf7 fields to the taxonomies
    foreach($post_map_taxonomy as $cf7_field=>$taxonomy){
      //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
      update_post_meta($this->cf7_post_ID, 'cf7_2_post_map_taxonomy-'.$taxonomy,$cf7_field);
    }
    $this->post_map_taxonomy = array_merge($this->post_map_taxonomy, $post_map_taxonomy);

    return true;
  }
  /**
	 * Load custom post properties from the CF7 post.
	 *
	 * @since    1.0.0
   */
  private function load_post_mapping(){
    $this->post_map_fields = array();
    $this->post_map_meta_fields = array();
    $this->post_map_taxonomy = array();
    $this->post_properties = array();
    //get_post_meta ( int $post_id, string $key = '', bool $single = false )
    $fields = get_post_meta ($this->cf7_post_ID);
    //debug_msg("found post meta,");
    //($fields);
    $start = strlen('_cf7_2_post-');
    $start2 = strlen('cf7_2_post_map-');
    $start3 = strlen('cf7_2_post_map_meta-');
    $start4 = strlen('cf7_2_post_map_taxonomy-');
    foreach ( $fields as $key=>$value ) {
      //debug_msg($key.'=>'.$value[0]);
      switch (true){
        case '_cf7_2_post-taxonomy' == $key;
        case '_cf7_2_post-supports' == $key:
        case '_cf7_2_post-capabilities' == $key: //use array value.
          //debug_msg(unserialize($value[0]), $key);
          $this->post_properties[substr($key,$start)]=unserialize($value[0]);
          break;
        case (0 === strpos($key,'_cf7_2_post-')): //for the others we want to get single values only
          $this->post_properties[substr($key,$start)]=$value[0];
          break;
        case (0 === strpos($key,'cf7_2_post_map-')): //this is post mapping field
          $this->post_map_fields[$value[0]]= substr($key,$start2);
          break;
        case (0 === strpos($key,'cf7_2_post_map_meta-')): //this is post meta mapping field
          $this->post_map_meta_fields[$value[0]]= substr($key,$start3);
          break;
      }
      //debug_msg($this->post_properties['taxonomy'], "taxonomy ");
    }
    //get taxonomies
    foreach($this->post_properties['taxonomy'] as $slug){
      $taxonomy_array = array('slug'=> $slug );
      $taxonomy_array['name'] = get_post_meta ($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_names-'.$slug, true);
      $taxonomy_array['singular_name'] = get_post_meta ($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_name-'.$slug, true);
      /**
      * Load the source of the taxonomy, 'factor' if created by this plugin, 'system' if existing
      *@since 1.1.0
      */
      $source = get_post_meta ($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_source-'.$slug, true);
      if(!$source){ //for pre-1.1 version we need to ensure we set some defaults
        $source = 'factory';
      }
      $taxonomy_array['source'] = $source;

      $this->taxonomy_properties[ $slug ] = $taxonomy_array;
      $cf7_field = get_post_meta ($this->cf7_post_ID, 'cf7_2_post_map_taxonomy-'.$slug, true);
      $this->post_map_taxonomy[$cf7_field] = $taxonomy_array['slug'];
    }
      //debug_msg($this->post_map_taxonomy,"mapped taxonomies... ");
    //set Title
    $this->post_properties['cf7_title'] = get_the_title($this->cf7_post_ID);
    //for old version plugin mapped post
    if(!isset($this->post_properties['version'])){
      $this->post_properties['version'] = '1.2.0';
    }
  }
  /**
	 * Return htlm <option></option> for field mapping .
	 *
	 * @since    1.0.0
   * @param   String   $field_to_map   optional post meta field if already mapped.
   * @param   boolean   $is_meta whether is custom meta field, default is false
   * @return   String htlm <option></option> for field mapping
   */
  public function get_select_options( $field_to_map=null, $is_meta = false){
    return $this->_select_options( $field_to_map, ($is_meta ? 'meta-field' : 'field') );
  }
  /**
   *Get a list of available system post_types as <option> elements
   *
   * @since 1.3.0
   * @return     String    html list of <option> elements with existing post_types in the DB
  **/
  public function get_system_posts_options(){
    $remove_post_types = array('revision','attachment','nav_menu_item','wpcf7_contact_form');
    $remove_post_types = apply_filters('cf7_2_post_filter_system_posts', $remove_post_types, $this->cf7_post_ID);
    $not_in = "'".implode("','", $remove_post_types)."'";
    global $wpdb;
    $posts = $wpdb->get_results(
      "SELECT DISTINCT post_type
      FROM {$wpdb->posts}
      WHERE post_type NOT IN ({$not_in})"
    );
    $html = '';
    $display = array();
    foreach($posts as $row){
      $display[] = $row->post_type;
    }
    $display = apply_filters('cf7_2_post_display_system_posts', $display, $this->cf7_post_ID);
    $selected = 'post';
    if('system' == $this->get('type_source')){
      $selected = $this->get('type');
    }
    foreach($display as $post_type){
      $select = ($selected == $post_type) ? ' selected="true"':'';
      $html .='<option value="' . $post_type . '"' . $select . '>' . $post_type . '</option>' . PHP_EOL;
    }
    return $html;
  }
  /**
   * Get a list of meta fields for the requested post_type
   *
   * @since 1.3.0
   * @param      String    $post_type     post_type for which meta fields are requested.
   * @return     String    a list of option elements for each existing meta field in the DB.
  **/
  public function get_system_post_metas($post_type){
    global $wpdb;
    $metas = $wpdb->get_results($wpdb->prepare(
      "SELECT DISTINCT meta_key
      FROM {$wpdb->postmeta} as wpm, {$wpdb->posts} as wp
      WHERE wpm.post_id = wp.ID AND wp.post_type = %s",
      $post_type
    ));
    $html = '';
    foreach($metas as $row){
      $selected='';
      $html+='<option value="' . $row->meta_key . '"' . $selected . '>' . $row->meta_key . '</option>' . PHP_EOL;
    }
    return $html;
  }
  /**
	 * Return htlm <option></option> for taxonomy mapping .
	 *
	 * @since    2.0.0
   * @param String  $field_to_map optional post meta field if already mapped.
   * @return String htlm <option></option> for field mapping
   */
  public function get_taxonomy_select_options( $field_to_map=null){
    return $this->_select_options( $field_to_map, 'taxonomy' );
  }
  /**
	 * Return htlm <option></option> for field mapping .
	 *
	 * @since    2.0.0
   * @param String $post_map_field optional post meta field if already mapped.
   * @param String  $type   the type of mapping, 'field' | 'meta-field' | 'taxonomy'
   * @return String htlm <option></option> for field mapping
   */
  protected function _select_options( $field_to_map=null, $data_type){
    if( !class_exists('WPCF7_ContactForm') ){
      return '<option>No CF7 Form Class found</option>';
    }
    //load teh form fields from the cf7 post
    $this->load_form_fields();
    //find the corresponding mapped field, stored in the cf7 post
    $cf7_maped_field_name = null;
    $taxonomy_types = array('select','checkbox','radio');

    if(!empty($field_to_map)){
      //get_post_meta ( int $post_id, string $key = '', bool $single = false )
      switch($data_type){
        case 'meta-field':
          $prefix = 'cf7_2_post_map_meta-' ;
          break;
        case 'taxonomy':
          $prefix =  'cf7_2_post_map_taxonomy-';
          break;
        case 'field':
        default:
          $prefix =  'cf7_2_post_map-';
          break;
      }

      $cf7_maped_field_name = get_post_meta($this->cf7_post_ID, $prefix.$field_to_map,true);
      //debug_msg("found meta, ".$prefix.$field_to_map."=".$cf7_maped_field_name,"loading... ");
    }
    $options = '  <option value="">Select a form field to map</option>';
    foreach ($this->cf7_form_fields as $field => $type) {
      //skip submit buttons
      if( 'submit' == $type) continue;
      //if the field to map is the thumbnail, use file type as options
      if( 'thumbnail' == $field_to_map && 'file' != $type ) continue;

      if( 'taxonomy' == $data_type && !in_array( $type, $taxonomy_types) )  continue;
        //debug_msg("Found field =".$field.', type='.$type);


      if(!empty($cf7_maped_field_name) && $field==$cf7_maped_field_name){
        $options .= '  <option selected="selected" value="'.$field.'">'.$field.'  ['.$type.']</option>';
      }else{
          $options .= '  <option value="'.$field.'">'.$field.'  ['.$type.']</option>';
      }
    }
    //filter option
    //debug_msg($field_to_map."=>".$cf7_maped_field_name.", ".(strpos($cf7_maped_field_name,'cf7_2_post_filter-')) );
    if( !empty($cf7_maped_field_name) && 0 === strpos($cf7_maped_field_name,'cf7_2_post_filter-') ){
      $options .= '  <option class="filter-option" selected="selected" value="'.$cf7_maped_field_name.'">Hook with a filter</option>';
    }else if(!empty($field_to_map)){
      $options .= '  <option class="filter-option" value="cf7_2_post_filter-'.$this->post_properties['type'].'-'.$field_to_map.'">Hook with a filter</option>';
    }else{
      $options .= '  <option class="filter-option" value="cf7_2_post_filter">Hook with a filter</option>';
    }

    return $options;
  }

  /**
  * Get the mapping of cf7 form field to taxonomy
  * @since 2.0.0
  * @return array array of {'form field'=>'taxonomy name'} mappings.
  */
  public function get_mapped_taxonomy(){
    return $this->post_map_taxonomy;
  }
  /**
  * Get the mapping of cf7 form field to post fields
  * @since 1.0.0
  * @return array array of {'form field'=>'post field'} mappings.
  */
  public function get_mapped_meta_fields(){
    return $this->post_map_meta_fields;
  }
  /**
	 * Check if a post attribute is supported.
	 *
	 * @since    1.0.0
   * @param String $post_attribute mapped post attribute to check
   * @return boolean true if it is supported
   */
  public function supports($post_attribute){
    if('draft'==$this->post_properties['map']) return true;
    return in_array( $post_attribute, $this->post_properties['supports'] );
  }
  /**
	 * Set an existing taxonomy for this post.
	 *
	 * @since    1.0.0
   * @param String $taxonomy registered taxonomy to set, if taxonomy does not exist, it will not set.
   */
  public function set_taxonomy($taxonomy){
    if( !in_array( $taxonomy, $this->post_properties['taxonomy'] ) && taxonomy_exists($taxonomy) ){
      $this->post_properties['taxonomy'][]=$taxonomy;
    }
  }
  /**
	 * Set an existing taxonomy for this post.
	 *
	 * @since    1.0.0
   * @param String $taxonomy registered taxonomy to set, if taxonomy does not exist, it will not set.
   * @return Array   an array with $taxonomy_slug=>array('source'=>$taxonomy_source, 'singular_name'=>$value, 'name'=>$plural_name) value.
   */
  public function get_taxonomy($taxonomy){
    if( isset( $this->taxonomy_properties[$taxonomy] ) ){
      return $this->taxonomy_properties[$taxonomy];
    }else{
      return array();
    }
  }
  /**
  * Load the cf7 forms fields
  * fields are loaded in the internal array.
  * @since 1.0.0
  */
  protected function load_form_fields(){
    //get all the fields of the form
    if(empty($this->cf7_form_fields)){
      $form = WPCF7_ContactForm::get_instance($this->cf7_post_ID);
      $form_elements = $form->scan_form_tags();
      //debug_msg($form_elements, " cf7 form elements ");
      foreach ($form_elements as $element) {
          $type = $element['type'];
          $type = str_replace('*', '', $type);
          $this->cf7_form_fields[$element['name']]=$type;
          $this->cf7_form_fields_options[$element['name']]=$element['options'];
      }
    }
  }
  /**
  * Check if a cf7 field name has an option
  * @since 2.0.0
  * @param String $field_name name of cf7 field
  * @param String $option option to check e.g. 'multiple'
  * @return boolean true if the option is set, false otherwise
  */
  protected function field_has_option($field_name, $option){
    return in_array($option, $this->cf7_form_fields_options[$field_name] );
  }
  /**
	 * Set the post singular name.
	 *
	 * @since    1.0.0
   * @param String $name post type singular name
   */
  public function has_file_field(){
    $this->load_form_fields();
    //debug_msg($this->cf7_form_fields,', has file');
    return in_array('file',$this->cf7_form_fields);
  }
  /**
	 * Set the post type capability.
	 *
	 * @since    1.0.0
   * @param String $capability post type capability such as 'hierarchical'
   * @param boolean $flag true or false
   */
  public function set_post_capability($capability, $flag=false){
    $this->post_properties[$capability]=$flag;
  }
  /**
	 * Set the post support attributes.
	 *
	 * @since    1.0.0
   * @param Array $supports post type supports attribues such as array('title','editor','')
   */
  public function set_supports($supports){
    $this->post_properties['supports']=$supports;
  }
  /**
	 * Get mapped custom post property.
	 *
	 * @since    1.0.0
   * @param String $property post property attribute
   * @return Srting value of the property, else null if not set
   */
  public function get($property='type'){
    if( isset( $this->post_properties[$property] ) ){
      return $this->post_properties[$property];
    }else{
      return '';
    }
  }
  /**
	 * Get mapped custom post property.
	 *
	 * @since    1.0.0
   * @param String $property post property attribute
   * @param String $echo_string_or_value_if_null optional string to echo is the property is set
   * @return String if the property is set/true echo of the 2nd parameter if passed
   * else the property value is the 2nd parameter is ommited.
   */
  public function is($property, $echo_string_or_value_if_null=null){
    switch (true){
      case ( isset( $this->post_properties[$property] )
              && false != $this->post_properties[$property]
              && null != $echo_string_or_value_if_null ):
        echo $echo_string_or_value_if_null;
        break;
      case  ( isset( $this->post_properties[$property] )
              && false == $this->post_properties[$property]
              && null != $echo_string_or_value_if_null ):
        echo '';
        break;
      case ( isset( $this->post_properties[$property] )
              && null == $echo_string_or_value_if_null ):
        echo $this->post_properties[$property];
        break;
      case !isset( $this->post_properties[$property] ):
        echo 'FACTORY_ERROR:PROPERTY-'.$property.'-NOT_SET';
        break;
      default:
        echo '';
        break;
    }
    $this->is_published();
  }
  /**
  * Disables field if the post is published
  * @since 1.0.0
  */
  public function is_published($element='input', $echo=true){
    if('publish' == $this->post_properties['map']){
      switch($element){
        case 'input':
          $value = ' disabled="disabled" ';
          break;
        case 'select':
          $value = ' disabled ';
          break;
        case 'boolean':
          $value = true;
          break;
      }
    }else{
      switch($element){
        case 'boolean':
          $value = false;
          break;
        default:
          $value = '';
          break;
      }
    }
    if($echo){
      echo $value;
    }else{
      return $value;
    }
  }
  /**
  * Check if this is published system post
  */
  public function is_system_published(){
    return('publish' == $this->get('map') && 'system' == $this->get('type_source') );
  }
  /**
  * Checks if a form mapping is published
  * @since 2.0.0
  */
  public static function is_mapped($cf7_post_ID){
    $map = get_post_meta($cf7_post_ID, '_cf7_2_post-map', true);

    if($map && 'publish'== $map){
      return true;
    }else{
      return false;
    }
  }
  /**
  * Register Custom Post Type based on CF7 mapped properties
  *
  * @since 1.0.0
  */
  protected function create_cf7_post_type() {
    //register any custom taxonomy
    if( !empty($this->post_properties['taxonomy']) ){
      foreach($this->post_properties['taxonomy'] as $taxonomy_slug){
        if('system' == $this->taxonomy_properties[$taxonomy_slug]['source']){
          continue;
        }
        $taxonomy = array(
      		'hierarchical'               => true,
      		'public'                     => true,
      		'show_ui'                    => true,
      		'show_admin_column'          => true,
      		'show_in_nav_menus'          => true,
      		'show_tagcloud'              => true,
          'show_in_quick_edit'         => true,
          'menu_name'                  => $this->taxonomy_properties[$taxonomy_slug]['name'],
          'description'                =>'',
      	);
        //debug_msg($this->taxonomy_properties[$taxonomy_slug]," taxonomy properties: ".$taxonomy_slug);
        $taxonomy =  array_merge( $this->taxonomy_properties[$taxonomy_slug], $taxonomy );
        $taxonomy_filtered = apply_filters('cf7_2_post_filter_taxonomy_registration-'.$taxonomy_slug, $taxonomy);
        //ensure we have all the key defined.
        $taxonomy =  $taxonomy_filtered + $taxonomy; //this will give precedence to filtered keys, but ensure we have all required keys.
        $this->register_custom_taxonomy($taxonomy);
      }
    }
  	$labels = array(
  		'name'                  => $this->post_properties['plural_name'],
  		'singular_name'         => $this->post_properties['singular_name'],
  		'menu_name'             => $this->post_properties['plural_name'],
  		'name_admin_bar'        => $this->post_properties['singular_name'],
  		'archives'              => $this->post_properties['singular_name'].' Archives',
  		'parent_item_colon'     => 'Parent '.$this->post_properties['singular_name'].':',
  		'all_items'             => 'All '.$this->post_properties['plural_name'],
  		'add_new_item'          => 'Add New '.$this->post_properties['singular_name'],
  		'add_new'               => 'Add New',
  		'new_item'              => 'New '.$this->post_properties['singular_name'],
  		'edit_item'             => 'Edit '.$this->post_properties['singular_name'],
  		'update_item'           => 'Update '.$this->post_properties['singular_name'],
  		'view_item'             => 'View '.$this->post_properties['singular_name'],
  		'search_items'          => 'Search '.$this->post_properties['singular_name'],
  		'not_found'             => 'Not found',
  		'not_found_in_trash'    => 'Not found in Trash',
  		'featured_image'        => 'Featured Image',
  		'set_featured_image'    => 'Set featured image',
  		'remove_featured_image' => 'Remove featured image',
  		'use_featured_image'    => 'Use as featured image',
  		'insert_into_item'      => 'Insert into '.$this->post_properties['singular_name'],
  		'uploaded_to_this_item' => 'Uploaded to this '.$this->post_properties['singular_name'],
  		'items_list'            => $this->post_properties['plural_name'].' list',
  		'items_list_navigation' => $this->post_properties['plural_name'].' list navigation',
  		'filter_items_list'     => 'Filter '.$this->post_properties['plural_name'].' list',
  	);
    //labels can be modified post taxonomy registratipn
  	$args = array(
  		'label'                 => $this->post_properties['singular_name'],
  		'description'           => 'Post for CF7 Form'. $this->post_properties['cf7_title'],
  		'labels'                => $labels,
      'supports'              => apply_filters('cf7_2_post_supports_'.$this->post_properties['type'], $this->post_properties['supports']),
  		'taxonomies'            => $this->post_properties['taxonomy'],
  		'hierarchical'          => !empty($this->post_properties['hierarchical']),
  		'public'                => !empty($this->post_properties['public']),
  		'show_ui'               => !empty($this->post_properties['show_ui']),
  		'show_in_menu'          => !empty($this->post_properties['show_in_menu']),
  		'menu_position'         => $this->post_properties['menu_position'],
  		'show_in_admin_bar'     => !empty($this->post_properties['show_in_admin_bar']),
  		'show_in_nav_menus'     => !empty($this->post_properties['show_in_nav_menus']),
  		'can_export'            => !empty($this->post_properties['can_export']),
  		'has_archive'           => !empty($this->post_properties['has_archive']),
  		'exclude_from_search'   => !empty($this->post_properties['exclude_from_search']),
  		'publicly_queryable'    => !empty($this->post_properties['publicly_queryable']),
  	);
    $reference = array(
        'edit_post' => '',
        'edit_posts' => '',
        'edit_others_posts' => '',
        'publish_posts' => '',
        'read_post' => '',
        'read_private_posts' => '',
        'delete_post' => ''
    );
    $capabilities = array_filter(apply_filters('cf7_2_post_capabilities_'.$this->post_properties['type'], $reference));
    $diff=array_diff_key($reference, $capabilities);
    if( empty( $diff ) ) {
      $args['capabilities'] = $capabilities;
      $args['map_meta_cap'] = true;
    }else{ //some keys are not set, so capabilities will not work
      //set to defaul post capabilities
      $args['capability_type'] = 'post';
    }

    //allow additional settings
    $args = apply_filters('cf7_2_post_register_post_'.$this->post_properties['type'], $args );

  	register_post_type( $this->post_properties['type'], $args );

    //link the taxonomy and the post
    foreach($this->post_properties['taxonomy'] as $taxonomy_slug){
      register_taxonomy_for_object_type( $taxonomy_slug, $this->post_properties['type'] );
    }
  }
  /**
  * Dynamically registers new custom post
  * Hooks 'init'action in the admin section
  * @since 1.0.0
  */
  public static function register_cf7_post_maps(){
    global $wpdb;
    $cf7_post_ids = $wpdb->get_col(
      "SELECT post_id FROM $wpdb->postmeta, $wpdb->posts
      WHERE meta_key='_cf7_2_post-map'
      AND meta_value='publish'
      AND ID=post_id
      AND post_status LIKE 'publish'"
    );
    foreach($cf7_post_ids as $post_id){
      $cf7_2_post_map = self::get_factory($post_id);
      if('factory'==$cf7_2_post_map->get('type_source')){
        $cf7_2_post_map->create_cf7_post_type();
      }
    }
  }
  /**
  * Save the submitted form data to a new/existing post
  * calling this funciton assumes the mapped post_type exists and is published
  *@since 1.0.0
  *@param Array $cf7_form_data data submitted from cf7 form
  */
  public function save_form_2_post($submission){
    $cf7_form_data = $submission->get_posted_data();
    //check if this is a system post
    if('system' == $this->get('type_source')){
      do_action( 'cf7_2_post_save-'.$this->get('type'), $this->cf7_key, $cf7_form_data, $submission->uploaded_files());
      return;
    }
    //create a new post
    //get the form email recipient
    $author = 1;
    if(is_user_logged_in()){
      $user = wp_get_current_user();
      $author = $user->ID;
    }else{
      //get_post_meta ( int $post_id, string $key = '', bool $single = false )
      $mail = get_post_meta ($this->cf7_post_ID,'_mail',true);
      if( !empty($mail) &&  isset($mail['recipient']) ){
        $user_email = $mail['recipient'];
        //get_user_by ( string $field, int|string $value )
        $user = get_user_by ( 'email', $user_email );
        if($user) $author = $user->ID;
      }
    }

    $post = array('post_type'  =>$this->post_properties['type'],
                  'post_author'=>$author,
                  'post_status'=>'draft',
                  'post_title'  => 'CF7 2 Post'
                );
    $post_id = '';
    if(isset($cf7_form_data['_map_post_id']) && !empty($cf7_form_data['_map_post_id'])){
      $post_id = $cf7_form_data['_map_post_id']; //this is an existing post being updated
      $wp_post = get_post($post_id);
      $post['post_status'] = $wp_post->post_status;
    }else{
      //this is a new mapping.
      $post['post_author'] = apply_filters('cf7_2_post_author_'.$this->post_properties['type'], $author, $this->cf7_post_ID, $cf7_form_data );
      //wp_insert_post ( array $postarr, bool $wp_error = false )
      $post_id = wp_insert_post ( $post );
    }
    $post['ID'] = $post_id;
    //debug_msg("Creating a new post... ".$post_id);
    foreach($this->post_map_fields as $form_field => $post_field){
      $post_key ='';
      $skip_loop = false;
      switch($post_field){
        case 'title':
        case 'author':
        case 'excerpt':
          $post_key = 'post_'.$post_field;
          break;
        case 'editor':
          $post_key ='post_content';
          break;
        case 'slug':
          $post_key ='post_name';
          break;
        case 'thumbnail':
          //
          //debug_msg($form_field, 'uploaded file...');
          $cf7_files = $submission->uploaded_files();
          $file = $cf7_files[$form_field];
          $filename = $cf7_form_data[$form_field]; //path

          //wp_upload_bits( $name, $deprecated, $bits, $time )
          $upload_file = wp_upload_bits($filename, null, @file_get_contents($file));
          if (!$upload_file['error']) {
          	$wp_filetype = wp_check_filetype($filename, null );
          	$attachment = array(
          		'post_mime_type' => $wp_filetype['type'],
          		'post_parent' => $post_id,
          		'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
          		'post_content' => '',
          		'post_status' => 'inherit'
          	);
            //wp_insert_attachment( $attachment, $filename, $parent_post_id );
          	$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'], $post_id );
          	if (!is_wp_error($attachment_id)) {
          		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
              //wp_generate_attachment_metadata( $attachment_id, $file ); for images
          		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
          		wp_update_attachment_metadata( $attachment_id,  $attachment_data );
              set_post_thumbnail( $post_id, $attachment_id );
              //debug_msg($attachment,'attached file '.$attachment_id);
              //debug_msg($attachment_data,'attached file data '.$attachment_id);
          	}else{
              debug_msg($attachment_id, 'error while attaching to post '.$post_id.'... ');
            }
          }else{
            debug_msg($upload_file, 'error while uploading the file, '.$filename.' to the Media Gallery... ');
          }
          //at this point skip the rest of the loop as the file is saved
          $skip_loop = true;
          //we need a special treatment
          break;
      }

      if($skip_loop){
        continue;
      }

      if( empty($post_key) ){
        debug_msg("Unable to map form field=".$form_field." to post field= ".$post_field);
        continue;
       }

      if( 0 === strpos($form_field,'cf7_2_post_filter-') ){
        $post[$post_key] = apply_filters($form_field,'', $post_id, $cf7_form_data);
      }else{
        if( isset($cf7_form_data[$form_field]) ){
          if(is_array($cf7_form_data[$form_field])){
            $post[$post_key] = implode(',', $cf7_form_data[$form_field] );
          }else{
            $post[$post_key] = $cf7_form_data[$form_field];
          }
        }
      }
    }
    //update the post
    if( empty($post['post_name']) ){
      if( isset($post['post_title']) ){
        //sanitize_title( $title, $fallback_title, $context )
        $post['post_name'] = sanitize_title( $post['post_title'] );
      }else{
        $post['post_name'] = 'cf7_'.$this->cf7_post_ID.'_to_post_'.$post_id;
      }
    }
    //debug_msg($post, 'updating post... ');

    $post_id = wp_insert_post ( $post );
    //
    //-------------- meta fields
    //
    $publish_post = false;
    if(isset($cf7_form_data['save_cf7_2_post']) && 'true'==$cf7_form_data['save_cf7_2_post']){
      update_post_meta($post_id, '_cf7_2_post_form_submitted','no'); //form is saved
    }else{
      update_post_meta($post_id, '_cf7_2_post_form_submitted','yes'); //form is submitted
    }
    $this->load_form_fields(); //this loads the form fields and their type

    //debug_msg($cf7_form_data, "submitted data ");

    foreach($this->post_map_meta_fields as $form_field => $post_field){
      if( 0 === strpos($form_field,'cf7_2_post_filter-') ){
        $value = apply_filters($form_field,'', $post_id, $cf7_form_data);
        //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
        update_post_meta($post_id, $post_field, $value);
      }else{
        if( 'file' == $this->cf7_form_fields[$form_field] ){
          $cf7_files = $submission->uploaded_files();
          $file = $cf7_files[$form_field];
          $filename = $cf7_form_data[$form_field]; //path
          //wp_upload_bits( $name, $deprecated, $bits, $time )
          //debug_msg(, "uploading file to meta field... ");
          $upload_file = wp_upload_bits($filename, null, @file_get_contents($file));

          if (!$upload_file['error']) {
            update_post_meta($post_id, $post_field, $upload_file['file']);
          }else{
            //debug_msg($upload_file['error']);
            debug_msg($file, "Unable to upload file ".$filename);
          }
        }else{
          if( isset($cf7_form_data[$form_field]) ){
            update_post_meta($post_id, $post_field, $cf7_form_data[$form_field]);
          }
        }
      }
    }
    //
    //--------------- taxonomies
    //
    foreach($this->post_map_taxonomy as $form_field => $taxonomy){
      $value = '';
      if( 0 === strpos($form_field,'cf7_2_post_filter-') ) {
        $value = apply_filters($form_field, array(), $post_id, $cf7_form_data);
      }else if(isset( $cf7_form_data[$form_field] )){
        if( is_array( $cf7_form_data[$form_field] ) ){
          $value = array_map( 'intval', $cf7_form_data[$form_field] );
        }else{
          //debug_msg($cf7_form_data[$form_field], $taxonomy." values ");
          $value = array_map( 'intval',  array( $cf7_form_data[$form_field] ) );
        }
      }
      if( !empty($value) ){
        $term_taxonomy_ids = wp_set_object_terms( $post_id , $value, $taxonomy );
        if ( is_wp_error( $term_taxonomy_ids ) ) {
        	debug_msg($term_taxonomy_ids, " Unable to set taxonomy (".$taxonomy.") terms");
          debug_msg($value, "Attempted to set these term values ");
        }
      }
    }
    do_action('cf7_2_post_form_mapped_to_'.$this->post_properties['type'],$post_id, $cf7_form_data);
  }
  /**
  * Builds a set of field=>value pairs to pre-populate a mapped form
  * Called by Cf7_2_Post_Public::load_cf7_script()
  * @since 1.3.0
  * @param   Int  $cf7_2_post_id   a specific post to which this form submission is mapped/saved
  * @return    Array  cf7 form field=>value pairs.
  */
  public function get_form_values($cf7_2_post_id=''){
    //is user logged in?
    $load_saved_values = false;
    $post=null;

    $field_and_values = array();
    $unmapped_fields = array();
    $this->load_form_fields(); //this loads the cf7 form fields and their type

    //currently mapped fields will be
    if('system'==$this->get('type_source')){
      $post_values = array();

      $post_values = apply_filters('cf7_2_post_load-' . $this->get('type'), $post_values, $this->cf7_key, $this->cf7_form_fields, $this->cf7_form_fields_options, $this->cf7_post_ID, $this);
      $post_values = apply_filters('cf7_2_post_pre_load-' . $this->get('type'), $post_values, $this->cf7_key, $this);

      foreach($post_values as $field=>$value){
        $field_and_values[str_replace('-','_',$field)] = $value;
        //check if the select2 plugin is required
        if( 'select' == $this->cf7_form_fields[$field] ){
          $apply_jquery_select = apply_filters('cf7_2_post_filter_cf7_taxonomy_chosen_select',true, $this->cf7_post_ID, $field) && apply_filters('cf7_2_post_filter_cf7_taxonomy_select2',true, $this->cf7_post_ID, $field);

          if( $apply_jquery_select ){
            wp_enqueue_script('jquery-select2',plugin_dir_url( dirname( __FILE__ ) ) . 'assets/select2/js/select2.min.js', array('jquery'),CF7_2_POST_VERSION,true);
            wp_enqueue_style('jquery-select2',plugin_dir_url( dirname( __FILE__ ) ) . 'assets/select2/css/select2.min.css', array(),CF7_2_POST_VERSION);
          }
        }
      }

      //in future version, if the $post_values is empty we will simply conitnue
      return $field_and_values;
    }

    if(is_user_logged_in()){ //let's see if this form is already mapped for this user
      $user = wp_get_current_user();
      //find out if this user has a post already created/saved
      $args = array(
      	'posts_per_page'   => 1,
      	'post_type'        => $this->post_properties['type'],
      	'author'	   => $user->ID,
      	'post_status'      => 'any'
      );
      if(!empty($cf7_2_post_id)){ //search for the sepcific mapped/saved post
        $args['post__in']=array($cf7_2_post_id);
      }
      //filter by submission value for newer version so as not to break older version
      if( version_compare( CF7_2_POST_VERSION , $this->post_properties['version'] , '>=') ){
        $args['meta_query'] = array(
    		array(
    			'key'     => '_cf7_2_post_form_submitted',
    			'value'   => 'no',
    			'compare' => 'LIKE',
    		));
      }


      $args = apply_filters('cf7_2_post_filter_user_draft_form_query', $args, $this->post_properties['type']);
      $posts_array = get_posts( $args );
      //debug_msg($args, "looking for posts.... found, ".sizeof($posts_array));
      if($posts_array){
        $post = $posts_array[0];
        $load_saved_values = true;
        $field_and_values['map_post_id']= $post->ID;
      }
    }
      //we now need to load the save meta field values


      foreach($this->post_map_fields as $form_field => $post_field){
        $post_key ='';
        $post_value = '';
        $skip_loop = false;
        //if the value was filtered, let's skip it
        if( 0 === strpos($form_field,'cf7_2_post_filter-') ){
          continue;
        }

        switch($post_field){
          case 'title':
          case 'author':
          case 'excerpt':
            $post_key = 'post_'.$post_field;
            break;
          case 'editor':
            $post_key ='post_content';
            break;
          case 'slug':
            $post_key ='post_name';
            break;
          case 'thumbnail':
            break;
        }
        if($load_saved_values) {
          $post_value = $post->{$post_key};
        }else{
          $post_value = apply_filters('cf7_2_post_filter_cf7_field_value', $post_value, $this->cf7_post_ID, $form_field);
        }

        if(!empty($post_value)){
          $field_and_values[str_replace('-','_',$form_field)] = $post_value;
        }
      }
      //
      //----------- meta fields
      //
      //debug_msg($this->post_map_meta_fields, "loading meta fields mappings...");
      foreach($this->post_map_meta_fields as $form_field => $post_field){
        $post_value='';
        //if the value was filtered, let's skip it
        if( 0 === strpos($form_field,'cf7_2_post_filter-') ) {
          continue;
        }
        //get the meta value
        if($load_saved_values) {
          $post_value = get_post_meta($post->ID, $post_field);
        }else{
          //debug_msg('spllygin filter cf7_2_post_filter_cf7_field_value'. $form_field);
          $post_value = apply_filters('cf7_2_post_filter_cf7_field_value', $post_value, $this->cf7_post_ID, $form_field);
        }
        if(!empty($post_value)){
          $field_and_values[str_replace('-','_',$form_field)] = $post_value;
        }
      }
      /*
       Finally let's also allow a user to load values for unammaped fields
      */
      $unmapped_fields = array_diff_key( $this->cf7_form_fields, $this->post_map_meta_fields, $this->post_map_fields, $this->post_map_taxonomy );
      foreach($unmapped_fields as $form_field=>$type){
        if('submit' == $type){
          continue;
        }
        $post_value='';
        $post_value = apply_filters('cf7_2_post_filter_cf7_field_value', $post_value, $this->cf7_post_ID, $form_field);
        //$script .= $this->get_field_script($form_field, $post_value);
        if(!empty($post_value)){
          $field_and_values[str_replace('-','_',$form_field)] = $post_value;
        }
      }
      //
      // ------------ taxonomy fields
      //
      $load_chosen_script=false;
      foreach($this->post_map_taxonomy as $form_field => $taxonomy){
        //if the value was filtered, let's skip it
        if( 0 === strpos($form_field,'cf7_2_post_filter-') ) continue;
        $terms_id = array();
        if( $load_saved_values ) {
          $terms = get_the_terms($post, $taxonomy);
          foreach($terms as $term){
            $terms_id[] = $term->term_id;
          }
        }else{
          $terms_id = apply_filters('cf7_2_post_filter_cf7_taxonomy_terms',$terms_id, $this->cf7_post_ID, $form_field);
          if( is_string($terms_id) ){
            $terms_id = array($terms_id);
          }
        }
        //load the list of terms
        //debug_msg("buidling options for taxonomy ".$taxonomy);
        $field_type = $this->cf7_form_fields[$form_field];
        $options = $this->get_taxonomy_terms($taxonomy, 0, $terms_id, $form_field, $field_type);
        //for legacy purpose
        $apply_jquery_select = apply_filters('cf7_2_post_filter_cf7_taxonomy_chosen_select',true, $this->cf7_post_ID, $form_field) && apply_filters('cf7_2_post_filter_cf7_taxonomy_select2',true, $this->cf7_post_ID, $form_field);
        if( $apply_jquery_select ){
          wp_enqueue_script('jquery-select2',plugin_dir_url( dirname( __FILE__ ) ) . 'assets/select2/js/select2.min.js', array('jquery'),CF7_2_POST_VERSION,true);
          wp_enqueue_style('jquery-select2',plugin_dir_url( dirname( __FILE__ ) ) . 'assets/select2/css/select2.min.css', array(),CF7_2_POST_VERSION);
        }
        $field_and_values[str_replace('-','_',$form_field)] = wp_json_encode($options);

      }
    //filter the values
    $field_and_values = apply_filters('cf7_2_post_form_values', $field_and_values, $this->cf7_post_ID , $this->post_properties['type'] );
    //make sure the field names are with underscores
    $return_values = array();
    foreach($field_and_values as $field=>$value){
      $return_values[str_replace('-','_',$field)]=$value;
    }
    return $return_values;
  }

  /**
  * Function to print jquery script for form field initialisation
  *
  * @since 1.3.0
  * @param   Array  $field_and_values   array of $field_name=>$values pairs
  * @param   Int  $cf7_2_post_id   a specific post to which this form submission is mapped/saved
  */
  public function get_form_field_script($nonce, $cf7_2_post_id=''){
    ob_start();
    include( plugin_dir_path( __FILE__ ) . '/partials/cf7-2-post-script.php');
    $script = ob_get_contents ();
    ob_end_clean();
    //save to file
    /*$result = file_put_contents( plugin_dir_path( __DIR__ ) . 'public/js/cf7_2_post-'.$this->cf7_post_ID.'.js',  $script);
    if(false === $result){
      debug_msg(plugin_dir_path( __DIR__ ) . 'js/cf7_2_post-'.$this->cf7_post_ID.'.js', "Unable to save script file: ");
    }*/
    return $script;
  }
  /**
   * Function to return taxonomy terms as either options list for dropdown, checkbox, or radio
   * This is used for system post mapping in conjunstion with the filter 'cf7_2_post_load-{$post_type}'
   * @since 1.3.2
   * @param   String    $taxonomy     The taxonomy slug from which to retrieve the terms.
   * @param   String    $parent     the parent branch for which to retrieve the terms (by default 0).
   * @param   Array     $post_term_ids an array of term ids which a post has been tagged with
   * @param   String    $field form field name for which this taxonomy is mapped to.
   * @param   String    $field_type the type of field in which the tersm are going to be listed
   * @return  String    json encoded HTML script to be used as value for the $field     .
  **/
  public function get_taxonomy_mapping($taxonomy, $parent=0, $post_term_ids, $field){
    $this->load_form_fields();
    $script = $this->get_taxonomy_terms( $taxonomy, $parent, $post_term_ids, $field, $this->cf7_form_fields[$field] );
    return json_encode($script);
  }
  /**
  * Function to retrieve jquery script for form field taxonomy capture
  *
  * @since 1.2.0
  * @param   String $taxonomy  the taxonomy slug for which to return the list of terms
  * @param   Int  $parent  the parent ID of child terms to fetch
  * @param   Array  $post_terms an array of terms which a post has been tagged with
  * @param   String   $field form field name for which this taxonomy is mapped to.
  * @param   String $field_type the type of field in which the tersm are going to be listed
  * @return  String a jquery code to be executed once the page is loaded.
  */
   protected function get_taxonomy_terms( $taxonomy, $parent, $post_terms, $field, $field_type){
    $args = array(
      'parent' => $parent,
      'hide_empty' => 0,
    );
    $args = apply_filters('cf7_2_post_filter_taxonomy_query', $args, $this->cf7_post_ID, $taxonomy);
    //check the WP version
    global $wp_version;
	  if ( $wp_version >= 4.5 ) {
      $args['taxonomy'] = $taxonomy;
	    $terms = get_terms($args); //WP>= 4.5 the get_terms does not take a taxonomy slug field
    }else{
      $terms = get_terms($taxonomy, $args);
    }
    if( is_wp_error( $terms ) ){
      debug_msg('Taxonomy '.$taxonomy.' does not exist');
      return '';
    }else if( empty($terms) ){
      //debug_msg("No Terms found for taxonomy: ".$taxonomy.", parent ".$parent);
      return'';
    }
    //build the list
    $term_class = 'cf72post-'.$taxonomy;
    $nl = '';//PHP_EOL;
    $script = '<fieldset class="'.$term_class.'">';
    if($parent > 0){
      $script = '<fieldset class="cf72post-child-terms parent-term-'.$parent.'">';
      $term_class .= ' cf72post-child-term';
    }
    //if we are dealing with a dropdown, then don't group fieldsets
    if('select' == $field_type) $script = '';
    foreach($terms as $term){
      $term_id = $term->term_id;
      $is_optgroup=false;

      switch($field_type){
        case 'select':
          //debug_msg("Checking option: ".$this->cf7_post_ID." field(".$field."), term ".$term->name);
          //check if we group these terms
          if(0==$parent){
            //do we gorup top level temrs as <optgroup/> ?
            $groupOptions = false;
            $children = get_term_children($term_id, $taxonomy);
            if($children) $groupOptions = true;
            //let's filter this choice
            $groupOptions = apply_filters('cf7_2_post_filter_cf7_taxonomy_select_optgroup',$groupOptions, $this->cf7_post_ID, $field, $term);

             if($groupOptions){
              $script .='<optgroup label="'.$term->name.'">';
              $is_optgroup=true;
            }
          }
          if(!$is_optgroup){
            if( in_array($term_id, $post_terms) ){
              $script .='<option value="'.$term_id.'" selected="selected">'.$term->name.'</option>';
            }else{
              $script .='<option value="'.$term_id.'" >'.$term->name.'</option>';
            }
          }
          break;
        case 'radio':
          $check = '';
          if( in_array($term_id, $post_terms) ){
            $check = 'checked';
          }
          $script .='<input type="radio" name="'.$field.'" value="'.$term_id.'" class="'.$term_class.'" '.$check.'/>';
          $script .='<label>'.$term->name.'</label>'.$nl;
          break;
        case 'checkbox':
          $check = '';
          if( in_array($term_id, $post_terms) ){
            $check = 'checked';
          }
          $field_name = $field;
          if( !$this->field_has_option($field, 'exclusive') ){
            $field_name = $field.'[]';
          }
          $script .='<input type="checkbox" name="'.$field_name.'" value="'.$term_id.'" class="'.$term_class.'" '.$check.'/>';
          $script .='<label>'.$term->name.'</label>'.$nl;
          break;
        default:
          return ''; //nothing more to do here
          break;
      }
      //get children
      $script .= $this->get_taxonomy_terms($taxonomy, $term_id, $post_terms, $field, $field_type);
      if($is_optgroup) $script .='</optgroup>';
    }
    if('select' != $field_type) $script .='</fieldset>';

    return $script;
  }
  /**
  * regsiter a custom taxonomy
  * @since 2.0.0
  * @param  Array  $taxonomy  a, array of taxonomy arguments
  */
  protected function register_custom_taxonomy($taxonomy) {
  	$labels = array(
  		'name'                       =>  $taxonomy["name"],
  		'singular_name'              =>  $taxonomy["singular_name"],
  		'menu_name'                  =>  $taxonomy["menu_name"],
  		'all_items'                  =>  'All '.$taxonomy["name"],
  		'parent_item'                =>  'Parent '.$taxonomy["singular_name"],
  		'parent_item_colon'          =>  'Parent '.$taxonomy["singular_name"].':',
  		'new_item_name'              =>  'New '.$taxonomy["singular_name"].' Name',
  		'add_new_item'               =>  'Add New '.$taxonomy["singular_name"],
  		'edit_item'                  =>  'Edit '.$taxonomy["singular_name"],
  		'update_item'                =>  'Update '.$taxonomy["singular_name"],
  		'view_item'                  =>  'View '.$taxonomy["singular_name"],
  		'separate_items_with_commas' =>  'Separate '.$taxonomy["name"].' with commas',
  		'add_or_remove_items'        =>  'Add or remove '.$taxonomy["name"],
  		'choose_from_most_used'      =>  'Choose from the most used',
  		'popular_items'              =>  'Popular '.$taxonomy["name"],
  		'search_items'               =>  'Search '.$taxonomy["name"],
  		'not_found'                  =>  'Not Found',
  		'no_terms'                   =>  'No '.$taxonomy["name"],
  		'items_list'                 =>  $taxonomy["name"].' list',
  		'items_list_navigation'      =>  $taxonomy["name"].' list navigation',
  	);
    //labels can be modified post registration
  	$args = array(
  		'labels'                     => $labels,
  		'hierarchical'               => $taxonomy["hierarchical"],
  		'public'                     => $taxonomy["public"],
  		'show_ui'                    => $taxonomy["show_ui"],
  		'show_admin_column'          => $taxonomy["show_admin_column"],
  		'show_in_nav_menus'          => $taxonomy["show_in_nav_menus"],
  		'show_tagcloud'              => $taxonomy["show_tagcloud"],
      'show_in_quick_edit'         => $taxonomy["show_in_quick_edit"],
      'description'                => $taxonomy["description"],
  	);
    if(isset($taxonomy['meta_box_cb'])){
      $args['meta_box_cb'] = $taxonomy['meta_box_cb'];
    }
    if(isset($taxonomy['update_count_callback'])){
      $args['update_count_callback'] = $taxonomy['update_count_callback'];
    }
    if(isset($taxonomy['capabilities'])){
      $args['capabilities'] = $taxonomy['capabilities'];
    }
    $post_types = apply_filters('cf7_2_post_filter_taxonomy_register_post_type', array( $this->post_properties["type"] ), $taxonomy["slug"]);
  	register_taxonomy( $taxonomy["slug"], $post_types, $args );

  }
  /**
   * Delete a post mapping
   *
   * @since 1.0.0
   * @param      int    $cf7_post_id    form post id whose mapping is to be deleted .
   * @param     boolean    $delete_all_posted_data     .
  **/
  public function delete_mapping($delete_all_posted_data){
    //TODO delete all meta_fields for this post
    //delete_post_meta($post_id, $meta_key, $meta_value);
    foreach($this->post_properties as $key=>$value){
      delete_post_meta($this->cf7_post_ID, '_cf7_2_post-'.$key);
    }
    foreach($this->post_map_fields as $cf7_field=>$post_field){
      delete_post_meta($this->cf7_post_ID, 'cf7_2_post_map-'.$post_field);
    }
    foreach($this->post_map_meta_fields as $cf7_field=>$post_field){
      delete_post_meta($this->cf7_post_ID, 'cf7_2_post_map_meta-'.$post_field);
    }
    //taxonomy mapping
    delete_post_meta($this->cf7_post_ID, '_cf7_2_post-taxonomy');
    foreach($this->post_properties['taxonomy'] as $slug){
      //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
      delete_post_meta($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_names-'.$slug);
      delete_post_meta($this->cf7_post_ID, 'cf7_2_post_map_taxonomy_name-'.$slug);
      delete_post_meta($this->cf7_post_ID, 'cf7_2_post_map_taxonomy-'.$slug);
    }
    //TODO if( true==$delete_all_posted_data ) then we should remove all posted data
  }
}
