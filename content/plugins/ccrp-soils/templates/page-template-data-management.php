<?php
/**
 * Template Name: data-management
 **
 * @package sparkling-child
 */

GLOBAL $wpdb;

$test = $wpdb->get_results(
"SELECT * FROM xls_form_questions");

echo "<pre>" . var_export($test,true) . "</pre>";
echo "<br/>######################<br/>";


get_header(); ?>

  <div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post();

      //get user's groups
      $groups = BP_Groups_Member::get_group_ids( get_current_user_id());

      if(count($groups['groups']) == 0) { ?>
        <p class="alert alert-info">To view data for a project you must first be invited to the group. Please either go to the projects page and request membership for your project, or contact rms@stats4sd.org</p>

      <?php } ?>

      <h3 class="mb-4">Form and Data Management</h3>
      <p>This page is where you manage the forms that are shared with you through Kobotoolbox, and review and download data collected through those forms.</p>

      <div class="card">
        
        <h4 class="card-header">Instructions</h4>
        <div class="card-body">
          <h5>1. Sync Forms to Kobotoolbox</h5>
          <p>The table below shows the forms available for your project. Deployed forms are shared with your Kobotoolbox account - you should be able to see them by logging into Kobotools using your project account. To deploy a form, click the button in the Status column.</p>
          <h5 class="mt-4">2. Collect Data</h5>
          <p>With your forms deployed, you can collect data via Kobotoolbox / ODK Collect in the normal way. To pull new records from Kobotoolbox, click the button above. This will update the table with the number of records collected with each form.</p>
          <h5>3. Merge and download data</h5>
          <p>You can download data from Kobotoolbox directly, but this will give you one data file per form. Using this platform, you can get a merged dataset, containing 1 row per soil sample and data from all the forms above.</p>
          </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h4>Forms and Data</h4>
        </div>
        <?php if(count($groups['groups']) > 1) { ?>
          <div class="card-header">
            <p class="alert alert-info mb-0">You are currently a member of <?php echo count($groups['groups']); ?> projects. Use the tabs to switch between your projects.
            </p>
          </div>
          
          <div class="card-header mb-0 pb-0">
            <ul class="nav nav-tabs" id="projectTabs" role="tablist">
              <?php foreach($groups['groups'] as $groupid){
                $group_details = groups_get_group(array('group_id' => $groupid));
                $group_name = $group_details->name;

                //render tab
                echo "<li class='nav-item'><a class='nav-link' id='".$groupid."-tab' data-toggle='tab' href='#".$groupid."' role='tab' aria-controls='".$groupid."'>".$group_name."</a></li>";
              }
              ?>
            </ul>
          </div>

        <?php }
        else {
          // if there's only 1 project, make sure its tab is active below.
          $active = "active";
        }
        ?>
        
        <div class="tab-content card-body">
            
          <?php foreach($groups['groups'] as $groupid) {
              
            //get group details;
            $group_details = groups_get_group(array('group_id' => $groupid));
            $koboaccount = groups_get_groupmeta($groupid,'kobotools_account');

            if($koboaccount == ""){
              $koboaccount = 'none';
            }

            


            ?>
            <div class="tab-pane <?php echo $active; ?>" id="<?php echo $groupid; ?>" role="tabpanel" aria-labelledby="<?php echo $groupid; ?>-tab">
              
              <h3 class="mb-4">Project: <?php echo $group_details->name; ?> </h3>
              <p>Kobotoolbox user account: <span class="font-weight-bold"><?php echo $koboaccount; ?></span></p>
              <p class="font-italic"><a href="https://kc.kobotoolbox.org/">Go to Kobotools</a></p>
              <?php  if($koboaccount != "none"){ ?>


                <div class="card table-card bg-light my-3 p-0">
                  <div class="card-header">
                    <h5 class="mb-0">Forms Set</h5>
                  </div>
                  <div class="card-body p-0">
                    <table style="width: 100%" class='table table-striped table-bordered py-0 my-0' id="forms_table_<?php echo $groupid; ?>"></table>
                  </div>
                  <div class="card-footer table-footer">
                    <div id="buttons_for_forms_table<?php echo $groupid; ?>"></div>
                  </div>
                </div>
          
              <?php }

              else{
                ?>
                <p class='alert alert-info'>Please add your project kobotoolbox account to continue</p>
              <?php } ?>
            </div>
          <?php }?>


        </div> <!-- end tab content -->
      </div> <!-- end actions card -->

      <div id="working-space"></div>
      <div id="alert-space"></div>
      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

    <?php get_sidebar( 'content-bottom' ); ?>

  </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
