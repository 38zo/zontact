<?php
/**
 * Settings registration.
 *
 * @package ThirtyEightZo\Zontact\Admin\Settings
 */

namespace ThirtyEightZo\Zontact\Admin\Settings;

use ThirtyEightZo\Zontact\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Class Register
 *
 * Handles WordPress Settings API registration.
 */
class Register {

	/**
	 * Register settings with WordPress Settings API.
	 *
	 * @return void
	 */
	public static function register(): void {
		register_setting(
			'zontact_settings',
			'zontact_options',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( Main::class, 'sanitize' ),
				'default'           => Options::defaults(),
			)
		);

		/**
		 * Fires after Zontact settings are registered.
		 * Allows extensions to register additional settings.
		 */
		do_action( 'zontact_register_settings' );
	}
}

