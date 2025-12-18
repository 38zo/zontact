<?php
/**
 * Zontact Pro.
 *
 * @package ThirtyEightZo\Zontact
 */

namespace ThirtyEightZo\Zontact\Pro;

defined( 'ABSPATH' ) || exit;

/**
 * Main Pro class.
 */
final class Pro {

	/**
	 * Singleton instance.
	 *
	 * @var Pro|null
	 */
	private static ?Pro $instance = null;

	/**
	 * List of Pro module classes.
	 *
	 * @var array
	 */
	private const PRO_MODULES = [
		// Add your Pro modules here as you create them
		// Example:
		// AdvancedFields::class,
		// EmailIntegrations::class,
		// Analytics::class,
	];

	/**
	 * Get instance.
	 *
	 * @return Pro
	 */
	public static function instance(): Pro {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Pro constructor.
	 */
	private function __construct() {
		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define Pro constants.
	 *
	 * @return void
	 */
	private function define_constants(): void {
		if ( ! defined( 'ZONTACT_PRO_VERSION' ) ) {
			define( 'ZONTACT_PRO_VERSION', '1.1.2' );
		}

		if ( ! defined( 'ZONTACT_PRO_PATH' ) ) {
			define( 'ZONTACT_PRO_PATH', ZONTACT_PATH . 'Pro/' );
		}
	}

	/**
	 * Initialize hooks.
	 *
	 * @return void
	 */
	private function init_hooks(): void {
		add_action( 'init', [ $this, 'init_pro_modules' ], 20 );
	}

	/**
	 * Initialize Pro modules.
	 *
	 * @return void
	 */
	public function init_pro_modules(): void {
		foreach ( self::PRO_MODULES as $module ) {
			if ( class_exists( $module ) ) {
				$instance = new $module();

				if ( method_exists( $instance, 'register' ) ) {
					$instance->register();
				}
			}
		}
	}

	/**
	 * Check if a specific Pro feature is enabled.
	 *
	 * @param string $feature Feature name.
	 * @return bool
	 */
	public function is_feature_enabled( string $feature ): bool {
		$enabled_features = get_option( 'zontact_pro_features', [] );
		return in_array( $feature, $enabled_features, true );
	}
}