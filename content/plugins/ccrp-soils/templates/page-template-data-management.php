<?php
/**
 * Template Name: data-management
 **
 * @package sparkling-child
 */


get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post();

      //get user's groups
      $groups = BP_Groups_Member::get_group_ids( get_current_user_id());

      if(count($groups[groups]) == 0) { ?>
        <p class="alert alert-info">To view data for a project you must first be invited to the group. Please either go to the projects page and request membership for your project, or contact rms@stats4sd.org</p>

      <?php } ?>

      <h3 class="mb-4">Form and Data Management</h3>
      <p>This page is where you manage the forms that are shared with you through Kobotoolbox, and review and download data collected through those forms.</p>

      <?php if(count($groups[groups]) > 1) { ?>
        <p class="alert alert-info">You are currently a member of <?php echo count($groups[groups]); ?> projects. The Data management section below will be repeated for each project you can access.
        </p>

      
      <?php }

      foreach($groups[groups] as $groupid) {
          
          //get group details;
          $group_details = groups_get_group(array('group_id' => $groupid));
          $koboaccount = groups_get_groupmeta($groupid,'kobotools_account');

          if($koboaccount == ""){
            $koboaccount = 'none';
          }
          ?>
          <h3 class="mb-4">Project: <?php echo $group_details->name; ?> </h3>
          <p>Kobotoolbox user account: <span class="font-weight-bold"><?php echo $koboaccount; ?></span></p>
          
          <?php  if($koboaccount != "none"){ ?>
            <h5>1. Sync Forms to Kobotoolbox</h5>
            <p>The table below shows the forms available for your project. Deployed forms are shared with your Kobotoolbox account - you should be able to see them by logging into Kobotools using your project account. To deploy a form, click the button in the Status column.</p>
            <div style='max-width:90%'>
            <table class="table table-striped" style="max-width:90%" id="forms_table_<?php echo $groupid; ?>"></table>
            </div>
            <h5 class="mt-4">2. Collect Data</h5>
            <p>With your forms deployed, you can collect data via Kobotoolbox / ODK Collect in the normal way. To pull new records from Kobotoolbox, click the button above. This will update the table with the number of records collected with each form.</p>
            <h5>3. Merge and download data</h5>
            <p>You can download data from Kobotoolbox directly, but this will give you one data file per form. Using this platform, you can get a merged dataset, containing 1 row per soil sample and data from all the forms above.</p>
            <p>Make sure you have pulled any new records from Kobotoolbox, then click below to download your data</p>
            <div class='btn btn-secondary' onclick='downloaddata(<?php echo $groupid ?>)'>Download Merged Data</div>
          <?php }

          else{
            ?>
            <p class='alert alert-info'>Please add your project kobotoolbox account to continue</p>
          <?php }
            

        
      } ?>
      

      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

    <?php get_sidebar( 'content-bottom' ); ?>

  </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
