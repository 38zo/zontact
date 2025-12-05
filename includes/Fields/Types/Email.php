<?php
/**
 * The email custom field class.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Fields\Types
 */

namespace ThirtyEightZo\Zontact\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * The email custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */
class Email extends Base {

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render(): void {
		printf(
			'<input type="email" class="regular-text" name="%1$s" id="%2$s" value="%3$s">',
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_id() ),
			esc_attr( $this->value )
		);
		$this->render_description();
	}
}
