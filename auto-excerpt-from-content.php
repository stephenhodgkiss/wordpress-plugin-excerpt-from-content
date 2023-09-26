<?php
/*
Plugin Name: Auto Excerpt from Content
Description: Automatically generates excerpts for draft posts from the content.
Version: 1.0
Author: Steve Hodgkiss
Author URI: https://stevehodgkiss.net
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Automatically generate excerpts for draft posts from the content.
 * Extracts 30 words from the post content and appends a "Read more" link.
 * Runs only once, when the plugin is activated and then deactivates itself.
 * 
 * Installation:
 * 1. clone the repo using git clone https://github.com/stephenhodgkiss/wordpress-plugin-excerpt-from-content.git OR download the zip file.
 * 2. If cloned, compress the auto-excerpt-from-content folder into a zip file.
 * 3. Upload the plugin folder to the /wp-content/plugins/ directory OR upload the zip file via the WordPress admin panel under PLUGINS > Add New > Upload Plugin.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress.
 */

function auto_generate_excerpt_for_drafts()
{

    // is the plugin active?
    if (is_plugin_active(plugin_basename(__FILE__))) {

        // set $args that contains draft posts with the category tag_ID=135
        $args = array(
            'numberposts' => 100, // adjust as necessary due to possible timeouts
            'category' => 135,
            'post_status' => 'draft',
            'offset' => 0
        );

        $draft_posts = get_posts($args);

        $posts_updated = 0;

        foreach ($draft_posts as $post) {
            $post_id = $post->ID;
            $content = $post->post_content;
            $content = strip_tags($content); // Remove HTML tags.
            $excerpt = wp_trim_words($content, 30); // Adjust the word count as needed.

            $post_title = get_the_title($post_id);

            // get the slug or if does not exist yet, Generate a slug.
            $post_slug = $post->post_name;
            if (!$post_slug) {
                $post_slug = sanitize_title($post_title);
            }

            // get the protocol and domain name of the site
            $site_url = get_site_url();
            // combine the site url, the slug to create the full url
            $permalink = $site_url . '/' . $post_slug;

            // Create the "Read more" link.
            $read_more_link = '<a href="' . esc_url($permalink) . '">Read more</a>';

            // Append the "Read more" link to the excerpt.
            $excerpt_with_link = $excerpt . ' ' . $read_more_link;

            // Update the post's excerpt.
            $post->post_excerpt = $excerpt_with_link;
            wp_update_post($post);
            echo '<div class="updated"><p>' . esc_html($excerpt_with_link) . '</p></div>';

            $posts_updated++;
        }

        // Optionally, you can add a message to confirm the update
        $message = $posts_updated . ' posts updated. The plugin has now deactivated itself.';
        echo '<div class="updated"><p>' . esc_html($message) . '</p></div>';

        // Deactivate the plugin.
        deactivate_plugins(plugin_basename(__FILE__));
    }
}

// Hook the function to run when WordPress initializes.
add_action('init', 'auto_generate_excerpt_for_drafts');
