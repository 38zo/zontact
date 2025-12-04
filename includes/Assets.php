<?php
/**
 * Enqueue frontend assets for Zontact.
 *
 * @package ThirtyEightZo\Zontact
 */

namespace ThirtyEightZo\Zontact;

defined( 'ABSPATH' ) || exit;

/**
 * Handles scripts, styles, and localized data.
 */
final class Assets {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		$instance = new self();

		add_action( 'wp_enqueue_scripts', [ $instance, 'enqueue' ] );
		add_action( 'wp_enqueue_scripts', [ $instance, 'add_inline_styles' ], 20 );
	}

	/**
	 * Enqueue plugin styles and scripts.
	 *
	 * @return void
	 */
	public function enqueue(): void {
		$options = Options::get();

		// Only enqueue if button is enabled.
		if ( empty( $options['enable_button'] ) ) {
			return;
		}

		wp_enqueue_style(
			'zontact',
			ZONTACT_URL . 'assets/css/zontact.css',
			[],
			ZONTACT_VERSION
		);

		wp_enqueue_script(
			'zontact',
			ZONTACT_URL . 'assets/js/zontact.js',
			[],
			ZONTACT_VERSION,
			true
		);

		wp_localize_script(
			'zontact',
			'zontact',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'zontact_submit' ),
				'strings'  => [
					'sending' => __( 'Sending…', 'zontact' ),
					'error'   => __( 'Please fix the errors and try again.', 'zontact' ),
                    'success' => $options['success_message'] ?? __( 'Message sent successfully!', 'zontact' ),
                    'warning' => __( 'Message saved, but email delivery failed. We will review it shortly.', 'zontact' ),
				],
			]
		);
	}

	/**
	 * Add inline accent color styles.
	 *
	 * @return void
	 */
	public function add_inline_styles(): void {
		$options = Options::get();

		// Only add styles if button is enabled.
		if ( empty( $options['enable_button'] ) ) {
			return;
		}

		$accent   = ! empty( $options['accent_color'] ) ? esc_attr( $options['accent_color'] ) : '#2563eb';
		$css_vars = array( "--zontact-accent: {$accent};" );

		// Button customization CSS variables
		$button_bg_color = ! empty( $options['button_bg_color'] ) ? esc_attr( $options['button_bg_color'] ) : $accent;
		$button_text_color = ! empty( $options['button_text_color'] ) ? esc_attr( $options['button_text_color'] ) : '#ffffff';
		$button_border_radius = ! empty( $options['button_border_radius'] ) ? (int) $options['button_border_radius'] : 9999;
		$button_icon_size = ! empty( $options['button_icon_size'] ) ? (int) $options['button_icon_size'] : 20;

		$css_vars[] = "--zontact-button-bg: {$button_bg_color};";
		$css_vars[] = "--zontact-button-text: {$button_text_color};";
		$css_vars[] = "--zontact-button-radius: {$button_border_radius}px;";
		$css_vars[] = "--zontact-icon-size: {$button_icon_size}px;";

		// Generate button size CSS
		$button_size = ! empty( $options['button_size'] ) ? $options['button_size'] : 'medium';
		$custom_padding = ! empty( $options['button_custom_size'] ) ? esc_attr( $options['button_custom_size'] ) : '';

		$size_css = '';
		if ( 'custom' === $button_size && ! empty( $custom_padding ) ) {
			$size_css = ".zontact-button { padding: {$custom_padding} !important; }";
		}

		// Generate dynamic CSS
		$css = ":root { " . implode( ' ', $css_vars ) . " }";
		if ( ! empty( $size_css ) ) {
			$css .= ' ' . $size_css;
		}

		/**
		 * Filter inline CSS styles before output.
		 * Allows extensions to modify or add CSS.
		 *
		 * @param string $css     CSS string.
		 * @param array  $options Current plugin options.
		 */
		$css = apply_filters( 'zontact_inline_css', $css, $options );

		wp_add_inline_style( 'zontact', $css );
	}
}
