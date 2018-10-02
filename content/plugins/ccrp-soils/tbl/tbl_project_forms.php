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
add_action('wp_ajax_dt_project_forms','dt_project_forms');

function dt_project_forms() {

  //include DataTables php script
  include get_home_path() . "/content/plugins/wordpress-datatables/DataTablesEditor/php/DataTables.php";
  check_ajax_referer('pa_nonce', 'secure');
    if(isset($request['dt_action']) && isset($request['action'])) {
      $request['action'] = $request['dt_action'];
      unset($request['dt_action']);
    }

  //get request variables;
  $project_id = $_REQUEST['vars']['project_id'] ?? null;

  //setup the editor object
  $editor = Editor::inst( $db, 'project_forms_info' )

  //prepare fields from mysql table
  ->fields(
    Field::inst( 'project_forms_info.id' )->validator( 'Validate::notEmpty' ),
    Field::inst( 'project_forms_info.project_id' ),
    Field::inst( 'project_forms_info.project_name' ),
    Field::inst( 'project_forms_info.project_slug' ),

    Field::inst( 'project_forms_info.form_id' ),
    Field::inst( 'project_forms_info.project_kobotools_account' ),

    Field::inst( 'xls_forms.form_title'),
    Field::inst( 'xls_forms.form_id'),
    Field::inst( 'xls_forms.default_language'),
    Field::inst( 'xls_forms.version'),
    Field::inst( 'xls_forms.instance_name'),

    Field::inst( 'project_forms_info.form_kobo_id' ),
    Field::inst( 'project_forms_info.deployed' ),
    Field::inst( 'project_forms_info.count_records' ),
    Field::inst( 'project_forms_info.id_list' )


  )
  ->leftJoin('xls_forms','xls_forms.id','=','project_forms_info.form_id');

  if($project_id){
    $editor = $editor
    ->where("project_forms_info.project_id","=",$project_id);
  }

  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();

}


