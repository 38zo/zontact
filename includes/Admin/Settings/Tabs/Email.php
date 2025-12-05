<?php
/**
 * Email settings tab.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
 */

 namespace ThirtyEightZo\Zontact\Admin\Settings\Tabs;

 defined( 'ABSPATH' ) || exit;

 /**
  * Email settings tab.
  * 
  * @since 1.0.0
  * 
  * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
  */
 class Email {

    /**
     * Get the settings for the email tab.
     * 
     * @return array
     */
    public static function get_settings(): array {
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
}