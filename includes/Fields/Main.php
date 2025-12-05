<?php
/**
 * Main fields class.
 *  
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Fields
 */

namespace ThirtyEightZo\Zontact\Fields;

use ThirtyEightZo\Zontact\Fields\Types\Base;
use ThirtyEightZo\Zontact\Fields\Types\Text;
use ThirtyEightZo\Zontact\Fields\Types\Email;
use ThirtyEightZo\Zontact\Fields\Types\Textarea;
use ThirtyEightZo\Zontact\Fields\Types\Number;
use ThirtyEightZo\Zontact\Fields\Types\Checkbox;
use ThirtyEightZo\Zontact\Fields\Types\Select;
use ThirtyEightZo\Zontact\Fields\Types\Color;

defined( 'ABSPATH' ) || exit;

/**
 * Main fields class.
 *  
 * @since 1.0.0
 * 
 * @package ThirtyEightZo\Zontact\Fields
 */
class Main {

	/**
	 * Get field class name by type.
	 *
	 * @param string $type Field type.
	 * @return string|null Class name or null if not found.
	 */
	public static function get_field_class( string $type ): ?string {
		$types = array(
			'text'     => Text::class,
			'email'    => Email::class,
			'textarea' => Textarea::class,
			'number'   => Number::class,
			'checkbox' => Checkbox::class,
			'select'   => Select::class,
			'color'    => Color::class,
		);

		/**
		 * Filter field type classes.
		 *
		 * @param array  $types Array of field type => class name mappings.
		 * @param string $type  Current field type.
		 */
		$types = apply_filters( 'zontact_field_types', $types, $type );

		return $types[ $type ] ?? null;
	}

	/**
	 * Render a field.
	 *
	 * @param array $field Field configuration.
	 * @param mixed $value Field value.
	 * @return void
	 */
	public static function render( array $field, $value = '' ): void {
		$type = $field['type'] ?? 'text';
		$class_name = self::get_field_class( $type );

		if ( ! $class_name || ! class_exists( $class_name ) ) {
			// Fallback to text field if type not found.
			$class_name = Text::class;
		}

		// Check if class extends Base.
		if ( ! is_subclass_of( $class_name, Base::class ) ) {
			return;
		}

		$field_instance = new $class_name( $field, $value );
		$field_instance->render();

		/**
		 * Fires after rendering a field.
		 *
		 * @param array $field Field configuration.
		 * @param mixed $value Field value.
		 */
		do_action( 'zontact_after_render_field', $field, $value );
	}
}
