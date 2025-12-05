<?php
/**
 * Main settings coordinator.
 *
 * @package ThirtyEightZo\Zontact\Admin\Settings
 */

namespace ThirtyEightZo\Zontact\Admin\Settings;

use ThirtyEightZo\Zontact\Admin\Settings\Tabs\Main as MainTabs;
use ThirtyEightZo\Zontact\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Class Main
 *
 * Main coordinator for settings functionality.
 */
class Main {

	/**
	 * Get all settings with tabs and sections.
	 *
	 * @return array Settings array with 'tabs' and 'settings' keys.
	 */
	public static function get_settings(): array {
		return MainTabs::get_all();
	}

	/**
	 * Sanitize settings input.
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized settings.
	 */
	public static function sanitize( array $input ): array {
		return Options::sanitize( $input );
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public static function render_page(): void {
		View::render();
	}
}

