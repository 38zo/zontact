<?php
/**
 * Admin settings for Zontact plugin.
 *
 * @package ThirtyEightZo\Zontact
 */

namespace ThirtyEightZo\Zontact\Admin;

use ThirtyEightZo\Zontact\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 *
 * Registers and renders the Zontact settings page with tabs.
 */
class Settings {

	/**
	 * Register settings and admin menu.
	 *
	 * @return void
	 */
	public static function register(): void {
		register_setting(
			'zontact_settings',
			'zontact_options',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => Options::defaults(),
			)
		);

		/**
		 * Fires after Zontact settings are registered.
		 * Allows extensions to register additional settings.
		 */
		do_action( 'zontact_register_settings' );
	}

	/**
	 * Get all settings with tabs and sections.
	 *
	 * @return array Settings array with 'tabs' and 'settings' keys.
	 */
	public static function get_settings(): array {
		$tabs     = array();
		$settings = array();

		//region General Tab
		$tabs['general'] = __( 'General', 'zontact' );

		$settings[] = array(
			'id'      => 'enable_button',
			'title'   => __( 'Enable Contact Button', 'zontact' ),
			'desc'    => __( 'Enable or disable the floating contact button on the frontend.', 'zontact' ),
			'type'    => 'checkbox',
			'default' => true,
			'tab'     => 'general',
			'section' => __( 'Visibility', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'button_position',
			'title'   => __( 'Button Position', 'zontact' ),
			'desc'    => __( 'Choose where the contact button appears on the page.', 'zontact' ),
			'type'    => 'select',
			'choices' => array(
				'right' => __( 'Right', 'zontact' ),
				'left'  => __( 'Left', 'zontact' ),
			),
			'default' => 'right',
			'tab'     => 'general',
			'section' => __( 'Appearance', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'accent_color',
			'title'   => __( 'Accent Color', 'zontact' ),
			'desc'    => __( 'The primary color used for the contact button and form accents.', 'zontact' ),
			'type'    => 'color',
			'default' => '#2563eb',
			'tab'     => 'general',
			'section' => __( 'Appearance', 'zontact' ),
		);

		//endregion General

		//region Button Tab
		$tabs['button'] = __( 'Button', 'zontact' );

		$settings[] = array(
			'id'      => 'button_label',
			'title'   => __( 'Button Label', 'zontact' ),
			'desc'    => __( 'Text displayed on the contact button.', 'zontact' ),
			'type'    => 'text',
			'default' => __( 'Contact', 'zontact' ),
			'tab'     => 'button',
			'section' => __( 'Label & Display', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'button_display_mode',
			'title'   => __( 'Display Mode', 'zontact' ),
			'desc'    => __( 'Choose what to display on the button: icon only, label only, or both.', 'zontact' ),
			'type'    => 'select',
			'choices' => array(
				'icon-only'  => __( 'Icon Only', 'zontact' ),
				'label-only' => __( 'Label Only', 'zontact' ),
				'both'       => __( 'Icon & Label', 'zontact' ),
			),
			'default' => 'both',
			'tab'     => 'button',
			'section' => __( 'Label & Display', 'zontact' ),
		);

		$settings[] = array(
			'id'            => 'button_icon',
			'title'         => __( 'Button Icon', 'zontact' ),
			'desc'          => __( 'Choose an icon to display on the button. Icons use simple SVG shapes.', 'zontact' ),
			'type'          => 'select',
			'choices'       => array(
				'message'  => __( 'Message Bubble', 'zontact' ),
				'chat'     => __( 'Chat', 'zontact' ),
				'mail'     => __( 'Mail', 'zontact' ),
				'phone'    => __( 'Phone', 'zontact' ),
				'comment'  => __( 'Comment', 'zontact' ),
				'help'     => __( 'Help/Question', 'zontact' ),
				'pencil'   => __( 'Pencil/Edit', 'zontact' ),
				'plus'     => __( 'Plus', 'zontact' ),
			),
			'default'       => 'message',
			'tab'           => 'button',
			'section'       => __( 'Icon Settings', 'zontact' ),
			'conditional'   => array(
				'field'   => 'button_display_mode',
				'values'  => array( 'icon-only', 'both' ),
			),
		);

		$settings[] = array(
			'id'            => 'button_icon_size',
			'title'         => __( 'Icon Size (px)', 'zontact' ),
			'desc'          => __( 'Size of the icon in pixels (12-48px).', 'zontact' ),
			'type'          => 'number',
			'default'       => 20,
			'min'           => 12,
			'max'           => 48,
			'tab'           => 'button',
			'section'       => __( 'Icon Settings', 'zontact' ),
			'conditional'   => array(
				'field'   => 'button_display_mode',
				'values'  => array( 'icon-only', 'both' ),
			),
		);

		$settings[] = array(
			'id'      => 'button_size',
			'title'   => __( 'Button Size', 'zontact' ),
			'desc'    => __( 'Predefined button sizes or use custom padding.', 'zontact' ),
			'type'    => 'select',
			'choices' => array(
				'small'  => __( 'Small', 'zontact' ),
				'medium' => __( 'Medium', 'zontact' ),
				'large'  => __( 'Large', 'zontact' ),
				'custom' => __( 'Custom', 'zontact' ),
			),
			'default' => 'medium',
			'tab'     => 'button',
			'section' => __( 'Size & Spacing', 'zontact' ),
		);

		$settings[] = array(
			'id'            => 'button_custom_size',
			'title'         => __( 'Custom Padding', 'zontact' ),
			'desc'          => __( 'Custom padding (e.g., "10px 16px" for top/bottom and left/right). Only used when Button Size is set to Custom.', 'zontact' ),
			'type'          => 'text',
			'default'       => '',
			'tab'           => 'button',
			'section'       => __( 'Size & Spacing', 'zontact' ),
			'conditional'   => array(
				'field'   => 'button_size',
				'values'  => array( 'custom' ),
			),
		);

		$settings[] = array(
			'id'      => 'button_bg_color',
			'title'   => __( 'Background Color', 'zontact' ),
			'desc'    => __( 'Button background color. Leave empty to use the Accent Color from General settings.', 'zontact' ),
			'type'    => 'color',
			'default' => '',
			'tab'     => 'button',
			'section' => __( 'Colors', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'button_text_color',
			'title'   => __( 'Text Color', 'zontact' ),
			'desc'    => __( 'Color of the button text and icon.', 'zontact' ),
			'type'    => 'color',
			'default' => '#ffffff',
			'tab'     => 'button',
			'section' => __( 'Colors', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'button_border_radius',
			'title'   => __( 'Border Radius (px)', 'zontact' ),
			'desc'    => __( 'Roundness of button corners. Use a large value (like 9999) for fully rounded (pill shape).', 'zontact' ),
			'type'    => 'number',
			'default' => 9999,
			'min'     => 0,
			'max'     => 9999,
			'tab'     => 'button',
			'section' => __( 'Colors', 'zontact' ),
		);

		//endregion Button

		//region Email Tab
		$tabs['email'] = __( 'Email', 'zontact' );

		$settings[] = array(
			'id'      => 'recipient_email',
			'title'   => __( 'Recipient Email', 'zontact' ),
			'desc'    => __( 'Email address where contact form submissions will be sent.', 'zontact' ),
			'type'    => 'email',
			'default' => get_option( 'admin_email' ),
			'tab'     => 'email',
			'section' => __( 'Email Settings', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'subject',
			'title'   => __( 'Email Subject', 'zontact' ),
			'desc'    => __( 'Subject line for contact form email notifications.', 'zontact' ),
			'type'    => 'text',
			'default' => sprintf(
				/* translators: %s: site name */
				__( 'New message from %s', 'zontact' ),
				wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES )
			),
			'tab'     => 'email',
			'section' => __( 'Email Settings', 'zontact' ),
		);

		//endregion Email

		//region Form Tab
		$tabs['form'] = __( 'Form', 'zontact' );

		$settings[] = array(
			'id'      => 'consent_text',
			'title'   => __( 'Consent Text', 'zontact' ),
			'desc'    => __( 'GDPR consent text displayed with the consent checkbox. Leave empty to disable the consent requirement.', 'zontact' ),
			'type'    => 'textarea',
			'default' => __(
				'I agree to the processing of my personal data (name, email, message) for the purpose of responding to my inquiry. This data will be stored securely and not shared with third parties.',
				'zontact'
			),
			'tab'     => 'form',
			'section' => __( 'Form Settings', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'success_message',
			'title'   => __( 'Success Message', 'zontact' ),
			'desc'    => __( 'Message displayed to users after successfully submitting the form.', 'zontact' ),
			'type'    => 'text',
			'default' => __( 'Thanks! Your message has been sent.', 'zontact' ),
			'tab'     => 'form',
			'section' => __( 'Form Settings', 'zontact' ),
		);

		//endregion Form

		//region Storage Tab
		$tabs['storage'] = __( 'Storage', 'zontact' );

		$settings[] = array(
			'id'      => 'save_messages',
			'title'   => __( 'Save Messages', 'zontact' ),
			'desc'    => __( 'Store form submissions in the database. This allows you to view and manage submissions from the Entries page.', 'zontact' ),
			'type'    => 'checkbox',
			'default' => true,
			'tab'     => 'storage',
			'section' => __( 'Database Storage', 'zontact' ),
		);

		$settings[] = array(
			'id'      => 'data_retention_days',
			'title'   => __( 'Data Retention (Days)', 'zontact' ),
			'desc'    => __( 'How many days to keep saved messages before automatic deletion. This helps with GDPR compliance.', 'zontact' ),
			'type'    => 'number',
			'default' => 30,
			'min'     => 1,
			'max'     => 365,
			'tab'     => 'storage',
			'section' => __( 'Database Storage', 'zontact' ),
		);

		//endregion Storage

		/**
		 * Filter the settings array before registration.
		 *
		 * @param array $settings Array of setting definitions.
		 * @param array $tabs     Array of tab definitions.
		 */
		$settings = apply_filters( 'zontact_settings_array', $settings, $tabs );

		/**
		 * Filter the tabs array before registration.
		 *
		 * @param array $tabs Array of tab definitions.
		 */
		$tabs = apply_filters( 'zontact_settings_tabs', $tabs );

		return array(
			'tabs'     => $tabs,
			'settings' => $settings,
		);
	}

	/**
	 * Sanitize settings input.
	 *
	 * @param array $input Raw input data.
	 * @return array Sanitized settings.
	 */
	public static function sanitize( array $input ): array {
		return Options::sanitize( $input );
	}

	/**
	 * Render a settings field based on its type.
	 *
	 * @param array $setting Setting definition.
	 * @param mixed $value   Current value.
	 * @return void
	 */
	public static function render_field( array $setting, $value ): void {
		$id    = $setting['id'];
		$type  = $setting['type'] ?? 'text';
		$name  = 'zontact_options[' . esc_attr( $id ) . ']';
		$desc  = $setting['desc'] ?? '';
		$attrs = array();

		if ( isset( $setting['min'] ) ) {
			$attrs[] = 'min="' . esc_attr( $setting['min'] ) . '"';
		}
		if ( isset( $setting['max'] ) ) {
			$attrs[] = 'max="' . esc_attr( $setting['max'] ) . '"';
		}

		$attrs_str = ! empty( $attrs ) ? ' ' . implode( ' ', $attrs ) : '';

		switch ( $type ) {
			case 'checkbox':
				$checked = checked( $value, true, false );
				// Add hidden input to ensure unchecked checkboxes are submitted
				printf(
					'<input type="hidden" name="%1$s" value="0">',
					esc_attr( $name )
				);
				printf(
					'<label><input type="checkbox" name="%1$s" id="%2$s" value="1" %3$s> %4$s</label>',
					esc_attr( $name ),
					esc_attr( 'zontact_options_' . $id ),
					wp_kses_post( $checked ),
					esc_html( $desc )
				);
				break;			

			case 'select':
				$choices = $setting['choices'] ?? array();
				printf(
					'<select name="%1$s" id="%2$s">',
					esc_attr( $name ),
					esc_attr( 'zontact_options_' . $id )
				);
				foreach ( $choices as $choice_value => $choice_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $choice_value ),
						selected( $value, $choice_value, false ),
						esc_html( $choice_label )
					);
				}
				echo '</select>';
				if ( $desc ) {
					echo '<p class="description">' . esc_html( $desc ) . '</p>';
				}
				break;

			case 'textarea':
				printf(
					'<textarea name="%1$s" class="large-text" rows="3">%2$s</textarea>',
					esc_attr( $name ),
					esc_textarea( $value )
				);
				if ( $desc ) {
					echo '<p class="description">' . esc_html( $desc ) . '</p>';
				}
				break;

			case 'number':
				printf(
					'<input type="number" class="small-text" name="%1$s" id="%2$s" value="%3$s"%4$s>',
					esc_attr( $name ),
					esc_attr( 'zontact_options_' . $id ),
					esc_attr( $value ),
					wp_kses_post( $attrs_str )
				);
				if ( $desc ) {
					echo '<p class="description">' . esc_html( $desc ) . '</p>';
				}
				break;

			case 'color':
				printf(
					'<input type="color" name="%1$s" id="%2$s" value="%3$s" class="zontact-color-picker">',
					esc_attr( $name ),
					esc_attr( 'zontact_options_' . $id ),
					esc_attr( $value )
				);
				if ( $desc ) {
					echo '<p class="description">' . esc_html( $desc ) . '</p>';
				}
				break;

			case 'email':
			case 'text':
			default:
				$input_type = ( 'email' === $type ) ? 'email' : 'text';
				printf(
					'<input type="%1$s" class="regular-text" name="%2$s" id="%3$s" value="%4$s">',
					esc_attr( $input_type ),
					esc_attr( $name ),
					esc_attr( 'zontact_options_' . $id ),
					esc_attr( $value )
				);
				if ( $desc ) {
					echo '<p class="description">' . esc_html( $desc ) . '</p>';
				}
				break;
		}

		/**
		 * Fires after rendering a setting field.
		 *
		 * @param array $setting Setting definition.
		 * @param mixed $value   Current value.
		 */
		do_action( 'zontact_after_render_setting', $setting, $value );
	}

	/**
	 * Render the plugin settings page with tabs.
	 *
	 * @return void
	 */
	public static function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings_data = self::get_settings();
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
						// Check if all fields in this section are conditional
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
												<?php self::render_field( $setting, $value ); ?>
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
