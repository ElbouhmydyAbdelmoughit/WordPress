<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'shop' );

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
define( 'AUTH_KEY',         ',0E;hhf:TCJH0d,Vzp9XCqj;h@X!5i%B64n*JJxln,d~{}j+Wa/oJ5R0.{I{Ia&L' );
define( 'SECURE_AUTH_KEY',  'R=*bY_kB:$-L}PpeoXj%TM_9$B8<BFE*2h.*I2!KlIX5@XH<2<u#vw/2Zw^or[/B' );
define( 'LOGGED_IN_KEY',    'zQXd[s/OVNQx[gQ3SxC-M Ze{(i.[9)>:7rs|.O=R@:t@`1&1qm6=ZJBmnd8zab9' );
define( 'NONCE_KEY',        'L`xOmp4%9xS7Xb%nrh]{KN|M6nu[rCLo`1XfKP-.Y>VctWPEpn_DWw6~avL[cWbo' );
define( 'AUTH_SALT',        'h.,k?~.Q:Fg->iLy~kTA8/-a a37fAg1JJu6qiNjg1MMpB@fpPX1<mbAwjd`E)z7' );
define( 'SECURE_AUTH_SALT', 'pwl0,)mkD^9XxZB0Jd^UdZ.R=MiZ+G>BK2UPAUz]<pos,N`1@VoIA5S_Y&Ak7(S@' );
define( 'LOGGED_IN_SALT',   '`,(-@p#OVxxDb~Ty%vU-C<CnDim4,|YF:>|h m=5%T5{GDl^4Ftb,HD&SVI?+{E}' );
define( 'NONCE_SALT',       '7f5,TUpfk{X0!S2nJ4PUnGHm$Yi7 RpNj;(eT(hJDn9ZH/cJ/dz(TH-qP)ds[K{U' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
