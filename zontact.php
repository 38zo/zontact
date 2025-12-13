<?php
/**
 * Plugin Name:       Zontact
 * Plugin URI:        https://github.com/38zo/zontact
 * Description:       One button, one form, zero hassle. Floating contact button opens an accessible modal with a contact form.
 * Version:           1.1.2
 * Author:            38zo
 * Author URI:        https://github.com/38zo
 * Text Domain:       zontact
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

define( 'ZONTACT_FILE', __FILE__ );
define( 'ZONTACT_PATH', plugin_dir_path( ZONTACT_FILE ) );
define( 'ZONTACT_URL', plugin_dir_url( ZONTACT_FILE ) );

/**
 * Autoload dependencies.
 */
$autoload = ZONTACT_PATH . '/vendor/autoload.php';
if ( file_exists( $autoload ) ) {
    require_once $autoload;
}

/**
 * Include necessary files.
 */
$functions = ZONTACT_PATH . '/includes/functions.php';
if ( file_exists( $functions ) ) {
	require_once $functions;
}

if ( ! function_exists( 'zon_fs' ) ) {

    function zon_fs() {
        global $zon_fs;

        if ( isset( $zon_fs ) ) {
            return $zon_fs;
        }

        require_once ZONTACT_PATH . '/vendor/freemius/start.php';

        $zon_fs = fs_dynamic_init( array(
            'id'                  => '21526',
            'slug'                => 'zontact',
            'premium_slug'        => 'zontact-pro',
            'type'                => 'plugin',
            'public_key'          => 'pk_f70ecbf17445436c99f4684f2d694',
            'is_premium'          => true,
			'premium_suffix'      => 'Pro',
            'has_premium_version' => true,
            'has_paid_plans'      => true,
			'trial'               => array(
                    'days'               => 7,
                    'is_require_payment' => false,
            ),
            'has_addons'          => false,

            'menu' => array(
                'slug'       => 'zontact',
                'capability' => 'manage_options',
            ),
        ) );

		// Init Freemius.
		zon_fs();

        do_action( 'zon_fs_loaded' );

        return $zon_fs;
    }

    add_action( 'plugins_loaded', 'zon_fs' );
}

/**
 * Bootstrap the plugin.
 */
add_action( 'plugins_loaded', function () {
	if ( class_exists( \ThirtyEightZo\Zontact\Plugin::class ) ) {
		\ThirtyEightZo\Zontact\Plugin::instance();
		/**
		 * Fires after Zontact has been bootstrapped.
		 *
		 * Allows third-parties to hook once the plugin is initialized.
		 *
		 * @since 1.0.0
		 */
		do_action( 'Zontact_bootstrapped' );
	}
});
