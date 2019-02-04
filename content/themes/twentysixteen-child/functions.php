<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_style('bootstrap4', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');

}


// function replace_text($text) {
//   $text = str_replace('groups', 'projects', $text);
//   $text = str_replace('Groups', 'Projects', $text);
//   return $text;
// }
// add_filter('the_content', 'replace_text');

//add_action('admin_head','datatables');
//add_action('wp_head','datatables');
//



?>
