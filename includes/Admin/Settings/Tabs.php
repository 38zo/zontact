<?php
/**
 * Settings tabs and definitions.
 *
 * @package ThirtyEightZo\Zontact\Admin\Settings
 */

namespace ThirtyEightZo\Zontact\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tabs
 *
 * Manages settings tabs and field definitions.
 */
class Tabs {

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
		$settings = array_merge( $settings, self::get_general_settings() );

		// Button settings.
		$settings = array_merge( $settings, self::get_button_settings() );

		// Email settings.
		$settings = array_merge( $settings, self::get_email_settings() );

		// Form settings.
		$settings = array_merge( $settings, self::get_form_settings() );

		// Storage settings.
		$settings = array_merge( $settings, self::get_storage_settings() );

		/**
		 * Filter the settings array before registration.
		 *
		 * @param array $settings Array of setting definitions.
		 */
		return apply_filters( 'zontact_settings_array', $settings );
	}

	/**
	 * Get general settings.
	 *
	 * @return array
	 */
	private static function get_general_settings(): array {
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

	/**
	 * Get button settings.
	 *
	 * @return array
	 */
	private static function get_button_settings(): array {
		return array(
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
			array(
				'id'      => 'button_size',
				'title'   => __( 'Button Size', 'zontact' ),
				'desc'    => __( 'Predefined button sizes or use custom padding.', 'zontact' ),
				'type'    => 'select',
				'choices' => array(
					'small'  => __( 'Small', 'zontact' ),
					'medium' => __( 'Medium', 'zontact' ),
					'large'  => __( 'Large', 'zontact' ),
					'custom' => __( 'Custom', 'zontact' ),
				),
				'default' => 'medium',
				'tab'     => 'button',
				'section' => __( 'Size & Spacing', 'zontact' ),
			),
			array(
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
			),
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
		);
	}

	/**
	 * Get email settings.
	 *
	 * @return array
	 */
	private static function get_email_settings(): array {
		return array(
			array(
				'id'      => 'recipient_email',
				'title'   => __( 'Recipient Email', 'zontact' ),
				'desc'    => __( 'Email address where contact form submissions will be sent.', 'zontact' ),
				'type'    => 'email',
				'default' => get_option( 'admin_email' ),
				'tab'     => 'email',
				'section' => __( 'Email Settings', 'zontact' ),
			),
			array(
				'id'      => 'subject',
				'title'   => __( 'Email Subject', 'zontact' ),
				'desc'    => __( 'Subject line for contact form email notifications.', 'zontact' ),
				'type'    => 'text',
				'default' => sprintf(
					/* translators: %s: site name */
					__( 'New message from %s', 'zontact' ),
					wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
				),
				'tab'     => 'email',
				'section' => __( 'Email Settings', 'zontact' ),
			),
		);
	}

	/**
	 * Get form settings.
	 *
	 * @return array
	 */
	private static function get_form_settings(): array {
		return array(
			array(
				'id'      => 'consent_text',
				'title'   => __( 'Consent Text', 'zontact' ),
				'desc'    => __( 'GDPR consent text displayed with the consent checkbox. Leave empty to disable the consent requirement.', 'zontact' ),
				'type'    => 'textarea',
				'default' => __(
					'I agree to the processing of my personal data (name, email, message) for the purpose of responding to my inquiry. This data will be stored securely and not shared with third parties.',
					'zontact'
				),
				'tab'     => 'form',
				'section' => __( 'Form Settings', 'zontact' ),
			),
			array(
				'id'      => 'success_message',
				'title'   => __( 'Success Message', 'zontact' ),
				'desc'    => __( 'Message displayed to users after successfully submitting the form.', 'zontact' ),
				'type'    => 'text',
				'default' => __( 'Thanks! Your message has been sent.', 'zontact' ),
				'tab'     => 'form',
				'section' => __( 'Form Settings', 'zontact' ),
			),
		);
	}

	/**
	 * Get storage settings.
	 *
	 * @return array
	 */
	private static function get_storage_settings(): array {
		return array(
			array(
				'id'      => 'save_messages',
				'title'   => __( 'Save Messages', 'zontact' ),
				'desc'    => __( 'Store form submissions in the database. This allows you to view and manage submissions from the Entries page.', 'zontact' ),
				'type'    => 'checkbox',
				'default' => true,
				'tab'     => 'storage',
				'section' => __( 'Database Storage', 'zontact' ),
			),
			array(
				'id'      => 'data_retention_days',
				'title'   => __( 'Data Retention (Days)', 'zontact' ),
				'desc'    => __( 'How many days to keep saved messages before automatic deletion. This helps with GDPR compliance.', 'zontact' ),
				'type'    => 'number',
				'default' => 30,
				'min'     => 1,
				'max'     => 365,
				'tab'     => 'storage',
				'section' => __( 'Database Storage', 'zontact' ),
			),
		);
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

