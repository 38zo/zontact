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

/**
 * Initialize Freemius SDK.
 * Must be done after autoloader but before plugin bootstrap.
 */
if ( ! function_exists( 'zon_fs' ) ) {
    // Create a helper function for easy SDK access.
    function zon_fs() {
        global $zon_fs;

        if ( ! isset( $zon_fs ) ) {
            // Activate multisite network integration.
            if ( ! defined( 'WP_FS__PRODUCT_21526_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_21526_MULTISITE', true );
            }

            // Include Freemius SDK.
            // SDK is auto-loaded through Composer

            $zon_fs = fs_dynamic_init( array(
                'id'                  => '21526',
                'slug'                => 'zontact',
                'premium_slug'        => 'zontact-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_f70ecbf17445436c99f4684f2d694',
                'is_premium'          => true,
                'premium_suffix'      => 'Pro',
                // If your plugin is a serviceware, set this option to false.
                'has_premium_version' => true,
                'has_addons'          => false,
                'has_paid_plans'      => true,
                // Automatically removed in the free version. If you're not using the
                // auto-generated free version, delete this line before uploading to wp.org.
                'wp_org_gatekeeper'   => 'OA7#BoRiBNqdf52FvzEf!!074aRLPs8fspif$7K1#4u4Csys1fQlCecVcUTOs2mcpeVHi#C2j9d09fOTvbC0HloPT7fFee5WdS3G',
                'trial'               => array(
                    'days'               => 7,
                    'is_require_payment' => false,
                ),
                'menu'                => array(
                    'slug'           => 'zontact',
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $zon_fs;
    }

    // Init Freemius.
    zon_fs();
    // Signal that SDK was initiated.
    do_action( 'zon_fs_loaded' );
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