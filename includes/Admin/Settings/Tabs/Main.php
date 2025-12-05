<?php
/**
 * Settings tabs and definitions.
 *
 * @package ThirtyEightZo\Zontact\Admin\Settings
 */

namespace ThirtyEightZo\Zontact\Admin\Settings\Tabs;

defined( 'ABSPATH' ) || exit;

/**
 * Class Main
 *
 * Manages settings tabs and field definitions.
 */
class Main {

	/**
	 * Get all settings tabs.
	 *
	 * @return array Array of tab keys => labels.
	 */
	public static function get_tabs(): array {
		$tabs = array(
			'general' => __( 'General', 'zontact' ),
			'button'  => __( 'Button', 'zontact' ),
			'email'   => __( 'Email', 'zontact' ),
			'form'    => __( 'Form', 'zontact' ),
			'storage' => __( 'Storage', 'zontact' ),
		);

		/**
		 * Filter the tabs array before registration.
		 *
		 * @param array $tabs Array of tab definitions.
		 */
		return apply_filters( 'zontact_settings_tabs', $tabs );
	}

	/**
	 * Get all settings definitions.
	 *
	 * @return array Array of setting definitions.
	 */
	public static function get_settings(): array {
		$settings = array();

		// General settings.
		$settings = array_merge( $settings, General::get_settings() );

		// Button settings.
		$settings = array_merge( $settings, Button::get_settings() );

		// Email settings.
		$settings = array_merge( $settings, Email::get_settings() );

		// Form settings.
		$settings = array_merge( $settings, Form::get_settings() );

		// Storage settings.
		$settings = array_merge( $settings, Storage::get_settings() );

		/**
		 * Filter the settings array before registration.
		 *
		 * @param array $settings Array of setting definitions.
		 */
		return apply_filters( 'zontact_settings_array', $settings );
	}

	/**
	 * Get all settings with tabs.
	 *
	 * @return array Array with 'tabs' and 'settings' keys.
	 */
	public static function get_all(): array {
		return array(
			'tabs'     => self::get_tabs(),
			'settings' => self::get_settings(),
		);
	}
}

