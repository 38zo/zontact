<?php

/**
 * General settings tab.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
 */

 namespace ThirtyEightZo\Zontact\Admin\Settings\Tabs;

 defined( 'ABSPATH' ) || exit;

 /**
  * General settings tab.
  * 
  * @since 1.0.0
  * 
  * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
  */
 class General {
    /**
     * Get the settings for the general tab.
     * 
     * @return array
     */
    public static function get_settings(): array {
        return array(
			array(
				'id'      => 'enable_button',
				'title'   => __( 'Enable Contact Button', 'zontact' ),
				'desc'    => __( 'Enable or disable the floating contact button on the frontend.', 'zontact' ),
				'type'    => 'checkbox',
				'default' => true,
				'tab'     => 'general',
				'section' => __( 'Visibility', 'zontact' ),
			),
			array(
				'id'      => 'button_position',
				'title'   => __( 'Button Position', 'zontact' ),
				'desc'    => __( 'Choose where the contact button appears on the page.', 'zontact' ),
				'type'    => 'select',
				'choices' => array(
					'right' => __( 'Right', 'zontact' ),
					'left'  => __( 'Left', 'zontact' ),
				),
				'default' => 'right',
				'tab'     => 'general',
				'section' => __( 'Appearance', 'zontact' ),
			),
			array(
				'id'      => 'accent_color',
				'title'   => __( 'Accent Color', 'zontact' ),
				'desc'    => __( 'The primary color used for the contact button and form accents.', 'zontact' ),
				'type'    => 'color',
				'default' => '#2563eb',
				'tab'     => 'general',
				'section' => __( 'Appearance', 'zontact' ),
			),
		);
    }
 }