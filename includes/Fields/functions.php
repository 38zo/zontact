<?php
/**
 * Functions for the fields.
 *
 * @package ThirtyEightZo\Zontact\Fields
 */

namespace ThirtyEightZo\Zontact\Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Render a field.
 *
 * @param array $field Field configuration.
 * @param mixed $value Field value.
 * @return void
 */
function render_field( array $field, $value = '' ): void {
	Main::render( $field, $value );
}
