<?php
/**
 * Pro helper functions.
 *
 * @package ThirtyEightZo\Zontact
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if Pro version is active and licensed.
 *
 * @return bool
 */
function zontact_is_pro(): bool {
	return function_exists( 'zon_fs' ) && zon_fs()->can_use_premium_code();
}

/**
 * Check if a specific Pro feature is available.
 *
 * @param string $feature Feature name.
 * @return bool
 */
function zontact_pro_feature_enabled( string $feature ): bool {
	if ( ! zontact_is_pro() ) {
		return false;
	}

	if ( class_exists( \ThirtyEightZo\Zontact\Pro\Pro::class ) ) {
		return \ThirtyEightZo\Zontact\Pro\Pro::instance()->is_feature_enabled( $feature );
	}

	return false;
}

/**
 * Get Pro version number.
 *
 * @return string
 */
function zontact_pro_version(): string {
	return defined( 'ZONTACT_PRO_VERSION' ) ? ZONTACT_PRO_VERSION : '';
}

/**
 * Display Pro badge in admin.
 *
 * @return string
 */
function zontact_pro_badge(): string {
	if ( ! zontact_is_pro() ) {
		return '';
	}

	return '<span class="zontact-pro-badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; margin-left: 8px;">PRO</span>';
}
