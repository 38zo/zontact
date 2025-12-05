<?php
/**
 * Form settings tab.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
 */

 namespace ThirtyEightZo\Zontact\Admin\Settings\Tabs;

 defined( 'ABSPATH' ) || exit;

 /**
  * Form settings tab.
  * 
  * @since 1.0.0
  * 
  * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
  */
 class Form {
    /**
     * Get the settings for the form tab.
     * 
     * @return array
     */
    public static function get_settings(): array {
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
 }