<?php
/*
Plugin Name: Disable Comments
Plugin URI: https://www.littlebizzy.com/plugins/disable-comments
Description: Disables comments without database
Version: 1.0.2
Requires PHP: 7.0
Author: LittleBizzy
Author URI: https://www.littlebizzy.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
GitHub Plugin URI: littlebizzy/disable-comments
Primary Branch: master
*/

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// disable wordpress.org updates for this plugin
add_filter( 'gu_override_dot_org', function( $overrides ) {
    $overrides[] = 'disable-comments/disable-comments.php';
    return $overrides;
}, 999 );

// Disable support for comments and trackbacks in all post types
function disable_comments_post_types_support() {
    $post_types = get_post_types();

    foreach ( $post_types as $post_type ) {
        remove_post_type_support( $post_type, 'comments' );
        remove_post_type_support( $post_type, 'trackbacks' );
    }
}
add_action( 'admin_init', 'disable_comments_post_types_support' );

// Close comments on the front-end
function disable_comments_status() {
    return false;
}
add_filter( 'comments_open', 'disable_comments_status', 20, 2 );
add_filter( 'pings_open', 'disable_comments_status', 20, 2 );

// Hide existing comments
function disable_existing_comments( $comments ) {
    return array();
}
add_filter( 'comments_array', 'disable_existing_comments', 20, 2 );

// Remove comments page from menu and redirect non-admin users trying to access it
function disable_comments_admin_menu() {
    // If the user is neither an admin nor a super admin
    if ( ! current_user_can( 'manage_options' ) ) {
        // Remove comments page from menu
        remove_menu_page( 'edit-comments.php' );

        // Redirect non-admin users who try to access the comments page directly
        global $pagenow;
        if ( $pagenow === 'edit-comments.php' ) {
            wp_safe_redirect( admin_url() );
            exit;
        }
    }
}
add_action( 'admin_menu', 'disable_comments_admin_menu' );
add_action( 'admin_init', 'disable_comments_admin_menu' );

// Remove the Recent Comments section from the Activity widget for non-admin users
function disable_recent_comments_from_activity_widget() {
    if ( ! current_user_can( 'manage_options' ) ) {
        // Use a filter to prevent comments from showing in the Activity widget
        add_filter( 'the_comments', '__return_empty_array', 10, 1 );
    }
}
add_action( 'load-index.php', 'disable_recent_comments_from_activity_widget' );

// Remove comments links from admin bar
function disable_comments_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'disable_comments_admin_bar', 999 );

// Remove X-Pingback header to prevent trackbacks
function disable_pingback_header( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}
add_filter( 'wp_headers', 'disable_pingback_header' );

// Disable comment feed
function disable_comment_feed() {
    if ( is_comment_feed() ) {
        wp_die( __( 'Comments feed is disabled on this site.', 'disable-comments' ), '', array( 'response' => 403 ) );
    }
}
add_action( 'template_redirect', 'disable_comment_feed' );

// Remove comment reply script
function disable_comment_reply_script() {
    wp_deregister_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'disable_comment_reply_script' );

// Remove discussion settings fields
function disable_discussion_settings_fields() {
    add_filter( 'pre_option_default_ping_status', '__return_zero' );
    add_filter( 'pre_option_default_comment_status', '__return_zero' );
}
add_action( 'admin_init', 'disable_discussion_settings_fields' );

// Remove meta boxes from all post types
function disable_meta_boxes() {
    $post_types = get_post_types();

    foreach ( $post_types as $post_type ) {
        remove_meta_box( 'commentstatusdiv', $post_type, 'normal' ); // Removes the "Allow comments" checkbox
        remove_meta_box( 'trackbacksdiv', $post_type, 'normal' );    // Removes the "Allow trackbacks" checkbox
        remove_meta_box( 'commentsdiv', $post_type, 'normal' );      // Removes the "Comments" section from the editor
    }
}
add_action( 'admin_menu', 'disable_meta_boxes' );

// Remove comment feed links from the header
function disable_comment_feed_links() {
    remove_action( 'wp_head', 'feed_links_extra', 3 );
}
add_action( 'wp_head', 'disable_comment_feed_links', 1 );

// Ref: ChatGPT
// Ref: https://github.com/HandyPlugins/simply-disable-comments/
// Ref: https://wordpress.stackexchange.com/questions/213712/admin-dashboard-unset-recent-comments
