<?php
/**
 * WordPress Configuration File
 * Seyahat Acentesi - Travel Marketplace Platform
 */

// ** Database settings - You will need to update these for your environment ** //
/** Database Charset */
define( 'DB_CHARSET', 'utf8mb4' );

/** Database Collate type */
define( 'DB_COLLATE', '' );

/** Authentication Unique Keys and Salts */
define( 'AUTH_KEY',         'seyahat-auth-key-unique-string' );
define( 'SECURE_AUTH_KEY',  'seyahat-secure-auth-key-unique' );
define( 'LOGGED_IN_KEY',    'seyahat-logged-in-key-unique' );
define( 'NONCE_KEY',        'seyahat-nonce-key-unique' );
define( 'AUTH_SALT',        'seyahat-auth-salt-unique' );
define( 'SECURE_AUTH_SALT', 'seyahat-secure-auth-salt' );
define( 'LOGGED_IN_SALT',   'seyahat-logged-in-salt' );
define( 'NONCE_SALT',       'seyahat-nonce-salt-unique' );

/** WordPress Database Table prefix */
$table_prefix = 'sy_';

/** WordPress debugging mode */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

/** WordPress memory limit */
define( 'WP_MEMORY_LIMIT', '512M' );

/** SSL and security settings */
define( 'FORCE_SSL_ADMIN', true );
define( 'DISALLOW_FILE_EDIT', true );

/** Multisite configuration (for future expansion) */
// define( 'WP_ALLOW_MULTISITE', true );

/** Custom content directory */
define( 'WP_CONTENT_DIR', dirname(__FILE__) . '/wp-content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content' );

/** Automatic updates */
define( 'WP_AUTO_UPDATE_CORE', true );

/** File permissions */
define( 'FS_CHMOD_DIR', (0755 & ~ umask()) );
define( 'FS_CHMOD_FILE', (0644 & ~ umask()) );

/** Cache settings */
define( 'WP_CACHE', true );
define( 'WPCACHEHOME', dirname(__FILE__) . '/wp-content/plugins/wp-super-cache/' );

/** API settings for integrations */
define( 'SEYAHAT_API_VERSION', '1.0' );
define( 'SEYAHAT_REST_API_ENABLED', true );

/** Multi-language support */
define( 'WPLANG', 'tr_TR' );

/** Absolute path to the WordPress directory */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files */
require_once ABSPATH . 'wp-settings.php';