<?php

/**
 * Button settings tab.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
 */

 namespace ThirtyEightZo\Zontact\Admin\Settings\Tabs;

defined( 'ABSPATH' ) || exit;

/**
 * Button settings tab.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
 */
class Button {
    
    /**
     * Get the settings for the button tab.
     * 
     * @return array
     */
    public static function get_settings(): array {
		$is_pro = function_exists( 'zontact_is_pro' ) && zontact_is_pro();

        $settings = array(
			array(
				'id'      => 'button_label',
				'title'   => __( 'Button Label', 'zontact' ),
				'desc'    => __( 'Text displayed on the contact button.', 'zontact' ),
				'type'    => 'text',
				'default' => __( 'Contact', 'zontact' ),
				'tab'     => 'button',
				'section' => __( 'Label & Display', 'zontact' ),
			),
			array(
				'id'      => 'button_display_mode',
				'title'   => __( 'Display Mode', 'zontact' ),
				'desc'    => __( 'Choose what to display on the button: icon only, label only, or both.', 'zontact' ),
				'type'    => 'select',
				'choices' => array(
					'icon-only'  => __( 'Icon Only', 'zontact' ),
					'label-only' => __( 'Label Only', 'zontact' ),
					'both'       => __( 'Icon & Label', 'zontact' ),
				),
				'default' => 'both',
				'tab'     => 'button',
				'section' => __( 'Label & Display', 'zontact' ),
			),
			array(
				'id'            => 'button_icon',
				'title'         => __( 'Button Icon', 'zontact' ),
				'desc'          => __( 'Choose an icon to display on the button. Icons use simple SVG shapes.', 'zontact' ),
				'type'          => 'select',
				'choices'       => array(
					'message' => __( 'Message Bubble', 'zontact' ),
					'chat'    => __( 'Chat', 'zontact' ),
					'mail'    => __( 'Mail', 'zontact' ),
					'phone'   => __( 'Phone', 'zontact' ),
					'comment' => __( 'Comment', 'zontact' ),
					'help'    => __( 'Help/Question', 'zontact' ),
					'pencil'  => __( 'Pencil/Edit', 'zontact' ),
					'plus'    => __( 'Plus', 'zontact' ),
				),
				'default'       => 'message',
				'tab'           => 'button',
				'section'       => __( 'Icon Settings', 'zontact' ),
				'conditional'   => array(
					'field'  => 'button_display_mode',
					'values' => array( 'icon-only', 'both' ),
				),
			),
			array(
				'id'            => 'button_icon_size',
				'title'         => __( 'Icon Size (px)', 'zontact' ),
				'desc'          => __( 'Size of the icon in pixels (12-48px).', 'zontact' ),
				'type'          => 'number',
				'default'       => 20,
				'min'           => 12,
				'max'           => 48,
				'tab'           => 'button',
				'section'       => __( 'Icon Settings', 'zontact' ),
				'conditional'   => array(
					'field'  => 'button_display_mode',
					'values' => array( 'icon-only', 'both' ),
				),
			),
		);

		// Button size choices - add Custom option only for Pro
		$button_size_choices = array(
			'small'  => __( 'Small', 'zontact' ),
			'medium' => __( 'Medium', 'zontact' ),
			'large'  => __( 'Large', 'zontact' ),
		);

		// Add Custom option for Pro users
		if ( $is_pro ) {
			$button_size_choices['custom'] = __( 'Custom', 'zontact' );
		} else {
			// Show as disabled for free users with upgrade prompt
			$button_size_choices['custom'] = __( 'Custom (Pro Only)', 'zontact' );
		}

		$settings[] = array(
			'id'      => 'button_size',
			'title'   => __( 'Button Size', 'zontact' ),
			'desc'    => __( 'Predefined button sizes or use custom padding.', 'zontact' ),
			'type'    => 'select',
			'choices' => $button_size_choices,
			'default' => 'medium',
			'tab'     => 'button',
			'section' => __( 'Size & Spacing', 'zontact' ),
		);

		// Custom padding field - only for Pro users
		if ( $is_pro ) {
			$settings[] = array(
				'id'            => 'button_custom_size',
				'title'         => __( 'Custom Padding', 'zontact' ),
				'desc'          => __( 'Custom padding (e.g., "10px 16px" for top/bottom and left/right). Only used when Button Size is set to Custom.', 'zontact' ),
				'type'          => 'text',
				'default'       => '',
				'tab'           => 'button',
				'section'       => __( 'Size & Spacing', 'zontact' ),
				'conditional'   => array(
					'field'  => 'button_size',
					'values' => array( 'custom' ),
				),
			);
		}

		$settings = array_merge( $settings, array(
			array(
				'id'      => 'button_bg_color',
				'title'   => __( 'Background Color', 'zontact' ),
				'desc'    => __( 'Button background color. Leave empty to use the Accent Color from General settings.', 'zontact' ),
				'type'    => 'color',
				'default' => '',
				'tab'     => 'button',
				'section' => __( 'Colors', 'zontact' ),
			),
			array(
				'id'      => 'button_text_color',
				'title'   => __( 'Text Color', 'zontact' ),
				'desc'    => __( 'Color of the button text and icon.', 'zontact' ),
				'type'    => 'color',
				'default' => '#ffffff',
				'tab'     => 'button',
				'section' => __( 'Colors', 'zontact' ),
			),
			array(
				'id'      => 'button_border_radius',
				'title'   => __( 'Border Radius (px)', 'zontact' ),
				'desc'    => __( 'Roundness of button corners. Use a large value (like 9999) for fully rounded (pill shape).', 'zontact' ),
				'type'    => 'number',
				'default' => 9999,
				'min'     => 0,
				'max'     => 9999,
				'tab'     => 'button',
				'section' => __( 'Colors', 'zontact' ),
			),
		) );

		return $settings;
    }
}