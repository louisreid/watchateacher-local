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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'mysql');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'zx(|$S1~7M.&;]^DY43Cj7 ;EIt(6p YzL37}93%<H8F|*)=L~iMlszG@N)VQf5t');
define('SECURE_AUTH_KEY',  'LakVJOD7wkCP&q.$H<*O!;Hg^$h6rLL^nJ6cMNMv*lK[uFr-$[6B)0d8E]DuQ;rY');
define('LOGGED_IN_KEY',    '!n~cF{GAx]q`Op-o~@H2!X6}-5Et13fuqcA_m0Fe9i6%s)j;8,#; $&cW3`8(* p');
define('NONCE_KEY',        'SCZi(*}em#&m@}IlDeODC4j8UFG{h#C|ED*}0Vx;IDEh8`Od&w W!N`}yx4rsg}:');
define('AUTH_SALT',        'Y;dNLkjbyW}vb^y)*&CVEH4t5L8,WC|](sQ!1MMPaJ1]vZjQEzmP0bQB4AFwrloc');
define('SECURE_AUTH_SALT', 'jmASaz}OZ)]9gB6k0K`4jrc:=$:`]n!;c1O4:&&g6P+rHA|9KDC.yPC B>.e~Jl ');
define('LOGGED_IN_SALT',   '.=)<p-d?#5fOiwPnMPm~vTY`-:?1pHX)mvT02;pTd_h%E6wA]AO9o~q;w3g)Zp%m');
define('NONCE_SALT',       'R~KTozdE(jPAj(.*0snG(x/ig&> o!{vPM/(?]uL&GT#*bKS_3!+mmLkHFTx&XC|');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'watchateacher_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
