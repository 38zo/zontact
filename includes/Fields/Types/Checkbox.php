<?php
/**
 * The checkbox custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */

namespace ThirtyEightZo\Zontact\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * The checkbox custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */
class Checkbox extends Base {

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render(): void {
		$checked = checked( $this->value, true, false );
		$desc    = $this->get_description();

		// Add hidden input to ensure unchecked checkboxes are submitted.
		printf(
			'<input type="hidden" name="%1$s" value="0">',
			esc_attr( $this->get_name() )
		);

		printf(
			'<label><input type="checkbox" name="%1$s" id="%2$s" value="1" %3$s> %4$s</label>',
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_id() ),
			wp_kses_post( $checked ),
			esc_html( $desc )
		);
	}
}
