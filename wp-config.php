<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', $_SERVER["DB_NAME"]);

/** MySQL database username */
define('DB_USER', $_SERVER["DB_USER"]);

/** MySQL database password */
define('DB_PASSWORD', $_SERVER["DB_PASS"]);

/** MySQL hostname */
define('DB_HOST', $_SERVER["DB_HOST"]);

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
define('AUTH_KEY',         '8iZ!e|pk(fv?sEL <C>Cc|YoJm]4D<osm@-dmD=.w.<L&AU>XE$V|!||vz$C^ Du');
define('SECURE_AUTH_KEY',  'ureUtr4u-L8^/pdbuG-^%vxtW6(Wk|$]yFu|FWkiaVscY.As4_J+x<DU8`M)W|XJ');
define('LOGGED_IN_KEY',    'gnch|N[W-tE46<]#MC2_IR=3C$3-?c}y7TDKb@|r3<]LY{PM&u2n@eOk8~?$x6#d');
define('NONCE_KEY',        ')Hrb-7(_+F=2a+D5Fou|~m_mc], rzOwdijb^(<Z3C@_WDUjYwuMgh?<G.ClUI{D');
define('AUTH_SALT',        ' mwh(PY<Sxw/:{j:D{6>2-yPB<+bNpIIe~Ja:*-a}lInkNo$sG<=je$ffx%N&n3N');
define('SECURE_AUTH_SALT', '1S-Z!2Bwd5n}}JlCWrZ^Ja&M2cjBA]w||+y`u-unG=n*Z4_ngK1OM[tLi)9On`v9');
define('LOGGED_IN_SALT',   'jq8-IjRTT}OQ>CFm*h41%ipz&#NC!xg2{86t56E7Tb`Vc>cEzzeqaBbCa6%~MoYv');
define('NONCE_SALT',       '<{o(-z3m Y&:AJy_sWC%6Pt}+^9[)dr)bs,p?ONFuAuqB,Q6|fO,l.+-OMmm:l%$');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
