<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ayuntamiento_web_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'WRf}~eDwOVR~IKX2/iKLD;irQ8lX[0>ybRDEfpd0!%z8PilAa0eoOac 0SHFY{<;' );
define( 'SECURE_AUTH_KEY',  '`)7ZOPyBUR#B4PE*e2b2`q6=tiN^tt,WGkVbtzT~8#Nc%w{kyI<F+^PQ>>8YxGR{' );
define( 'LOGGED_IN_KEY',    'kK2d_h/_Pen{p9o%cgzaRgSD*B%`k8NOr,u:D`V8c3$!Jrgxcg~@eb s#:/80Aj,' );
define( 'NONCE_KEY',        ')c11dK.94)wNDh}zxz[>:Q/Q_U1.O@oh[3CSK?h7Y3F!kR3mb))ZB=F=MVN1z<=_' );
define( 'AUTH_SALT',        'xtL.k~GB*_3},fUpk&yCiW^uWsoRw1+jZcF5=xc+]{_IgY/7GN?jAh7BHu#6%6~*' );
define( 'SECURE_AUTH_SALT', '3.uS<ht]Y*kLq.L|}-bv2ok-S^;ul&}@A4U#(=]t.P}{B%?~;iXh ?<z8Sr4x^A{' );
define( 'LOGGED_IN_SALT',   '>=hkJO^+Ca2Giy2u$`s)Z2]emks9oxu5_2+Tj%`s|2V(zKf/a_<L?$]qmm:q~/+=' );
define( 'NONCE_SALT',       'J17L1dUD)?22ONeiEV^L>LFn3Yy#I;f~G!uc[$=|:7u3&w^<?G3f+,4w+HhbQOxv' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define('WP_HOME', 'http://localhost/ayuntamiento_web');
define('WP_SITEURL', 'http://localhost/ayuntamiento_web');


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
