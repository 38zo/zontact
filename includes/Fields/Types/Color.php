<?php
/**
 * The color custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */

namespace ThirtyEightZo\Zontact\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * The color custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */
class Color extends Base {

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render(): void {
		printf(
			'<input type="color" name="%1$s" id="%2$s" value="%3$s" class="zontact-color-picker">',
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_id() ),
			esc_attr( $this->value )
		);
		$this->render_description();
	}
}
