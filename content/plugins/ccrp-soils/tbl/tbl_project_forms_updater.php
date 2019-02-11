<?php

/*
 * Example PHP implementation used for the index.html example
 */

//get user group submitted:
//

// Alias Editor classes so they are easy to use
use
  DataTables\Editor,
  DataTables\Editor\Field,
  DataTables\Editor\Format,
  DataTables\Editor\Mjoin,
  DataTables\Editor\Options,
  DataTables\Editor\Upload,
  DataTables\Editor\Validate;

//add the action into the WordPress Ajax hook, so it can be called with WP authentication / security:
add_action('wp_ajax_dt_project_forms_updater','dt_project_forms_updater');
add_action('wp_ajax_nopriv_dt_project_forms_updater','dt_project_forms_updater');


// Created this seperate function for updating data. The "dt_project_forms" function pulls from a mySQL view, which cannot be edited. So, this one goes to the main table behind the view.
function dt_project_forms_updater() {

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

  //get request variables;
  $project_id = $_REQUEST['vars']['project_id'] ?? null;

  //setup the editor object
  $editor = Editor::inst( $db, 'projects_xls_forms' )

  //prepare fields from mysql table
  ->fields(
    Field::inst( 'projects_xls_forms.id' )->validator( 'Validate::notEmpty' ),
    Field::inst( 'projects_xls_forms.project_id' ),
    Field::inst( 'projects_xls_forms.form_id' ),
    Field::inst( 'projects_xls_forms.form_kobo_id' )->setFormatter( Format::ifEmpty( null ) ),
    Field::inst( 'projects_xls_forms.form_kobo_id_string' )->setFormatter( Format::ifEmpty( null ) ),

    Field::inst( 'projects_xls_forms.deployed' ),
    Field::inst( 'projects_xls_forms.records' )

  );

  if($project_id){
    $editor = $editor
    ->where("projects_xls_forms.project_id",$project_id);
  }

  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();

}


