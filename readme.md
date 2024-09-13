# Disable Comments

Disables comments without database

## Changelog

### 1.0.0
- initial release
- follows WordPress coding standards
- no database involvement
- disables all comments, pingbacks, trackbacks on all pages and post types
- closes comments and pings on frontend and hides existing comments
- disables `/wp-admin/edit-comments.php` for non-Admin and non-Super Admin (Multisite) users
- removes Recent Comments in Activity widget for non-Admin users
- removes the comments drop-down in Admin bar
- removes X-Pingback headers
- denies access to any RSS comment feeds
- prevents comment-reply JS script from loading
- disables comment related meta boxes on post edit screen
- removes comment feed links in wp_head
- supports PHP 7.0 to PHP 8.3
- supports Multisite
