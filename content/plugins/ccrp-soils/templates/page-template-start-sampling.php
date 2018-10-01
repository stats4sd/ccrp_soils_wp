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
      <h2 class='mb-4'>The purpose of the platform</h2>
      <p>We built this platform to help CCRP projects manage the data they collect during soil sampling and analysis, following the protocols developed by the Cross-cutting Soils project team. The tools available here will help you follow the analysis guides and ensure the data you collect is well structured and easily linkable to the correct physical soil sample. If you choose to make use of the full platform, it will also allow you to download fully merged datasets, containing data from your initial sampling process and all the analyses you have conducted on that sample.</p>
          
      <h2 class='mb-4'>How the platform works</h2>
      <!-- <p class="alert alert-info">How and where to begin?</p> -->
      <p>There are three main components to this data platform: 
        <ul>
          <li>A set of ODK forms for use when collecting and analysing soil samples;</li>
          <li>A QR Code generation tool, to help you uniquely identify and manage your physical samples;</li>
          <li>A MySQL Database that helps organise data collected through the different forms.</li>
        </ul>
      </p>
      <p></p>
      <p>You can use these components in different ways, described below.</p>
      <h5 class='mb-2'>1. Just use the ODK forms</h5>
      <p>Using the downloadable resources requires no sign-up, simply download the XLS forms you wish to use from our downloads page.</p>
      <p>The different analysis protocols all require some level of calculation. The ODK forms we have developed have these calculations programmed in, to help save time and reduce the chance of errors in your results.</p>
      <p>The analysis forms require you to scan a QR or barcode at the start of the process, to identify your soil sample. We highly recommend using QR codes for uniquely identifying your physical samples, as they can be printed out and kept with the sample. See the QR code generation page for more infomation.</p>
      <p>To see all the forms available and choose ones to download, go to our downloads page.</p>

      <h5 class='mb-2'>2. Register to use the platform's database</h5>
      <p>Registering on the site will give you access to the full set of tools, including the ability to collect data via your own Kobotoolbox account, have it syncronised to the platform, and then automatically merged into downloadable datasets. </p>
      <p>To make full use of the platform, you need to have a kobotoolbox account for your project - if you don't have one, you can set one up easily at <a href="https://kf.kobotoolbox.org">https://kf.kobotoolbox.org</a>. This platform integrates with Kobotoolbox to let you collect soil sample and analysis data with the same tools you use for other data collection activities.</p>
      <p>The process of using the full platform is as follows:
        <ol>
          <li>Contact RMS or the Soils project team to get your project added to the platform.</li>
          <li>Once we have created your project account, you will be able to log in as project administrator. You can then invite any number of other people to join into your project group, so your full team can access the resources.</li>
          <li>Decide which kobotoolbox account you want to use and add the username to your project. The platform then shares the full set of ODK forms with you on Kobotolbox using the Kobo API.</li>
          <li>Finally, you go to the QR generation page and create and print a set of unique QR codes for your project team</li>
        </ol>

        Then, simply use the forms that are shared through Kobotoolbox. The forms are optimised for the Android app ODK-Collect. All the data you collect through the shared forms will be brought into the platform. Once you have collected some data, you can view the records directly through this site, and download a merged dataset containing data from all the forms.

      </p>




      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

    <?php get_sidebar( 'content-bottom' ); ?>

  </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
