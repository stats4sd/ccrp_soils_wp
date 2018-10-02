<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

define('DB_NAME', '');

/** MySQL database username */
define('DB_USER', '');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', '');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '');
define('SECURE_AUTH_KEY',  '');
define('LOGGED_IN_KEY',    '');
define('NONCE_KEY',        '');
define('AUTH_SALT',        '');
define('SECURE_AUTH_SALT', '');
define('LOGGED_IN_SALT',   '');
define('NONCE_SALT',       '');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * Composer's autoloader, used throughout the project
 */
require_once( __DIR__ . '/content/vendor/autoload.php' );


define( 'SITE_NAME', '');
// define( 'NODE_URL', 'http://localhost:3004');

if(SITE_NAME === ''){
  define('SL','/');
}
else {
  define('SL','/');
}

/**
 * Dynamic home and site URLs to handle both websites in all environments.
 */
define( 'WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] . SL . SITE_NAME.'/wp' );
define( 'WP_HOME', 'http://' . $_SERVER['HTTP_HOST']. SL . SITE_NAME);


/**
 * Custom plugins directory shared across both websites.
 */

define( 'WP_PLUGIN_DIR', dirname(__FILE__) . '/content/plugins' );
define( 'WP_PLUGIN_URL', 'http://' . $_SERVER['HTTP_HOST'] . SL .SITE_NAME . '/content/plugins' );

define( 'WP_CONTENT_DIR', dirname(__FILE__) . '/content' );
define( 'WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . SL . SITE_NAME . '/content' );


# Disables the embebeded editor
define( 'DISALLOW_FILE_EDIT', true);
define( 'DISALLOW_FILE_MODS', true);
define( 'RELOCATE', true);
# Disables automatic update functions
define( 'AUTOMATIC_UPDATER_DISABLED', false );
define( 'WP_AUTO_UPDATE_CORE', false );
/**
 * SSL
 * You might want to force SSL on the admin page
 */
# define( 'FORCE_SSL_LOGIN', true );
# define( 'FORCE_SSL_ADMIN', false );
/**
 * Debug Flags
 * Use them under development environments
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);
define('SAVEQUERIES', false);


/* KEEP OUT BELOW */
/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');