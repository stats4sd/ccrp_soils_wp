<?php

// *****************************************************
// This script uses the DataTables Editor library (https://editor.datatables.net/manual/php/)
// The function below creates an Editor object that connects to the table or view named in the initial Editor::inst() call. Other scripts can call this function via a WordPress Ajax action to SELECT, UPDATE, INSERT or DELETE records from this table or view.
// (Views are not editable directly, so views will be setup to only accept SELECT calls)
// *****************************************************
  use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Options,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate;

add_action('wp_ajax_dt_samples','dt_samples');

function dt_samples() {

  include get_home_path() . "content/plugins/wordpress-datatables/DataTablesEditor/php/DataTables.php";
  check_ajax_referer('pa_nonce', 'secure');
  $_REQUEST = replace_dt_action($_REQUEST);


  // Build our Editor instance and process the data coming from _POST
  $editor = Editor::inst( $db, 'samples' )
    ->fields(
      Field::inst('samples.id')->validator('Validate::notEmpty'),
      FIeld::inst('samples.sampling_date'),
      Field::inst('samples.sampling_depth'),
      Field::inst('samples.sample_comments'),
      Field::inst('samples.farmer_id'),
      Field::inst('samples.collector_name'),
      Field::inst('samples.latitude'),
      Field::inst('samples.longitude'),
      Field::inst('samples.altitude'),
      Field::inst('samples.accuracy'),
      Field::inst('samples.project_id')
    );



  //if the request is a GET (action = fetch), and there is a $_GET['id'] defined, then filter the results to only return the requested record:
  $id = $_REQUEST['id'] ?? null;

  if($id){
    
    //add where filter to $editor:
    $editor = $editor->where('samples.id',$id);
  }

  //process data from the $editor and echo it to the ajax response:
  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();
}
