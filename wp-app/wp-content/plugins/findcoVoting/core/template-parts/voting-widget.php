<section id="voting">
    <div class="container">
        <?php
        $user_ip = $_SERVER['REMOTE_ADDR']; /* Get current user IP */
        $post_id = get_the_ID(); /* Get post id */
        $yes_votes = intval(get_post_meta($post_id, 'yes_votes', true));
        $no_votes = intval(get_post_meta($post_id, 'no_votes', true));
        $total_votes = $yes_votes + $no_votes;

        // Check if the user has already voted
        $has_voted = get_post_meta($post_id, 'voted_' . $user_ip, true);
        if ($has_voted) {
            // Calculate percentages
            $like_percentage = $total_votes > 0 ? round(($yes_votes / $total_votes) * 100) : 0;
            $dislike_percentage = 100 - $like_percentage;
            ?>
            <span>Thank you for your feedback.</span>
            <div class="buttons">
                <a class="disabled vote-button <?= $yes_votes > $no_votes ? 'button-selected' : '' ?>" disabled>
                    <?= $like_percentage ?>%
                </a>
                <a class="disabled vote-button <?= $no_votes > $yes_votes ? 'button-selected' : '' ?>" disabled>
                    <?= $dislike_percentage ?>%
                </a>
            </div>
            <?php
        } else {
            ?>
            <span>Was this Article Helpful?</span>
            <div class="buttons">
                <a class="vote-button" data-post-id="<?= $post_id ?>" data-vote-option="yes"
                    id="yes-votes-<?= $post_id ?>">YES</a>
                <a class="vote-button" data-post-id="<?= $post_id ?>" data-vote-option="no"
                    id="no-votes-<?= $post_id ?>">NO</a>
            </div>
            <?php
        }
        ?>
    </div>
</section>