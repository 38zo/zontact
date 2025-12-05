<?php
/**
 * The number custom field class.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Fields\Types
 */

namespace ThirtyEightZo\Zontact\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * The number custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */
class Number extends Base {

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render(): void {
		printf(
			'<input type="number" class="small-text" name="%1$s" id="%2$s" value="%3$s"%4$s>',
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_id() ),
			esc_attr( $this->value ),
			wp_kses_post( $this->get_attributes() )
		);
		$this->render_description();
	}
}
