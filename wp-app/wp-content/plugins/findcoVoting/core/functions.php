<?php

// Add a filter to the_content hook
add_filter('the_content', 'add_template_part_after_content');

function add_template_part_after_content($content) {
    if (is_single()) {
        $template_part_path = plugin_dir_path( __FILE__ ) . '/template-parts/voting-widget.php';
        if (file_exists($template_part_path)) {
            ob_start();
            include $template_part_path; 
            $template_part_content = ob_get_clean();
            $content .= $template_part_content;
        }
    }
    return $content;
}


function custom_enqueue_scripts() {
    wp_enqueue_style('voting-style', plugins_url('../assets/css/voting-style.css', __FILE__));
    wp_enqueue_script('voting-script', plugins_url('../assets/js/voting-script.js', __FILE__), array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');


?>