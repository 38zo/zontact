<?php
/**
 * Zontact global functions
 *
 * @package   Zontact
 * @author    Lewis ushindi <frenziecodes@gmail.com>
 * @license   GPL-3.0+
 * @link      https://github.com/38zo/zontact
 * @copyright 2025 Zontact LLC
 */

 /**
 * Returns the name of the plugin. (Allows the name to be overridden.)
 * @return string
 */
function zontact_plugin_name() {
	return apply_filters( 'zontact_plugin_name', 'Zontact' );
}

/**
 * Returns the dashicon of the plugin.
 */
function zontact_plugin_dashicon() {
    return apply_filters( 'zontact_plugin_dashicon', 'dashicons-email-alt2' );
}

/**
 * Returns the top level menu slug for the plugin.
 * @return string
 */
function zontact_top_level_menu_slug() {
    return apply_filters( 'zontact_top_level_menu_slug', 'Zontact' );
}

/**
 * Sanitize HTML to make it safe to output. Used to sanitize potentially harmful HTML.
 *
 * @since 1.0
 *
 * @param string $text
 * @return string
 */
function zontact_sanitize_html( $text ) {
	$safe_text = wp_kses_post( $text );
	return $safe_text;
}

/**
 * Sanitize user input for safe output.
 *
 * @param string $input
 * @return string
 */
function zontact_sanitize_input( $input ) {
    // 1. Remove any script tags or inline event attributes safely.
    $input = wp_kses( $input, array(
        'a'      => array( 'href' => array(), 'title' => array(), 'target' => array() ),
        'br'     => array(),
        'em'     => array(),
        'strong' => array(),
        'p'      => array(),
        'span'   => array(),
    ) );

    // 2. Strip any "javascript:" URLs if present.
    $input = preg_replace( '/javascript\s*:/i', '', $input );

    // 3. Final safety net
    return sanitize_text_field( $input );
}

/**
 * Do full sanitization of a string
 *
 * @param string $text
 *
 * @return string
 */
function zontact_sanitize_full( $text ) {
    return zontact_sanitize_input( $text );
}

/**
 * Returns the role and all roles with higher privileges.
 *
 * @param $role
 * @return array|string[]
 */
function zontact_get_roles_and_higher( $role ) {
    // Define roles in hierarchical order
    $roles_hierarchy = array(
        'subscriber',
        'contributor',
        'author',
        'editor',
        'administrator',
        'super_admin' // Note: 'super_admin' is used in Multisite networks only
    );

    // Find the index of the input role
    $role_index = array_search( $role, $roles_hierarchy );

    // If the input role is not found, return the input role.
    if ( $role_index === false) {
        // Return the input role, and also admin, as we always want admins to be able to create Forms, when custom roles are set.
        return array( $role, 'administrator', 'super_admin' );
    }

    // Get the roles with the same or higher privileges
    return array_slice( $roles_hierarchy, $role_index );
}

/**
 * Get a Zontact setting by key.
 *
 * @param string $key     Option key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function zontact_get_option( string $key, $default = '' ) {
	$options = get_option( 'zontact_options', [] );
	return $options[ $key ] ?? $default;
}

/**
 * Update a Zontact setting by key.
 *
 * @param string $key   Option key.
 * @param mixed  $value New value.
 * @return bool
 */
function zontact_update_option( string $key, $value ): bool {
	$options         = get_option( 'zontact_options', [] );
	$options[ $key ] = $value;
	return update_option( 'zontact_options', $options );
}
