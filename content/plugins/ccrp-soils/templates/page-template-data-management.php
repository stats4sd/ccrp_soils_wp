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

      if(count($groups[groups] == 0)) { ?>
        <p class="alert alert-info">To view data for a project you must first be invited to the group. Please either go to the projects page and request membership for your project, or contact rms@stats4sd.org</p>

      <?php } ?>

      <p>This page is where you manage the forms that are shared with you through Kobotoolbox, and review and download data collected through those forms.</p>

      <?php if(count($groups[groups] > 1)) { ?>
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
          <p>Kobotoolbox user account: <span class="font-weight-bold"><?php echo $koboaccount; ?></span> (update this)</p>
          
          <?php  if($koboaccount != "none"){ ?>
            <h5>Form Setup</h5>
            <p>The table below shows the forms available for your project. Forms highlighted in green are shared with your kobotoolbox account. To share a new form, click the "share" button in the last column.</p>
          <?php } ?>

            <table id="forms_table_<?php echo $groupid; ?>"></table>

        
      <?php } ?>
      

      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

    <?php get_sidebar( 'content-bottom' ); ?>

  </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
