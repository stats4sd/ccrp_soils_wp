<?php
/**
 * Template Name: location
 *
 * This is the template that displays full width page without sidebar
 *
 * @package sparkling-child
 */

get_header(); ?>

  <div id="primary" class="content-area">

    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post(); ?>

<!-- ########################## -->
<!-- ########################## -->
<!-- ########################## -->
<!-- Main Page Contents go here -->

<?php 

// Get current users' group(s):
$groupid = BP_Groups_Member::get_group_ids( get_current_user_id());

// check how many groups user is a member of:


if(count($groupid[groups]) == 0 ) {
echo "<div class='alert alert-warning'>Hello. Thank you for registering on the site. You need to be invited to one of the active projects before viewing this page. Please contact your project administrator.<br/><br/> For more information, please contact support@stat4sd.org.</div>";
die();
}

if(count($groupid[groups]) == 1) {
//use current() to get the first entry int the groupid array.
// This only works if a member is part of ONLY 1 group.
$this_id = current($groupid[groups]);

if($this_id == 3) {
  $district = "network";
  $community = "VBA";
  $farm = "farmer";
  $plot = "terrace";
}

else {
  $district = "district";
  $community = "village";
  $farm = "farm";
  $plot = "plot";
}

//echo "<pre>user is member of 1 group => group id = " . $this_id . "</pre>";

}

if(count($groupid[groups]) > 1) {
  //this is just for the admin: quickly change the views here:
  foreach($groupid[groups] as $group) {
    $this_id = $group;
    //echo "<pre>user group id = " . $this_id . "</pre>";
  $district = "Districts";
  $community = "Villages";
  $farm = "Farmers / Farms";
  $plot = "Plots";
  }
}

?>

<h1>Location Management</h1>
<div class='alert alert-info'>
  <p>This page allows you to add and update data about the places you are collecting soil samples. This system is currently done per-project, so for now you will see your project's communities available on this page.</p>
  <p>Use the tabbed interface below to review the list of locations, and add any new ones that you need for your project.</p>
</div>

<div class="row">
  <div class="col-lg-4">
    <div class="card table-card bg-light my-3 p-0">
      <div class="card-header"><h3 class='my-3'><?php echo $district;?></h3></div>
      <div class="card-body p-0">
          <table id="districtsTable" class='table table-striped table-bordered py-0 my-0 h-40' width="100%" ></table>
      </div>
      <div class="card-footer">
        <div id="dt-buttons_for_districtsTable">
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card table-card bg-light my-3 p-0">
      <div class="card-header"><h3 class='my-3'><?php echo $community;?></h3></div>
      <div class="card-body p-0">
          <table id="communitiesTable" class='table table-striped table-bordered py-0 my-0 h-40' width="100%" ></table>
      </div>
      <div class="card-footer">
        <div id="dt-buttons_for_communitiesTable">
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col">
    <div class="card table-card bg-light my-3 p-0">
      <div class="card-header"><h3 class='my-3'><?php echo $farm;?></h3></div>
      <div class="card-body p-0">
          <table id="farmsTable" class='table table-striped table-bordered py-0 my-0 h-40' width="100%" ></table>
      </div>
      <div class="card-footer">
        <div id="dt-buttons_for_farmsTable">
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Main page contents end here  -->
<!-- ########################## -->
<!-- ########################## -->
<!-- ########################## -->

<div id='sample_sheet'></div>
<div id="comm_sample_sheet"></div>

<div id="farm-edit-template">
  <h4>Add / Edit <?php echo $farm; ?> details</h4>
  <div data-editor-template="farmers.project"></div>
  <div data-editor-template="farmers.community_id"></div>
  <div class="input-group mb-3">
      <span class="input-group-addon" id="farmer_code_prefix"></span>
      <input type="text" class="form-control" id="farmer_code_entered" aria-describedby="farmer_code_prefix">
    </div>
  <div data-editor-template="farmers.id"></div>
  <div data-editor-template="farmers.farmer_name"></div>
</div>

<div id="community-edit-template">
  <h4>Add / Edit <?php echo $community; ?> details</h4>
  <div data-editor-template="communities.project"></div>
  <div data-editor-template="communities.district_id"></div>
  <div class="input-group mb-3">
      <span class="input-group-addon" id="community_code_prefix"></span>
      <input type="text" class="form-control" id="community_code_entered" aria-describedby="community_code_prefix">
    </div>
  <div data-editor-template="communities.id"></div>
  <div data-editor-template="communities.community_label"></div>

</div>

<div id="districts-edit-template">
  <h4>Add / Edit <?php echo $district; ?> details
    <div data-editor-template="districts.country_id"></div>
    <div data-editor-template="districts.project"></div>
    <label for="basic-url">District Code: The prefix is determined by the chosen project</label><!-- label> -->
    <div class="input-group mb-3">
      <span class="input-group-addon" id="district_code_prefix"></span>
      <input type="text" class="form-control" id="district_code_entered" aria-describedby="district_code_prefix">
    </div>
    <div data-editor-template="districts.district_code"></div>

    <div data-editor-template="districts.district_label"></div>

</div>

      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

  </div><!-- #primary -->

<?php get_footer(); ?>
