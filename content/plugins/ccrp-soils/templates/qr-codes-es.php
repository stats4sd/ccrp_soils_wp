<?php
/**
 * Template Name: qr-codes-es
 *
 * This is the template that displays full width page without sidebar
 *
 * @package sparkling-child
 */

get_header(); ?>

  <div id="primary" class="content-area">

    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post(); ?>


<h1>Generación de hoja de códigos QR</h1>
<p>Use esta página para generar códigos QR que no estén ligados a una localidad o productor específicos. Este es usado si usted está configurando rápidamente, o si están usando las herramientas para ayudar con análisis de muestras y están administrando sus datos en otro lugar. </p>
<p>Presione el botón abajo para generar una hoja de 6 códigos de muestra para imprimir. Cada código será único en el sistema. Simplemente genere e imprima tantas hojas como necesite para su trabajo.</p>
<br/><br/><br/>
<button onclick="getCodes(6)">Generación de hojas de código para impresión</button>
<div id='sample_sheet'></div>

        <?php get_template_part( 'content', 'page' ); ?>



      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->

  </div><!-- #primary -->

<?php get_footer(); ?>
