<?php

/**
 * Storage settings tab.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
 */

 namespace ThirtyEightZo\Zontact\Admin\Settings\Tabs;

 defined( 'ABSPATH' ) || exit;

 /**
  * Storage settings tab.
  * 
  * @since 1.0.0
  * 
  * @package ThirtyEightZo\Zontact\Admin\Settings\Tabs
  */
 class Storage {
    /**
     * Get the settings for the storage tab.
     * 
     * @return array
     */
    public static function get_settings(): array {
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
 }