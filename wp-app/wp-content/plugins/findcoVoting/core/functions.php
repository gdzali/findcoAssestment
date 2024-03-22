<?php

/* Enqueue css and js files */
function custom_enqueue_scripts() {
    wp_enqueue_style('voting-style', plugins_url('../assets/css/voting-style.css', __FILE__));
    wp_enqueue_style('voting-responsive', plugins_url('../assets/css/voting-responsive.css', __FILE__)); /* Responsive file separated to write clean code. */
    wp_enqueue_script('voting-script', plugins_url('../assets/js/voting-script.js', __FILE__), array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');


?>