<?php
/**
 * The select custom field class.
 * 
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Fields\Types
 */

namespace ThirtyEightZo\Zontact\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * The select custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */
class Select extends Base {

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render(): void {
		$choices = $this->field['choices'] ?? array();

		printf(
			'<select name="%1$s" id="%2$s">',
			esc_attr( $this->get_name() ),
			esc_attr( $this->get_id() )
		);

		foreach ( $choices as $choice_value => $choice_label ) {
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $choice_value ),
				selected( $this->value, $choice_value, false ),
				esc_html( $choice_label )
			);
		}

		echo '</select>';
		$this->render_description();
	}
}
