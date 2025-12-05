<?php
/**
 * The textarea custom field class.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Fields\Types
 */

namespace ThirtyEightZo\Zontact\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * The textarea custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */
class Textarea extends Base {

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render(): void {
		printf(
			'<textarea name="%1$s" id="%2$s" class="large-text" rows="3">%3$s</textarea>',
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_id() ),
			esc_textarea( $this->value )
		);
		$this->render_description();
	}
}
