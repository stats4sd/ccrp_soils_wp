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

add_action('wp_ajax_dt_analysis_agg','dt_analysis_agg');

function dt_analysis_agg() {

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
  $editor = Editor::inst( $db, 'analysis_agg' )
    ->fields(
      Field::inst('analysis_agg.id')->validator('Validate::notEmpty'),
      FIeld::inst('analysis_agg.analysis_date'),
      Field::inst('analysis_agg.sample_id'),
      Field::inst('analysis_agg.weight_soil'),
      Field::inst('analysis_agg.weight_cloth'),
      Field::inst('analysis_agg.weight_stones2mm'),
      Field::inst('analysis_agg.weight_2mm_aggreg'),
      Field::inst('analysis_agg.weight_cloth_250micron'),
      Field::inst('analysis_agg.weight_250micron_aggreg'),
      Field::inst('analysis_agg.pct_stones'),
      Field::inst('analysis_agg.twomm_aggreg_pct'),
      Field::inst('analysis_agg.twofiftymicr_aggreg_pct')

    );



  //if 'id' is available and we're getting data:
  $id = $_REQUEST['id'] ?? null;

  if($id){
    //add where filter to $editor:
    $editor = $editor->where('analysis_agg.id',$id);
  }

  //process data from the $editor and echo it to the ajax response:
  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();
}
