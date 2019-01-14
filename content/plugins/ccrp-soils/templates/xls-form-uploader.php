<?php
/**
 * Template Name: xls-form-uploader
 *
 * This is the template that displays full width page without sidebar
 *
 * @package sparkling-child
 */

global $wpdb;

get_header();

$forms = $wpdb->get_results(
        $wpdb->prepare("SELECT id, form_id, form_title FROM xls_forms")
    );



?>


  <div id="primary" class="content-area">

    <main id="main" class="site-main" role="main">


<h1>XLS Form Uploader</h1>

 <?php if( current_user_can('administrator') || current_user_can('editor') ):  ?>


    <div class='alert alert-info'>
        <p>Use the form below to upload an XLS Form file. Make sure that the form contains a 'settings' sheet, and make sure that you have correctly added a form_id. The platform uses this form_id to check the form. </p>
        <p>If you are replacing an existing form, make sure that the form_id matches the form_id in the list below</p>
    </div>


<?php endif; ?>

<?php get_template_part( 'content', 'page' );

echo "<pre>" . var_export($forms,true) . "</pre>";
echo "<br/>######################<br/>";

?>

    </main><!-- #main -->

  </div><!-- #primary -->

<?php get_footer(); ?>
