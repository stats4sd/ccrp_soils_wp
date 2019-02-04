<?php

/*=============================================
=            Ajax Functions                   =
=============================================*/


//Function to get a defined SQL table (or view) and expose it as a json blob into javascript:
//This is an AJAX function, so first add the 2 actions to let Wordpress handle the ajax calls properly:
//
add_action('wp_ajax_create_barcode','create_barcode');
add_action('wp_ajax_nopriv_create_barcode','create_barcode');
function create_barcode() {
  GLOBAL $wpdb;

  check_ajax_referer('pa_nonce', 'secure');


  //if a root_id has been passed, setup the codes based on that.
  if(isset($_POST['root_id'])){
    $root_id = $_POST['root_id'];
    $root_id .= "_";

  }

  //otherwise, create a random 4-digit code to use as the base-code
  else{
    $root_id = random_int(1000,9999);
    $root_id = (string)$root_id;
    $root_id .= "_";
  }


  $number = $_POST['number'];

  $query = [];
  $ids = [];
  //generate the number of barcodes asked for:
  for($i = 0; $i<$number; $i++){
    // //create entry in barcodes table (to generate an auto-increment value);
    $query[] = $wpdb->insert('barcodes',array('farm_id' => $root_id, 'status'=>"gen"));
    //get the ID of the inserted row:
    $id[] = $wpdb->insert_id;
  }

  //then, run update command to turn the newly 'gen'-ed ID into a code that can be barcoded. This code will include the country and community IDs

  $updateQuery = $wpdb->get_results("
                                       UPDATE `barcodes`
                                       SET `barcodes`.`code` = CONCAT(`farm_id`,`barcodes`.`id`), `barcodes`.`status`='coded'
                                       WHERE `barcodes`.`status`='gen'");
  for($j=0;$j<$number;$j++){
    $id[$j] = $root_id . $id[$j];
  }
  wp_send_json_success($id);
} //end create barcode()

add_action('wp_ajax_create_community_barcodes','create_community_barcodes');
add_action('wp_ajax_nopriv_create_community_barcodes','create_community_barcodes');
function create_community_barcodes() {
  GLOBAL $wpdb;

  $farmers = $_POST['farmers'];
  $farmer_count = count($farmers);

  $query = [];
  $ids = [];
  $codes = [];
  $results = [];

  //for each farmer, generate a code and put it into array;
  for($x = 0; $x < $farmer_count; $x++) {
    // $farmer_id = $farmers[$x]['id'];
    // $farmer_name = $farmers[$x]['farmer_name'];

    //create entry in barcodes table (to generate an auto-increment value);
    $query[$x] =  $wpdb->insert('barcodes',array('farm_id' => $farmers[$x]['id'], 'status'=>"gen"));

    //get the ID of the inserted row:
    $id[$x] = $wpdb->insert_id;

    //concatenate to make the code
    $codes[$x] = $farmers[$x]['id'] . $id[$x];

    $results[$x] = array(
      "code" => $codes[$x],
      "farmer_id" => $farmers[$x]['id'],
      "farmer_name" => $farmers[$x]['farmer_name'],
    );

    //add start or end of row for printing:
    //
    if(($x+1) % 2 == 0 ) {
      $results[$x]["start_div"] = "";
      $results[$x]["end_div"] = "</div>";
    }
    else {
      $results[$x]["start_div"] = "<div class='row'>";
      $results[$x]["end_div"] = "";
    }

  }
  //then, run update command to turn the newly 'gen'-ed AI into a code that can be barcoded. This code will include the country and community IDs

  $updateQuery = $wpdb->get_results("
                                       UPDATE `barcodes`
                                       SET `barcodes`.`code` = CONCAT(`farm_id`,`barcodes`.`id`), `barcodes`.`status`='coded'
                                       WHERE `barcodes`.`status`='gen'");

  wp_send_json_success($results);

} //end create barcode()

add_action('wp_ajax_update_barcodes','update_barcodes');
add_action('wp_ajax_nopriv_update_barcodes','update_barcodes');
function update_barcodes(){
  GLOBAL $wpdb, $qrcodetag;
  $soilsdb = new wpdb('root','ssd@soils-dev','soils','localhost');


  $community_id = $_POST['value'];

  $sql = "UPDATE `barcodes` set `status`='printed' where `community_id`='" . $community_id . "';";

  $query = $soilsdb->get_results($sql);
  wp_send_json_success(json_encode($query));
}
