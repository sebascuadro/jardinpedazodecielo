<?php
define( 'WP_CACHE', true );
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
define( 'DB_NAME', 'u888426794_L2Zmo' );

/** MySQL database username */
define( 'DB_USER', 'u888426794_aISEU' );

/** MySQL database password */
define( 'DB_PASSWORD', 'vcI7P5fsbi' );

/** MySQL hostname */
define( 'DB_HOST', 'mysql' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'w*uR-dRtu{X(vh_:W86fhFo=]T4lUt;$<eP8Q6>jVEfMEPh.2d:`_JA$r_xESRi1' );
define( 'SECURE_AUTH_KEY',   '2#b/V6mSU:v2&6Q-|EuZ/D{nz8dC;xU@/0qihKSAHiF^*N=d^eIJc!BM!A3;E0G1' );
define( 'LOGGED_IN_KEY',     '([gIvN{M>yWtV<P5xXlFt7Ri~1Fv_hz oyO<SD|]u)oPW-ec/,09q:^#E%#Zr})R' );
define( 'NONCE_KEY',         '+5E|SKB%c}kP5mQb|g%o^A>c,1];B:z^0<^,ucO`2; q@=8yW4u`z`^77I~CJV_7' );
define( 'AUTH_SALT',         'wi3Ha*O__kv()dc+B!$}+=aV*Eb94O.u7.ey5wZcTNzv:DrYpeJ;7QgQjSg08lRI' );
define( 'SECURE_AUTH_SALT',  'bpMVf%,1icu#lpv$:>57o:Xn~ST_.q5]VA_]Y!?#RX4)XA=6qQ:ZY}zfx@bOao>%' );
define( 'LOGGED_IN_SALT',    '.^@8ADvK2Nw4W3cf1wUYfA|?z(VL{.)BbXraj/Ye&)#Qq6E$;8]|,t..!Q3mkr*p' );
define( 'NONCE_SALT',        'D%6QEF^oemV*KlmkXMGf!;|XKx>N-@o!so04%_2R,AjTdU-Pa^{#T?3GY3Juyot<' );
define( 'WP_CACHE_KEY_SALT', '4=yz@Lu^d{(]=jeA7<UEf>+N2Ie)^Um3*eL0?o^O4[Bszt+dZ-oo2fNR~;-|,}U(' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
