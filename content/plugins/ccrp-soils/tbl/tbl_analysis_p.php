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

add_action('wp_ajax_dt_analysis_p','dt_analysis_p');

function dt_analysis_p() {

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
  $editor = Editor::inst( $db, 'analysis_p' )
    ->fields(
      Field::inst('analysis_p.id')->validator('Validate::notEmpty'),
      FIeld::inst('analysis_p.analysis_date'),
      Field::inst('analysis_p.weight_soil'),
      Field::inst('analysis_p.volume_filtered_extract'),
      Field::inst('analysis_p.volume_topup'),
      Field::inst('analysis_p.colorimeter'),
      Field::inst('analysis_p.colorimeter_100'),
      Field::inst('analysis_p.comment_cloudy_solution'),
      Field::inst('analysis_p.raw_conc_extract'),
      Field::inst('analysis_p.soil_conc_olsen'),
      Field::inst('analysis_p.sample_id')
    );



  //if 'id' is available and we're getting data:
  $id = $_REQUEST['id'] ?? null;
  if($id){
    //add where filter to $editor:
    $editor = $editor->where('analysis_p.id',$id);
  }

  //process data from the $editor and echo it to the ajax response:
  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();
}
