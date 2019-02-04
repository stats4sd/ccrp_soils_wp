<?php
/**
 * Template Name: qr-codes
 *
 * This is the template that displays full width page without sidebar
 *
 * @package sparkling-child
 */

$soils_data = new Soils_Data_Plugin();
$local = $soils_data->enqueue_js('qr-codes');

get_header(); ?>

  <div id="primary" class="content-area">

    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post(); ?>


<h1><?php _e("Generate QR Codes","twentysixteen-child") ?></h1>
<p><?php _e("Use this page to generate QR codes that aren't linked to a specific location or farmer. This is used if you are getting quickly setup, or are mainly using the toolkit to aid with analysis of samples, and are managing your data elsewhere.","twentysixteen-child") ?></p>
<p><?php _e("Click the button below to generate a sheet of 6 sample codes for printing. Every code will be unique within the system. Simply generate and print as many sheets as you need for your work.","twentysixteen-child") ?></p>
<br/><br/><br/>
<button onclick="getCodes(6)"><?php _e("Generate Code Sheet for printing","twentysixteen-child") ?></button>
<div id='sample_sheet'></div>

        <?php get_template_part( 'content', 'page' ); ?>



      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

  </div><!-- #primary -->

<?php get_footer(); ?>
