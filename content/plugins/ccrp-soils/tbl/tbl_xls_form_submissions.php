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

add_action('wp_ajax_dt_xls_form_submissions','dt_xls_form_submissions');

function dt_xls_form_submissions() {

  //include DataTables php script
  include WP_PLUGIN_DIR . "/wordpress-datatables/DataTablesEditor/php/DataTables.php";
  check_ajax_referer('pa_nonce', 'secure');

  if($_SERVER['REQUEST_METHOD'] === "POST"){
    if(isset($_POST['dt_action']) && isset($_POST['action'])) {
      $_POST['action'] = $_POST['dt_action'];
      unset($_POST['dt_action']);
    }
    elseif(isset($_POST['action'])) {
      unset($_POST['action']);
    }
  }
  


  // Build our Editor instance and process the data coming from _POST
  $editor = Editor::inst( $db, 'xls_form_submissions' )
    ->fields(
      Field::inst('xls_form_submissions.id')->validator('Validate::notEmpty'),
      Field::inst('xls_form_submissions.record_data')->validator('Validate::notEmpty'),
      Field::inst('xls_form_submissions.form_kobo_id')->validator('Validate::notEmpty'),
      Field::inst('xls_form_submissions.uuid')
    );



  //if the request is a GET (action = fetch), and there is a $_GET['id'] defined, then filter the results to only return the requested record:

  $id = $_REQUEST['id'] ?? null;

  if($id){
    
    //add where filter to $editor:
    $editor = $editor->where('xls_form_submissions.id',$id);
  }

  //process data from the $editor and echo it to the ajax response:
  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();
}
