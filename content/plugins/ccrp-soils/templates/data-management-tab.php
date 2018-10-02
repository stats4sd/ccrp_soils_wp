<?php
/**
 * Template Name: data-management
 **
 * @package sparkling-child
 */

//get user's groups
$groups = BP_Groups_Member::get_group_ids( get_current_user_id());

if(count($groups[groups] == 0)) { ?>
  <p class="alert alert-info">To view data for a project you must first be invited to the group. Please either go to the projects page and request membership for your project, or contact rms@stats4sd.org</p>

<?php } ?>

<p>This page is where you manage the forms that are shared with you through Kobotoolbox, and review and download data collected through those forms.</p>

<?php
if(count($groups[groups] > 1)) { ?>

  <p class="alert alert-info">You are currently a member of <?php echo count($groups[groups]); ?> projects. Please select which project to interact with from the dropdown menu below:</p>
  
<?php } ?>


<h3 class="mb-4">Form Setup</h3>
<p>The table below shows the forms available for your project.


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


  <?php }
} ?>
