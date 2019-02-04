<?php
// *****************************************************
// Add Additional fields to BuddyPress Groups
// *****************************************************

function bp_group_meta_init() {
  function custom_field($meta_key=''){
    //get current group id and load meta_key value if passed.
    return groups_get_groupmeta(bp_get_group_id(),$meta_key);

  }

  //function to generate the field markup for front-end form:
  function group_header_fields_markup() {
    global $bp, $wpdb;?>
    <label for="kobotools_account">Kobotoolbox Account username</label>
    <input id="kobotools_account" type="text" name="kobotools_account" value="<?php echo custom_field('kobotools_account'); ?>" />
    <br>
    <?php
  }

  // This saves the custom group meta – props to Boone for the function
  // Where $plain_fields = array.. you may add additional fields, eg
  //  $plain_fields = array(
  //      'field-one',
  //      'field-two'
  //  );
  function group_header_fields_save( $group_id ) {
    global $bp, $wpdb;
    $plain_fields = array(
      'kobotools_account'
    );
    foreach( $plain_fields as $field ) {
      $key = $field;
      if ( isset( $_POST[$key] ) ) {
        $value = $_POST[$key];
        groups_update_groupmeta( $group_id, $field, $value );
      }
    }
  }
  add_filter( 'groups_custom_group_fields_editable', 'group_header_fields_markup' );
  add_action( 'groups_group_details_edited', 'group_header_fields_save' );
  add_action( 'groups_created_group',  'group_header_fields_save' );

  // Show the custom field in the group header
  function show_field_in_header( ) {
    echo "<p> Kobotoolbox account username:" . custom_field('kobotools_account') . "</p>";
  }
  add_action('bp_group_header_meta' , 'show_field_in_header') ;
}

add_action( 'bp_include', 'bp_group_meta_init' );
