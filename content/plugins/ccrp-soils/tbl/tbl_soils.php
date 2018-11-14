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
add_action('wp_ajax_dt_soils','dt_soils');

function dt_soils() {

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


  $user_group_id = $_REQUEST['vars']['user_group_id'] ?? null;
  $project_id = $_REQUEST['vars']['project_id'] ?? null;;
  
  
  // OLD JOIN
  // //setup the editor object
  // $editor = Editor::inst( $db, 'samples' )

  // //prepare fields from mysql table
  // ->fields(
  //   Field::inst( 'samples.id' )->validator( 'Validate::notEmpty' ),
  //   Field::inst( 'samples.date' ),
  //   Field::inst( 'samples.depth' ),
  //   Field::inst( 'samples.texture' ),
  //   Field::inst( 'samples.at_plot' ),
  //   Field::inst( 'samples.plot_photo' ),
  //   Field::inst( 'samples.longitude' ),
  //   Field::inst( 'samples.latitude' ),
  //   Field::inst( 'samples.altitude' ),
  //   Field::inst( 'samples.accuracy' ),
  //   Field::inst( 'samples.comment' ),
  //   Field::inst( 'samples.plot_id' ),
  //   Field::inst( 'samples.farmer_quick' ),
  //   Field::inst(' samples.community_quick'),
  //   Field::inst(' samples.project_id')

  // )

  // //full joins to other tables;
  // ->join(
  //   Mjoin::inst('analysis_poxc')
  //   ->link('samples.id','analysis_poxc.sample_id')
  //   ->fields(
  //     FIeld::inst('analysis_date'),
  //     Field::inst('sample_id'),
  //     Field::inst('weight_soil'),
  //     Field::inst('color'),
  //     Field::inst('color100'),
  //     Field::inst('conc_digest'),
  //     Field::inst('cloudy'),
  //     Field::inst('colorimeter'),
  //     Field::inst('raw_conc'),
  //     Field::inst('poxc_sample'),
  //     Field::inst('poxc_soil'),
  //     Field::inst('correct_moisture'),
  //     Field::inst('moisture'),
  //     Field::inst('poxc_soil_corrected')
  //   )
  // )
  // ->join(
  //   Mjoin::inst('analysis_ph')
  //   ->link('samples.id','analysis_ph.sample_id')
  //   ->fields(
  //     FIeld::inst('analysis_date'),
  //     Field::inst('weight_soil'),
  //     Field::inst('vol_water'),
  //     Field::inst('reading_ph'),
  //     Field::inst('stability'),
  //     Field::inst('sample_id')
  //   )
  // )
  // ->join(
  //   Mjoin::inst('analysis_p')
  //   ->link('samples.id','analysis_p.sample_id')
  //   ->fields(
  //     Field::inst('sample_id'),
  //     Field::inst('analysis_date'),
  //     Field::inst('weight_soil'),
  //     Field::inst('vol_extract'),
  //     Field::inst('vol_topup'),
  //     Field::inst('color'),
  //     Field::inst('cloudy'),
  //     Field::inst('raw_conc'),
  //     Field::inst('olsen_p'),
  //     Field::inst('blank_water'),
  //     Field::inst('correct_moisture'),
  //     Field::inst('moisture'),
  //     Field::inst('olsen_p_corrected')
  //   )
  // );
  // 


  $editor = Editor::inst( $db, 'samples_merged','sample_id' )
  ->fields(
    Field::inst("samples_merged.project_id"),
    Field::inst("samples_merged.sample_id"),
    Field::inst("samples_merged.sampling_date"),
    Field::inst("samples_merged.username"),
    Field::inst("samples_merged.date"),
    Field::inst("samples_merged.depth"),
    Field::inst("samples_merged.texture"),
    Field::inst("samples_merged.at_plot"),
    Field::inst("samples_merged.plot_photo"),
    Field::inst("samples_merged.longitude"),
    Field::inst("samples_merged.latitude"),
    Field::inst("samples_merged.altitude"),
    Field::inst("samples_merged.accuracy"),
    Field::inst("samples_merged.comment"),
    Field::inst("samples_merged.farmer_quick"),
    Field::inst("samples_merged.community_quick"),
    Field::inst("samples_merged.plot_id"),
    Field::inst("samples_merged.analysis_p-date"),
    Field::inst("samples_merged.analysis_p-weight_soil"),
    Field::inst("samples_merged.analysis_p-vol_extract"),
    Field::inst("samples_merged.analysis_p-vol_topup"),
    Field::inst("samples_merged.analysis_p-cloudy"),
    Field::inst("samples_merged.analysis_p-color"),
    Field::inst("samples_merged.analysis_p-raw_conc"),
    Field::inst("samples_merged.analysis_p-olsen_p"),
    Field::inst("samples_merged.analysis_p-blank_water"),
    Field::inst("samples_merged.analysis_p-correct_moisture"),
    Field::inst("samples_merged.analysis_p-moisture"),
    Field::inst("samples_merged.analysis_p-olsen_p_corrected"),
    Field::inst("samples_merged.analysis_ph-date"),
    Field::inst("samples_merged.analysis_ph-weight_soil"),
    Field::inst("samples_merged.analysis_ph-vol_water"),
    Field::inst("samples_merged.analysis_ph-reading_ph"),
    Field::inst("samples_merged.analysis_ph-stability"),
    Field::inst("samples_merged.analysis_poxc-date"),
    Field::inst("samples_merged.analysis_poxc-weight_soil"),
    Field::inst("samples_merged.analysis_poxc-color"),
    Field::inst("samples_merged.analysis_poxc-color100"),
    Field::inst("samples_merged.analysis_poxc-conc_digest"),
    Field::inst("samples_merged.analysis_poxc-cloudy"),
    Field::inst("samples_merged.analysis_poxc-colorimeter"),
    Field::inst("samples_merged.analysis_poxc-raw_conc"),
    Field::inst("samples_merged.analysis_poxc-poxc_soil"),
    Field::inst("samples_merged.analysis_poxc-poxc_sample"),
    Field::inst("samples_merged.analysis_poxc-correct_moisture"),
    Field::inst("samples_merged.analysis_poxc-moisture"),
    Field::inst("samples_merged.analysis_poxc-poxc_soil_corrected")
  );

  if($project_id){
    $editor = $editor
    ->where("samples_merged.project_id",$project_id);
  }

  // if($user_group_id){
  //   $editor = $editor
  //   ->where( function($q) use ($user_group_id) {
  //     $q->where("samples.project_id",'0',"=");
  //     foreach($user_group_id as $group){
  //       $q->or_where("samples.project_id",$group);
  //     }
  //   });
  // }

  $data = $editor
  ->process( $_POST )
  ->data();

  echo json_encode($data);

  wp_die();

}


