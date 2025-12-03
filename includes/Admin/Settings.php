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
				printf(
					'<label><input type="checkbox" name="%1$s" value="1" %2$s> %3$s</label>',
					esc_attr( $name ),
					wp_kses_post( $checked ),
					esc_html( $desc )
				);
				break;			

			case 'select':
				$choices = $setting['choices'] ?? array();
				echo '<select name="' . esc_attr( $name ) . '">';
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
					'<input type="number" class="small-text" name="%1$s" value="%2$s"%3$s>',
					esc_attr( $name ),
					esc_attr( $value ),
					wp_kses_post( $attrs_str )
				);
				if ( $desc ) {
					echo '<p class="description">' . esc_html( $desc ) . '</p>';
				}
				break;

			case 'color':
				printf(
					'<input type="color" name="%1$s" value="%2$s" class="zontact-color-picker">',
					esc_attr( $name ),
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
					'<input type="%1$s" class="regular-text" name="%2$s" value="%3$s">',
					esc_attr( $input_type ),
					esc_attr( $name ),
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
						<div class="zontact-settings-section">
							<h2><?php echo esc_html( $section_name ); ?></h2>
							<table class="form-table" role="presentation">
								<tbody>
									<?php foreach ( $section_settings as $setting ) : ?>
										<?php
										$setting_id = $setting['id'];
										$value      = isset( $options[ $setting_id ] ) ? $options[ $setting_id ] : ( $setting['default'] ?? '' );
										?>
										<tr>
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
		</style>
		<?php
	}
}
