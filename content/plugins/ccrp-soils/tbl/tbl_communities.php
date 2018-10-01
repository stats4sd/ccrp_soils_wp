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
add_action('wp_ajax_dt_communities','dt_communities');

function dt_communities() {

  //include DataTables php script
  include get_home_path() . "content/plugins/wordpress-datatables/DataTablesEditor/php/DataTables.php";

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

      $_POST['action'] = $_POST['dt_action'];
      unset($_POST['dt_action']);
    elseif(isset($_POST['action'])) {
      unset($_POST['action']);
    }
  }

  //get request variables;
  $user_group_id = $_REQUEST['vars']['user_group_ids'] ?? null;

  // Build our Editor instance and process the data coming from _POST
  $editor = Editor::inst( $db, 'communities' )
  ->fields(
    Field::inst( 'communities.id' )->validator( 'Validate::notEmpty' ),
    Field::inst( 'communities.community_label' )->validator( 'Validate::notEmpty'),
    
    Field::inst( 'communities.district_id' )->validator('Validate::notEmpty')
      ->options( Options::inst()
        ->table('districts')
        ->value('id')
        ->label('district_label')
      ),
    Field::inst('districts.district_label'),
   Field::inst( 'communities.project' )->validator('Validate::notEmpty')
      ->options( Options::inst()
        ->table('wp_bp_groups')
        ->value('id')
        ->label('name')
      ),
    Field::inst('wp_bp_groups.name')


  )
  ->leftJoin('districts','districts.id', '=','communities.district_id')
  ->leftJoin('wp_bp_groups','wp_bp_groups.id', '=','communities.project')
  //join into the farmers table to get farmer information into the communities tab.
  ->join(
    Mjoin::inst( 'farmers')
      ->link('communities.id','farmers.community_id')
      ->fields(
        Field::inst( 'id' ),
        Field::inst( 'farmer_name' )
      )
    );

  if($user_group_id) {
    $editor
      ->where( function($q) use ($user_group_id) {
        $q->where("communities.project",'0',"=");
        foreach($user_group_id as $group){
          $q->or_where("communities.project",$group);
        }
      });
  }

  $data = $editor
    ->process( $_POST )
    ->data();

  echo json_encode($data);

  wp_die();

}