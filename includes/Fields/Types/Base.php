<?php
/**
 * The base custom field class.
 *
 * Custom field types such as texts, textarea etc can extend this class.
 * They will then be instantiated with details of the corresponding custom field.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */

namespace ThirtyEightZo\Zontact\Fields\Types;

defined( 'ABSPATH' ) || exit;

/**
 * The base custom field class.
 *
 * @since 1.0.0
 *
 * @package ThirtyEightZo\Zontact\Fields\Types
 */
abstract class Base {

	/**
	 * Field configuration.
	 *
	 * @var array
	 */
	protected $field = array();

	/**
	 * Field value.
	 *
	 * @var mixed
	 */
	protected $value = '';

	/**
	 * Constructor.
	 *
	 * @param array $field Field configuration.
	 * @param mixed $value Field value.
	 */
	public function __construct( array $field, $value = '' ) {
		$this->field = $field;
		$this->value = $value;
	}

	/**
	 * Get field name attribute.
	 *
	 * @return string
	 */
	protected function get_name(): string {
		$id = $this->field['id'] ?? '';
		return 'zontact_options[' . esc_attr( $id ) . ']';
	}

	/**
	 * Get field ID attribute.
	 *
	 * @return string
	 */
	protected function get_id(): string {
		$id = $this->field['id'] ?? '';
		return 'zontact_options_' . esc_attr( $id );
	}

	/**
	 * Get field description.
	 *
	 * @return string
	 */
	protected function get_description(): string {
		return $this->field['desc'] ?? '';
	}

	/**
	 * Render field description.
	 *
	 * @return void
	 */
	protected function render_description(): void {
		$desc = $this->get_description();
		if ( ! empty( $desc ) ) {
			echo '<p class="description">' . esc_html( $desc ) . '</p>';
		}
	}

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	abstract public function render(): void;

	/**
	 * Get additional HTML attributes.
	 *
	 * @return string
	 */
	protected function get_attributes(): string {
		$attrs = array();

		if ( isset( $this->field['min'] ) ) {
			$attrs[] = 'min="' . esc_attr( $this->field['min'] ) . '"';
		}
		if ( isset( $this->field['max'] ) ) {
			$attrs[] = 'max="' . esc_attr( $this->field['max'] ) . '"';
		}

		return ! empty( $attrs ) ? ' ' . implode( ' ', $attrs ) : '';
	}
}
