<?php
/**
 * Handles frontend rendering for Zontact.
 *
 * @package ThirtyEightZo\Zontact
 */

namespace ThirtyEightZo\Zontact;

defined( 'ABSPATH' ) || exit;

/**
 * Manages frontend form output and structure.
 */
final class Frontend {

	/**
	 * Initialize frontend hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'wp_footer', [ $this, 'render_form' ] );
	}

	/**
	 * Render the contact form markup.
	 *
	 * @return void
	 */
	public function render_form(): void {
		$opts = Options::get();

		// Check if button is enabled.
		if ( empty( $opts['enable_button'] ) ) {
			return;
		}

		$position_class = ( 'left' === $opts['button_position'] ) ? 'zontact-left' : 'zontact-right';
		$accent         = $opts['accent_color'];
		$consent_text   = trim( zontact_sanitize_html( $opts['consent_text'] ) );

		// Button customization options
		$button_label        = ! empty( $opts['button_label'] ) ? $opts['button_label'] : __( 'Contact', 'zontact' );
		$display_mode        = ! empty( $opts['button_display_mode'] ) ? $opts['button_display_mode'] : 'both';
		$button_icon         = ! empty( $opts['button_icon'] ) ? $opts['button_icon'] : 'message';
		$button_size         = ! empty( $opts['button_size'] ) ? $opts['button_size'] : 'medium';
		$button_bg_color     = ! empty( $opts['button_bg_color'] ) ? $opts['button_bg_color'] : $accent;
		$button_text_color   = ! empty( $opts['button_text_color'] ) ? $opts['button_text_color'] : '#ffffff';
		$button_border_radius = ! empty( $opts['button_border_radius'] ) ? (int) $opts['button_border_radius'] : 9999;

		$button_classes = array( 'zontact-button' );
		$button_classes[] = 'zontact-button-size-' . esc_attr( $button_size );
		$button_classes[] = 'zontact-button-mode-' . esc_attr( $display_mode );

		?>
		<div 
			class="zontact-root <?php echo esc_attr( $position_class ); ?>" 
			data-accent="<?php echo esc_attr( $accent ); ?>"
			data-button-bg="<?php echo esc_attr( $button_bg_color ); ?>"
			data-button-text="<?php echo esc_attr( $button_text_color ); ?>"
			data-button-radius="<?php echo esc_attr( $button_border_radius ); ?>"
		>
			<button 
				type="button" 
				class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>"
				aria-haspopup="dialog" 
				aria-controls="zontact-modal" 
				aria-expanded="false"
			>
				<?php if ( 'label-only' !== $display_mode ) : ?>
					<span class="zontact-button-icon">
						<?php echo $this->render_icon( $button_icon, $opts['button_icon_size'] ?? 20 ); ?>
					</span>
				<?php endif; ?>
				<?php if ( 'icon-only' !== $display_mode ) : ?>
					<span class="zontact-button-label">
						<?php echo esc_html( $button_label ); ?>
					</span>
				<?php endif; ?>
			</button>

			<div 
				id="zontact-modal" 
				class="zontact-modal" 
				role="dialog" 
				aria-modal="true" 
				aria-labelledby="zontact-title" 
				aria-hidden="true"
			>
				<div class="zontact-modal__overlay" data-zontact-close></div>
				<div class="zontact-modal__dialog" role="document">

					<header class="zontact-modal__header">
						<h2 id="zontact-title"><?php esc_html_e( 'Contact us', 'zontact' ); ?></h2>
						<button 
							type="button" 
							class="zontact-close" 
							aria-label="<?php esc_attr_e( 'Close', 'zontact' ); ?>" 
							data-zontact-close
						>
							&times;
						</button>
					</header>

					<form class="zontact-form" novalidate>
						<div class="zontact-form__content">
							<?php $this->render_fields( $consent_text ); ?>
						</div>

						<div class="zontact-actions">
							<button type="submit" class="zontact-submit">
								<?php esc_html_e( 'Send', 'zontact' ); ?>
							</button>
							<div class="zontact-status" role="status" aria-live="polite"></div>
						</div>
					</form>

				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the input fields and consent checkbox.
	 *
	 * @param string $consent_text Optional consent text.
	 * @return void
	 */
	private function render_fields( string $consent_text ): void {
		?>
		<div class="zontact-field">
			<label for="zontact-name"><?php esc_html_e( 'Name', 'zontact' ); ?></label>
			<input id="zontact-name" name="name" type="text" autocomplete="name" required>
		</div>

		<div class="zontact-field">
			<label for="zontact-email"><?php esc_html_e( 'Email', 'zontact' ); ?></label>
			<input id="zontact-email" name="email" type="email" autocomplete="email" required>
		</div>

		<div class="zontact-field">
			<label for="zontact-message"><?php esc_html_e( 'Message', 'zontact' ); ?></label>
			<textarea id="zontact-message" name="message" rows="4" required></textarea>
		</div>

		<div class="zontact-field zontact--hp" aria-hidden="true" hidden>
			<label for="zontact-website">Website</label>
			<input id="zontact-website" name="website" type="text" tabindex="-1" autocomplete="off">
		</div>

		<?php if ( $consent_text ) : ?>
			<div class="zontact-field zontact-consent">
				<label>
					<input name="consent" type="checkbox" required>
					<span class="zontact-consent__text">
						<?php echo wp_kses_post( $consent_text ); ?>
					</span>
				</label>
			</div>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render an icon SVG based on icon name.
	 *
	 * @param string $icon_name Icon identifier.
	 * @param int    $size      Icon size in pixels.
	 * @return string SVG markup.
	 */
	private function render_icon( string $icon_name, int $size = 20 ): string {
		$icon_name = sanitize_key( $icon_name );
		$size      = max( 12, min( 48, $size ) );
		$viewbox   = "0 0 {$size} {$size}";

		/**
		 * Filter icon SVG markup before rendering.
		 * Allows extensions to provide custom icons.
		 *
		 * @param string $svg     SVG markup (empty string to use default).
		 * @param string $icon_name Icon identifier.
		 * @param int    $size    Icon size.
		 */
		$svg = apply_filters( 'zontact_button_icon_svg', '', $icon_name, $size );

		if ( ! empty( $svg ) ) {
			return $svg;
		}

		// Default icons as inline SVG
		switch ( $icon_name ) {
			case 'message':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>';
				break;
			case 'chat':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/></svg>';
				break;
			case 'mail':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>';
				break;
			case 'phone':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>';
				break;
			case 'comment':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M21.99 4c0-1.1-.89-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18zM18 14H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>';
				break;
			case 'help':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11 18h2v-2h-2v2zm1-16C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-2.21 0-4 1.79-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.75-3 5h2c0-2.25 3-2.5 3-5 0-2.21-1.79-4-4-4z"/></svg>';
				break;
			case 'pencil':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>';
				break;
			case 'plus':
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>';
				break;
			default:
				// Default to message icon
				$svg = '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>';
				break;
		}

		return $svg;
	}
}
