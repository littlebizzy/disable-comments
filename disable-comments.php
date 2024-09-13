/*
Plugin Name: Disable Comments
Plugin URI: https://www.littlebizzy.com/plugins/disable-comments
Description: Disables comments without database
Version: 1.0.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
GitHub Plugin URI: https://github.com/littlebizzy/disable-comments
Primary Branch: master
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Disable WordPress.org updates for this plugin
add_filter( 'gu_override_dot_org', function( $overrides ) {
    $overrides[] = 'disable-comments/disable-comments.php';
    return $overrides;
});

// Disable support for comments and trackbacks in post types
function disable_comments_post_types_support() {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'disable_comments_post_types_support');

// Close comments on the front-end
function disable_comments_status() {
    return false;
}
add_filter('comments_open', 'disable_comments_status', 20, 2);
add_filter('pings_open', 'disable_comments_status', 20, 2);

// Hide existing comments
function disable_existing_comments($comments) {
    return array();
}
add_filter('comments_array', 'disable_existing_comments', 10, 2);

// Remove comments page in menu
function disable_comments_admin_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'disable_comments_admin_menu');

// Redirect any user trying to access comments page
function disable_comments_admin_menu_redirect() {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function disable_comments_dashboard() {
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'disable_comments_dashboard');

// Remove comments links from admin bar
function disable_comments_admin_bar() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
}
add_action('init', 'disable_comments_admin_bar');

// Remove X-Pingback header to prevent trackbacks
add_filter('wp_headers', function($headers) {
    unset($headers['X-Pingback']);
    return $headers;
});

// Disable comment feed
function disable_comment_feed() {
    if (is_comment_feed()) {
        wp_die(__('Comments are closed.', 'disable-comments'));
    }
}
add_action('template_redirect', 'disable_comment_feed');

// Remove comment reply script
function disable_comment_reply_script() {
    wp_deregister_script('comment-reply');
}
add_action('wp_enqueue_scripts', 'disable_comment_reply_script');

// Remove discussion settings fields (from Simply Disable Comments)
function disable_discussion_settings_fields() {
    add_filter('pre_option_default_ping_status', '__return_zero');
    add_filter('pre_option_default_comment_status', '__return_zero');
    add_action('admin_menu', function() {
        remove_meta_box('commentstatusdiv', 'post', 'normal');
        remove_meta_box('commentstatusdiv', 'page', 'normal');
        remove_meta_box('trackbacksdiv', 'post', 'normal');
    });
}
add_action('admin_init', 'disable_discussion_settings_fields');

// Remove comment feed links from the header (from Simply Disable Comments)
function disable_comment_feed_links() {
    remove_action('wp_head', 'feed_links_extra', 3);
}
add_action('wp_head', 'disable_comment_feed_links', 1);

// Ref: ChatGPT
// Ref: https://github.com/HandyPlugins/simply-disable-comments/
