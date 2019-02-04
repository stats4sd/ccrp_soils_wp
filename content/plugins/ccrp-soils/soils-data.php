<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           stats4sd_api
 *
 * @wordpress-plugin
 * Plugin Name:       Soils Data
 * Plugin URI:        http://soils.stats4sd.org/
 * Description:       A small plugin to contain the custom code to interact with CCRP soils data
 * Version:           0.0.1
 * Author:            Stats4SD
 * Author URI:        http://example.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       ccrp-soils
 * Domain Path:       /languages
 */


// *****************************************************
// Starting Code - Include Dependancies etc
// *****************************************************

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Require Guzzle for making direct HTTP requests
require_once "vendor/autoload.php";
use GuzzleHttp\Client;

// Check for get_home_path() Wordpress helper function
if ( !function_exists( 'get_home_path' ) ) require_once( dirname(__FILE__) . '/../../../wp/wp-admin/includes/file.php' );


include plugin_dir_path( __FILE__ ) . "buddypress/class-my-bp-groups-widget.php";
include plugin_dir_path( __FILE__ ) . "buddypress/custom-buddypress-tab.php";
include plugin_dir_path( __FILE__ ) . "buddypress/functions.php";
include plugin_dir_path( __FILE__ ) . "functions/barcodes.php";


/*
All the files with names starting with "tbl_" contain AJAX functions for querying the database.
They use DataTables Editor to make the connection, to get, edit and create records in the database.

So, all the "tbl_" files are required. To avoid manually specifying all of them, instead scan the directory and add all that are found.
 */


// *****************************************************
// Scan the current directory for files to require:
// *****************************************************
foreach(scandir(dirname(__FILE__) . "/tbl") as $filename) {
  // if filename starts with "tbl_" and has a php file extension.

  if(substr($filename,0,4)=="tbl_" && substr($filename,-4,4)==".php"){
    $path = dirname(__FILE__) . "/tbl/" . $filename;

    //double check the file exists
    if(is_file($path)){
      //require the file
      require_once $path;
    }
  }
}

class Soils_Data_Plugin {

  // *****************************************************
  // Initialise the plugin
  // *****************************************************
  public function __construct() {

    //add WordPress hooks / actions here:

    //queue general scripts
    add_action('wp_enqueue_scripts',array($this,'dt_scripts'));

    //create extra things when new group is created:
    add_action('groups_group_create_complete',array($this,'create_bp_group_extra'));

  }

  // *****************************************************
  // Convenience function to get a particular set of values to 'localize' javascript files:
  // *****************************************************
  public static function getLocal() {

    // get buddypress groups of current user;
    $user_groups_ids = BP_Groups_Member::get_group_ids( get_current_user_id());
    $user_groups_ids = $user_groups_ids['groups'];

    if ( !function_exists( 'groups_get_group' ) ) {
      require_once get_site_url() . '/content/plugins/buddypress/bp-groups/bp-groups-functions.php';
    }

    //format groups to include kobotools_account.
    $user_groups = array_map(function($id){
      $group = groups_get_group($id);
      $group->kobotools_account = groups_get_groupmeta($id,"kobotools_account");
      return $group;
    }, $user_groups_ids);

    return array(
      'user_id' => get_current_user_id(),
      'site_url' => get_site_url(),
      'ajax_url' => admin_url('admin-ajax.php'),
      'mustache_url' => plugin_dir_url(__FILE__) . "views",
      'nonce' => wp_create_nonce('pa_nonce'),
      'node_url' => NODE_URL,
      'user_group_ids' => $user_groups_ids,
      'user_groups' => $user_groups,
      'lang' => apply_filters( 'wpml_current_language', NULL )
    );
  }

  // *****************************************************
  // Function to add the needed Javascript files for each page into the queue:
  // *****************************************************
  public function enqueue_js($filename) {
    GLOBAL $post;

    $localize = $this->getLocal();


    //check if there is an associated Javascript file (associated by filename);
    $scriptpath = "js/" . $filename . ".js";

    if(file_exists(plugin_dir_path(__FILE__) . $scriptpath)) {

      //if there is - enqueue it and pass it the $localize aray as "vars"
      wp_register_script($filename, plugin_dir_url(__FILE__) . $scriptpath, array('jquery'),time(),true);

      //localizing passes the $localize array into the javascript with the given variable name, in this case "vars":
      wp_localize_script($filename, 'vars', $localize);
      wp_enqueue_script($filename);
    }
  }

  // *****************************************************
  // Queue dependancies, including DataTables
  // *****************************************************
  protected function dt_scripts() {

    $localize = $this->getLocal();

    //plugin css file:
    wp_enqueue_style('soils-style', plugin_dir_url( __FILE__ ) .'css/soils-style.css',array(),time() );

    wp_register_script('popper-script',  plugin_dir_url(__FILE__) . "js/node_modules/popper.js/dist/umd/popper.min.js", array(), time(), true );
    wp_enqueue_script('popper-script');

    //register and queue the general datatables functions:
    wp_register_script("datatables_custom", plugin_dir_url(__FILE__) . "js/datatables.js", array('jquery'),time(),true);
    wp_localize_script("datatables_custom", 'vars', $localize);
    wp_enqueue_script("datatables_custom");


    //add Select2 scripts:
    wp_enqueue_style('select2-style',plugin_dir_url( __FILE__ ) . "js/node_modules/select2/dist/css/select2.min.css","4.0.6");
    wp_register_script('select2-script',plugin_dir_url( __FILE__ ) . "js/node_modules/select2/dist/js/select2.full.min.js",array('jquery'),"4.0.6",true);
    wp_enqueue_script('select2-script');

    //register and queue js mustache (for calling and rendering mustache templates client-side);
    wp_register_script("mustache-js", plugin_dir_url(__FILE__) . "js/node_modules/mustache/mustache.min.js",array(),time(),true);
    wp_localize_script("mustache-js", 'vars', $localize);
    wp_enqueue_script("mustache-js");

    wp_register_script( 'jqueryprint-script', plugin_dir_url( __FILE__ ) . 'js/jquery-print.js', array( 'jquery' ), time(), true );
    wp_enqueue_script("jqueryprint-script");

    wp_register_script( 'qr-script', plugin_dir_url( __FILE__ ) . 'js/node_modules/qrcodejs/qrcode.min.js', array( 'jquery' ), time(), true );
    wp_enqueue_script("qr-script");


  }

  // *****************************************************
  // GIVE FORMS TO A NEW PROJECT
  // *****************************************************
  protected function create_bp_group_extra($group_id) {
    global $wpdb;

    //get list of active soils forms;
    $forms = $wpdb->get_results("SELECT * FROM xls_forms", ARRAY_A);


    //add each form to the newly created group
    foreach($forms as $index => $form) {
      $data = array(
        "form_id" => $form["id"],
        "project_id" => $group_id,
        "deployed" => false
      );

      $insert = $wpdb->insert("projects_xls_forms",$data);
    }

  }

}

// Initialize the plugin
$soils_data = new Soils_Data_Plugin();


