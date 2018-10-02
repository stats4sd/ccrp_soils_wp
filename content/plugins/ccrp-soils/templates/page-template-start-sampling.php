<?php
/**
 * Template Name: start-sampling
 **
 * @package sparkling-child
 */


get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post(); ?>

      <h3 class="mb-4">Option 1 - use your own system</h3>
      <p>To start sampling using our forms within your own data management system, you will need to:</p>
        <ol>
          <li>Download the XLS forms from our downloads page</li>
          <li>Upload them to your ODK Aggregate service</li>
          <li>Use our QR generation tool to create QR codes for your soil samples.</li>
        </ol>
      

      <h3 class="mb-4">Option 2 - use this data platform</h3>
      <p>For more information on the platform, how it's built and how it can help your data collection and processing for soil data, see the About page.</p>
      <?php

      if(!is_user_logged_in()){ ?>
        <p class="alert alert-info">If you want to setup your project on this platform, please register your interest using the link in the sidebar. If your project is already in the project list, please ask your project admin to invite you. They can send an invite to your email that will let you create an account on the site.</p>
      <?php }
      else{ 
        //get user's groups
        $groups = BP_Groups_Member::get_group_ids( get_current_user_id());

        if(count($groups[groups]) == 0 ) {  ?>
          <p class="alert alert-info">It looks like you are not a member of any projects. If your project is listed in the Projects page, please request a membership. The project admin will be notified and can give you access to the project's resources.</p>
        <?php }
        
        if(count($groups[groups]) > 0 ) {  ?>
          <p>To collect data through this platform, please do the following:</p>
          <ol>
            <li>Check that you have added your Kobotoolbox username to your project.</li>
            <li>Go to the Data Management page, to create a set of forms and see data you have collected.</li>
          </ol>
  

      <?php }} ?>
      

      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

    <?php get_sidebar( 'content-bottom' ); ?>

  </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
