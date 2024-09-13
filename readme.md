# Disable Comments

Disables comments without database

## Changelog

### 1.0.0
- initial release
- follows WordPress coding standards
- no database involvement
- disables all comments, pingbacks, trackbacks on all pages and post types
- prevents certain comment assets (JS etc) from loading
- disables comments drop-down in Admin bar
- disables comment meta boxes and discussion settings
- disables `/wp-admin/edit-comments.php` for non-Admin and non-Super Admin (Multisite) users
- disables Recent Comments WP Admin widget for non-Admin users
- supports PHP 7.0 to PHP 8.3
- supports Multisite
