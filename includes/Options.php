<?php
/**
 * Handles Zontact plugin options.
 *
 * @package ThirtyEightZo\Zontact
 */

namespace ThirtyEightZo\Zontact;

defined( 'ABSPATH' ) || exit;

/**
 * Class Options
 *
 * Provides default values, sanitization, and retrieval of Zontact settings.
 */
class Options {

	/**
	 * Get default plugin options.
	 *
	 * @return array Default option values.
	 */
	public static function defaults(): array {
		$defaults = array(
			'enable_button'         => true,
			'recipient_email'       => get_option( 'admin_email' ),
			'subject'               => sprintf(
				/* translators: %s: site name */
				__( 'New message from %s', 'zontact' ),
				wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
			),
			'save_messages'         => true,
			'data_retention_days'   => 30,
			'button_position'       => 'right',
			'accent_color'          => '#2563eb',
			'button_label'          => __( 'Contact', 'zontact' ),
			'button_display_mode'   => 'both', // 'icon-only', 'label-only', 'both'
			'button_icon'           => 'message', // Default icon name
			'button_icon_size'      => 20, // Icon size in pixels
			'button_size'           => 'medium', // 'small', 'medium', 'large', 'custom'
			'button_custom_size'    => '', // Custom size for padding
			'button_bg_color'       => '', // Empty uses accent_color
			'button_text_color'     => '#ffffff',
			'button_border_radius'  => 9999, // Large value for fully rounded (pill shape)
			'consent_text'          => __(
				'I agree to the processing of my personal data (name, email, message) for the purpose of responding to my inquiry. This data will be stored securely and not shared with third parties.',
				'zontact'
			),
			'success_message'       => __( 'Thanks! Your message has been sent.', 'zontact' ),
		);

		/**
		 * Filters the default Zontact options.
		 *
		 * @param array $defaults Default option values.
		 */
		return apply_filters( 'zontact_default_options', $defaults );
	}

	/**
	 * Get merged user and default options.
	 *
	 * @return array Plugin options.
	 */
	public static function get(): array {
		$defaults = self::defaults();
		$opts     = get_option( 'zontact_options', array() );

		if ( ! is_array( $opts ) ) {
			$opts = array();
		}

		return array_merge( $defaults, $opts );
	}

	/**
	 * Sanitize plugin options.
	 *
	 * @param array $input Raw input values.
	 * @return array Sanitized options.
	 */
	public static function sanitize( array $input ): array {
		$defaults = self::defaults();
		// Get existing options to preserve settings from other tabs
		$existing = get_option( 'zontact_options', array() );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}
		// Merge with defaults to ensure all keys exist
		$existing = array_merge( $defaults, $existing );
		$output   = $existing;

		// Only update fields that are present in the input (from current tab)
		if ( isset( $input['enable_button'] ) ) {
			// Handle checkbox: normalize value (may be array if both hidden and checkbox submitted)
			$value = is_array( $input['enable_button'] ) ? end( $input['enable_button'] ) : $input['enable_button'];
			$output['enable_button'] = ( '1' === $value || 1 === $value || true === $value );
		}

		if ( isset( $input['recipient_email'] ) ) {
			$output['recipient_email'] = sanitize_email( $input['recipient_email'] );
		}

		if ( isset( $input['subject'] ) ) {
			$output['subject'] = zontact_sanitize_html( $input['subject'] );
		}

		if ( isset( $input['save_messages'] ) ) {
			// Handle checkbox: normalize value (may be array if both hidden and checkbox submitted)
			$value = is_array( $input['save_messages'] ) ? end( $input['save_messages'] ) : $input['save_messages'];
			$output['save_messages'] = ( '1' === $value || 1 === $value || true === $value );
		}

		if ( isset( $input['data_retention_days'] ) ) {
			$output['data_retention_days'] = max( 1, (int) $input['data_retention_days'] );
		}

		if ( isset( $input['button_position'] ) ) {
			$output['button_position'] = in_array( $input['button_position'], array( 'left', 'right' ), true )
				? $input['button_position']
				: $defaults['button_position'];
		}

		if ( isset( $input['accent_color'] ) ) {
			$output['accent_color'] = preg_replace( '/[^#a-fA-F0-9]/', '', (string) $input['accent_color'] );
		}

		if ( isset( $input['consent_text'] ) ) {
			$output['consent_text'] = zontact_sanitize_html( $input['consent_text'] );
		}

		if ( isset( $input['success_message'] ) ) {
			$output['success_message'] = zontact_sanitize_html( $input['success_message'] );
		}

		// Button customization settings
		if ( isset( $input['button_label'] ) ) {
			$output['button_label'] = sanitize_text_field( $input['button_label'] );
		}

		if ( isset( $input['button_display_mode'] ) ) {
			$output['button_display_mode'] = in_array( $input['button_display_mode'], array( 'icon-only', 'label-only', 'both' ), true )
				? $input['button_display_mode']
				: $defaults['button_display_mode'];
		}

		if ( isset( $input['button_icon'] ) ) {
			$output['button_icon'] = sanitize_text_field( $input['button_icon'] );
		}

		if ( isset( $input['button_icon_size'] ) ) {
			$output['button_icon_size'] = max( 12, min( 48, (int) $input['button_icon_size'] ) );
		}

		if ( isset( $input['button_size'] ) ) {
			$output['button_size'] = in_array( $input['button_size'], array( 'small', 'medium', 'large', 'custom' ), true )
				? $input['button_size']
				: $defaults['button_size'];
		}

		if ( isset( $input['button_custom_size'] ) ) {
			$output['button_custom_size'] = sanitize_text_field( $input['button_custom_size'] );
		}

		if ( isset( $input['button_bg_color'] ) ) {
			$output['button_bg_color'] = preg_replace( '/[^#a-fA-F0-9]/', '', (string) $input['button_bg_color'] );
		}

		if ( isset( $input['button_text_color'] ) ) {
			$output['button_text_color'] = preg_replace( '/[^#a-fA-F0-9]/', '', (string) $input['button_text_color'] );
		}

		if ( isset( $input['button_border_radius'] ) ) {
			$output['button_border_radius'] = max( 0, min( 9999, (int) $input['button_border_radius'] ) );
		}

		return $output;
	}
}
