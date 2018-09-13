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
add_action('wp_ajax_dt_districts','dt_districts');

function dt_districts() {

  //include DataTables php script
  include dirname(__FILE__) . "/wordpress_datatables/DataTables_Editor/php/DataTables.php";

  //checks that the correct Nonce was passed to show the request came from the WordPress website.
  check_ajax_referer('pa_nonce', 'secure');

  if($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['vars'])) {
    $user_group_id = $_GET['vars']['user_group_ids'];
  }

  // Build our Editor instance and process the data coming from _POST
  $editor = Editor::inst( $db, 'districts' )
    ->fields(
      Field::inst( 'districts.id' )->validator( 'Validate::notEmpty' ),
      Field::inst( 'districts.district_label' )->validator( 'Validate::notEmpty'),
      Field::inst( 'districts.country_id' )->validator('Validate::notEmpty')
        ->options( Options::inst()
          ->table('countries')
          ->value('id')
          ->label('country_label')
        ),
      Field::inst('countries.country_label'),
      Field::inst( 'districts.project' )->validator('Validate::notEmpty')
        ->options( Options::inst()
          ->table('wp_bp_groups')
          ->value('id')
          ->label('name')
        ),
      Field::inst('wp_bp_groups.name')
    )
    ->leftJoin('countries','countries.id', '=','districts.country_id')
    ->leftJoin('wp_bp_groups','wp_bp_groups.id', '=','districts.project')
    ;

  if($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['vars'])) {
    $editor
      ->where( function($q) use ($user_group_id) {
        $q->where("districts.project",'0',"=");
        foreach($user_group_id as $group){
          $q->or_where("districts.project",$group);
        }
      });
  }

  $data = $editor
    ->process( $_POST )
    ->data();

  echo json_encode($data);

  wp_die();

}
