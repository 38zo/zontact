<?php
/**
 * Settings page view.
 *
 * @package ThirtyEightZo\Zontact\Admin\Settings
 */

namespace ThirtyEightZo\Zontact\Admin\Settings;

use ThirtyEightZo\Zontact\Fields\Main as Fields;
use ThirtyEightZo\Zontact\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Class View
 *
 * Handles rendering of the settings page.
 */
class View {

	/**
	 * Render the settings page.
	 *
	 * @return void
	 */
	public static function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings_data = Main::get_settings();
		$tabs          = $settings_data['tabs'];
		$settings      = $settings_data['settings'];
		$options       = Options::get();

		// Get active tab from URL or default to first tab.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : array_key_first( $tabs );

		// Group settings by tab and section.
		$grouped = array();
		foreach ( $settings as $setting ) {
			$tab     = $setting['tab'] ?? 'general';
			$section = $setting['section'] ?? __( 'General', 'zontact' );

			if ( ! isset( $grouped[ $tab ] ) ) {
				$grouped[ $tab ] = array();
			}
			if ( ! isset( $grouped[ $tab ][ $section ] ) ) {
				$grouped[ $tab ][ $section ] = array();
			}

			$grouped[ $tab ][ $section ][] = $setting;
		}

		?>
		<div class="wrap zontact-settings">
			<h1><?php echo esc_html( zontact_plugin_name() ); ?></h1>
			<p><em><?php esc_html_e( 'One button, one form, zero hassle.', 'zontact' ); ?></em></p>

			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
					<a href="?page=zontact-settings&tab=<?php echo esc_attr( $tab_key ); ?>" 
						class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $tab_label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'zontact_settings' );
				wp_nonce_field( 'zontact_settings', 'zontact_settings_nonce' );
				?>

				<?php if ( isset( $grouped[ $active_tab ] ) ) : ?>
					<?php foreach ( $grouped[ $active_tab ] as $section_name => $section_settings ) : ?>
						<?php
						// Check if all fields in this section are conditional.
						$all_conditional = true;
						foreach ( $section_settings as $setting ) {
							if ( ! isset( $setting['conditional'] ) ) {
								$all_conditional = false;
								break;
							}
						}
						$section_class = $all_conditional ? 'zontact-section-all-conditional' : '';
						?>
						<div class="zontact-settings-section <?php echo esc_attr( $section_class ); ?>">
							<h2 class="zontact-section-title"><?php echo esc_html( $section_name ); ?></h2>
							<table class="form-table" role="presentation">
								<tbody>
									<?php foreach ( $section_settings as $setting ) : ?>
										<?php
										$setting_id = $setting['id'];
										$value      = isset( $options[ $setting_id ] ) ? $options[ $setting_id ] : ( $setting['default'] ?? '' );
										$conditional = isset( $setting['conditional'] ) ? $setting['conditional'] : null;
										$row_class = '';
										$row_attrs = '';
										
										if ( $conditional ) {
											$row_class = 'zontact-conditional-field';
											$depends_on = 'zontact_options_' . esc_attr( $conditional['field'] );
											$depends_values = is_array( $conditional['values'] ) ? implode( ',', array_map( 'esc_attr', $conditional['values'] ) ) : esc_attr( $conditional['values'] );
											$row_attrs = sprintf(
												' data-depends-on="%s" data-depends-values="%s"',
												esc_attr( $depends_on ),
												esc_attr( $depends_values )
											);
										}
										?>
										<tr class="<?php echo esc_attr( $row_class ); ?>"<?php echo wp_kses_post( $row_attrs ); ?>>
											<th scope="row">
												<label for="zontact_options_<?php echo esc_attr( $setting_id ); ?>">
													<?php echo esc_html( $setting['title'] ); ?>
												</label>
											</th>
											<td>
												<?php Fields::render( $setting, $value ); ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php submit_button(); ?>
			</form>
		</div>

		<?php
		self::render_styles();
		self::render_scripts();
		?>
		<?php
	}

	/**
	 * Render settings page styles.
	 *
	 * @return void
	 */
	private static function render_styles(): void {
		?>
		<style>
			.zontact-settings .nav-tab-wrapper {
				margin: 20px 0 10px 0;
			}
			.zontact-settings-section {
				margin: 20px 0;
			}
			.zontact-settings-section h2 {
				font-size: 1.3em;
				margin: 1em 0 0.5em 0;
				padding-bottom: 0.5em;
				border-bottom: 1px solid #ddd;
			}
			.zontact-color-picker {
				width: 80px;
				height: 35px;
				border: 1px solid #ddd;
				border-radius: 3px;
				cursor: pointer;
			}
			.zontact-conditional-field {
				display: none;
			}
			.zontact-conditional-field.zontact-field-visible {
				display: table-row;
			}
			.zontact-section-all-conditional {
				display: none;
			}
			.zontact-section-all-conditional.zontact-section-visible {
				display: block;
			}
		</style>
		<?php
	}

	/**
	 * Render settings page scripts.
	 *
	 * @return void
	 */
	private static function render_scripts(): void {
		?>
		<script>
		(function() {
			function updateConditionalFields() {
				document.querySelectorAll('.zontact-conditional-field').forEach(function(row) {
					var dependsOn = row.getAttribute('data-depends-on');
					var dependsValues = row.getAttribute('data-depends-values');
					
					if (!dependsOn || !dependsValues) return;
					
					var field = document.getElementById(dependsOn);
					if (!field) return;
					
					var fieldValue = '';
					if (field.type === 'checkbox') {
						fieldValue = field.checked ? '1' : '0';
					} else {
						fieldValue = field.value || '';
					}
					
					var allowedValues = dependsValues.split(',').map(function(v) { return v.trim(); });
					var shouldShow = allowedValues.indexOf(fieldValue) !== -1 || allowedValues.indexOf(String(fieldValue)) !== -1;
					
					if (shouldShow) {
						row.classList.add('zontact-field-visible');
					} else {
						row.classList.remove('zontact-field-visible');
					}
				});
				
				// Update section titles visibility
				document.querySelectorAll('.zontact-section-all-conditional').forEach(function(section) {
					var visibleFields = section.querySelectorAll('.zontact-conditional-field.zontact-field-visible');
					if (visibleFields.length > 0) {
						section.classList.add('zontact-section-visible');
					} else {
						section.classList.remove('zontact-section-visible');
					}
				});
			}
			
			function initConditionalFields() {
				// Initial check
				updateConditionalFields();
				
				// Update when dependent fields change
				document.querySelectorAll('[id^="zontact_options_"]').forEach(function(field) {
					field.addEventListener('change', updateConditionalFields);
					if (field.type === 'checkbox') {
						field.addEventListener('change', updateConditionalFields);
					}
				});
			}
			
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', initConditionalFields);
			} else {
				initConditionalFields();
			}
		})();
		</script>
		<?php
	}
}

