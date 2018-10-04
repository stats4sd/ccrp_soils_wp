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

add_action('wp_ajax_dt_analysis_poxc','dt_analysis_poxc');

function dt_analysis_poxc() {

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
  $editor = Editor::inst( $db, 'analysis_poxc' )
    ->fields(
      Field::inst('analysis_poxc.id')->validator('Validate::notEmpty'),
      FIeld::inst('analysis_poxc.analysis_date'),
      Field::inst('analysis_poxc.sample_id'),
      Field::inst('analysis_poxc.weight_soil'),
      Field::inst('analysis_poxc.color'),
      Field::inst('analysis_poxc.color100'),
      Field::inst('analysis_poxc.conc_digest'),
      Field::inst('analysis_poxc.cloudy'),
      Field::inst('analysis_poxc.colorimeter'),
      Field::inst('analysis_poxc.raw_conc'),
      Field::inst('analysis_poxc.poxc_sample'),
      Field::inst('analysis_poxc.poxc_soil'),
      Field::inst('analysis_poxc.correct_moisture'),
      Field::inst('analysis_poxc.moisture'),
      Field::inst('analysis_poxc.poxc_soil_corrected')

    );



  //if 'id' is available and we're getting data:
  $id = $_REQUEST['id'] ?? null;

  if($id){
    //add where filter to $editor:
    $editor = $editor->where('analysis_poxc.id',$id);
  }

  //process data from the $editor and echo it to the ajax response:
  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();
}
