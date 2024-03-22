<?php

class CustomVoting
{
    public function __construct()
    {
        /* Frontend voting hooks */
        add_action('wp_enqueue_scripts', array($this, 'enqueue_voting_scripts'));
        add_action('wp_ajax_handle_voting_ajax', array($this, 'handle_voting_ajax'));
        add_action('wp_ajax_nopriv_handle_voting_ajax', array($this, 'handle_voting_ajax')); /* nonce for security */
        add_action('the_content', array($this, 'add_template_part_after_content'));

        /* Meta Box hooks */

        add_action('add_meta_boxes', array($this, 'custom_voting_results_meta_box'));
        add_action('save_post', array($this, 'custom_voting_results_save_meta_box_data'));
    }

    /* Enqueue ajax script */
    public function enqueue_voting_scripts()
    {
        wp_enqueue_script('voting-ajax', plugins_url('../assets/js/voting-ajax.js', __FILE__), array('jquery'), null, true);
        wp_localize_script(
            'voting-ajax',
            'voting_ajax_object',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('voting_nonce')
            )
        );
        /* Checked nonce is set or not to prevent any security measure. */
    }

    /* Callback function for AJAX request in JS. */
    function handle_voting_ajax()
    {
        $response = array('success' => false, 'message' => '');

        if (isset ($_POST['post_id']) && isset ($_POST['vote_option'])) {
            $post_id = intval($_POST['post_id']);
            $vote_option = sanitize_text_field($_POST['vote_option']);
            $user_ip = $_SERVER['REMOTE_ADDR'];

            // Check if the user has already voted for this post
            $has_voted = get_post_meta($post_id, 'voted_' . $user_ip, true);
            if ($has_voted) {
                $response['message'] = 'You have already voted for this post.';
            } else {
                if ($vote_option === 'yes' || $vote_option === 'no') {
                    $yes_votes = intval(get_post_meta($post_id, 'yes_votes', true));
                    $no_votes = intval(get_post_meta($post_id, 'no_votes', true));

                    if ($vote_option === 'yes') {
                        $yes_votes++;
                    } else {
                        $no_votes++;
                    }

                    update_post_meta($post_id, 'yes_votes', $yes_votes);
                    update_post_meta($post_id, 'no_votes', $no_votes);

                    // Mark that the user has voted for this post
                    update_post_meta($post_id, 'voted_' . $user_ip, true);

                    $response['success'] = true;
                    $response['yes_votes'] = $yes_votes;
                    $response['no_votes'] = $no_votes;
                } else {
                    $response['message'] = 'Invalid vote option.';
                }
            }
        } else {
            $response['message'] = 'Invalid request.';
        }

        wp_send_json($response);
    }

    /* Load template file to every post */
    function add_template_part_after_content($content)
    {
        if (is_single()) {
            $template_part_path = plugin_dir_path(__FILE__) . '/template-parts/voting-widget.php';
            if (file_exists($template_part_path)) {
                ob_start();
                include $template_part_path;
                $template_part_content = ob_get_clean();
                $content .= $template_part_content;
            }
        }
        return $content;
    }

    // Add meta box to display voting results
    public function custom_voting_results_meta_box()
    {
        add_meta_box(
            'custom-voting-results-meta-box',
            'Voting Results',
            array($this, 'custom_voting_results_meta_box_callback'),
            'post', // Post type
            'normal', // Context
            'high' // Priority
        );
    }

    public function custom_voting_results_save_meta_box_data($post_id)
    {
        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update or add meta data
        if (isset ($_POST['yes_votes'])) {
            update_post_meta($post_id, 'yes_votes', intval($_POST['yes_votes']));
        }
        if (isset ($_POST['no_votes'])) {
            update_post_meta($post_id, 'no_votes', intval($_POST['no_votes']));
        }
    }


    // Save meta box data
    public function custom_voting_results_meta_box_callback($post)
    {
        // Retrieve post ID
        $post_id = $post->ID;

        // Retrieve voting results data
        $yes_votes = intval(get_post_meta($post_id, 'yes_votes', true));
        $no_votes = intval(get_post_meta($post_id, 'no_votes', true));
        $total_votes = $yes_votes + $no_votes;

        // Calculate percentages
        $yes_percentage = $total_votes > 0 ? round(($yes_votes / $total_votes) * 100) : 0;
        $no_percentage = $total_votes > 0 ? round(($no_votes / $total_votes) * 100) : 0;

        // Display voting results with percentages
        echo '<p><strong>Yes Votes:</strong> ' . $yes_votes . ' (' . $yes_percentage . '%)</p>';
        echo '<p><strong>No Votes:</strong> ' . $no_votes . ' (' . $no_percentage . '%)</p>';
        echo '<p><strong>Total Votes:</strong> ' . $total_votes . '</p>';
    }
}

new CustomVoting();
