<?php
/*
Plugin Name: Auto Excerpt from Content
Description: Automatically generates excerpts for draft posts from the content.
Version: 1.0
Author: Steve Hodgkiss
Author URI: https://stevehodgkiss.net
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Automatically generate excerpts for draft posts from the content.
 * Extracts 20 words from the post content and appends a "Read more" link.
 * Runs only once, when the plugin is activated and then deactivates itself.
 * 
 * Installation:
 * 1. clone the repo using git clone https://github.com/stephenhodgkiss/wordpress-plugin-excerpt-from-content.git OR download the zip file.
 * 2. If cloned, compress the auto-excerpt-from-content folder into a zip file.
 * 3. Upload the plugin folder to the /wp-content/plugins/ directory OR upload the zip file via the WordPress admin panel under PLUGINS > Add New > Upload Plugin.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress.
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function auto_generate_excerpt_for_drafts() {
    // run only one time
    if (get_option('auto_generate_excerpt_for_drafts') == 'done') {
        return;
    }

    $args = array(
        'post_status' => 'draft',
        'posts_per_page' => -1, // Retrieve all draft posts.
    );

    $draft_posts = get_posts($args);

    foreach ($draft_posts as $post) {
        $content = $post->post_content;
        $content = strip_tags($content); // Remove HTML tags.
        $excerpt = wp_trim_words($content, 20); // Adjust the word count as needed.
        // Get the post permalink.
        $permalink = get_permalink($post->ID);

        // Create the "Read more" link.
        $read_more_link = '<a href="' . esc_url($permalink) . '">Read more</a>';

        // Append the "Read more" link to the excerpt.
        $excerpt_with_link = $excerpt . ' ' . $read_more_link;

        // Update the post's excerpt.
        $post->post_excerpt = $excerpt_with_link;
        wp_update_post($post);
    }

    // Update the option to prevent the function from running again.
    update_option('auto_generate_excerpt_for_drafts', 'done');

    // Deactivate the plugin.
    deactivate_plugins(plugin_basename(__FILE__));

}

// Hook the function to run when WordPress initializes.
add_action('init', 'auto_generate_excerpt_for_drafts');
