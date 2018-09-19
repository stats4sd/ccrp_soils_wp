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
add_action('wp_ajax_dt_farms','dt_farms');

function dt_farms() {

  //include DataTables php script
  include dirname(__FILE__) . "/wordpress_datatables/DataTables_Editor/php/DataTables.php";

  if($_SERVER['REQUEST_METHOD'] === "POST"){
    if(isset($_POST['dt_action']) && isset($_POST['action'])) {
      $_POST['action'] = $_POST['dt_action'];
      unset($_POST['dt_action']);
    }
    elseif(isset($_POST['action'])) {
      unset($_POST['action']);
    }
  }


  //checks that the correct Nonce was passed to show the request came from the WordPress website.
  check_ajax_referer('pa_nonce', 'secure');

  $user_group_id = $_REQUEST['vars']['user_group_id'] ?? null;



  $editor = Editor::inst( $db, 'farmers' )
    ->fields(
      // Farm-level data
      Field::inst( 'farmers.id' )->validator( 'Validate::notEmpty' ),
      Field::inst( 'farmers.farmer_name' ),
      Field::inst( 'farmers.community_id' )
        ->options( Options::inst()
          ->table('communities')
          ->value('id')
          ->label('community_label')
        ),
      //Community-level data:
      Field::inst('communities.community_label'),
      
      //Projects data
      Field::inst('farmers.project')
        ->options( Options::inst()
          ->table('wp_bp_groups')
          ->value('id')
          ->label('name')),
      Field::inst('wp_bp_groups.name')
    )
    ->leftJoin('communities','communities.id', '=','farmers.community_id')
    ->leftJoin('wp_bp_groups','wp_bp_groups.id', '=','farmers.project');
  
  if($user_group_id) {
    $editor
      ->where( function($q) use ($user_group_id) {
        $q->where("farmers.project",'0',"=");
        foreach($user_group_id as $group){
          $q->or_where("farmers.project",$group);
        }
      });
  }

  $data = $editor
    ->process( $_POST )
    ->data();

  echo json_encode($data);

  wp_die();

}
