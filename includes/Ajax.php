<?php
/**
 * AJAX handler for Zontact.
 *
 * @package ThirtyEightZo\Zontact
 */

namespace ThirtyEightZo\Zontact;

use ThirtyEightZo\Zontact\Mail\EmailService;

defined( 'ABSPATH' ) || exit;

/**
 * Handles form submission via AJAX.
 */
final class Ajax {

	/**
	 * Register AJAX hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		$instance = new self();

		add_action( 'wp_ajax_zontact_submit', [ $instance, 'handle' ] );
		add_action( 'wp_ajax_nopriv_zontact_submit', [ $instance, 'handle' ] );
	}

	/**
	 * Handle AJAX submission.
	 *
	 * @return void
	 */
    public function handle(): void {
        // Manual nonce verification to ensure JSON error response instead of -1 die.
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
        if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'zontact_submit' ) ) {
            wp_send_json_error(
                [ 'message' => __( 'Security check failed. Please reload the page and try again.', 'zontact' ) ],
                403
            );
        }

		$name    = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
		$email   = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$message = wp_kses_post( wp_unslash( $_POST['message'] ?? '' ) );
		$website = sanitize_text_field( wp_unslash( $_POST['website'] ?? '' ) );
		$consent = ! empty( $_POST['consent'] );

		$errors = [];

		if ( empty( $name ) ) {
			$errors['name'] = __( 'Name is required.', 'zontact' );
		}
		if ( empty( $email ) || ! is_email( $email ) ) {
			$errors['email'] = __( 'A valid email address is required.', 'zontact' );
		}
		if ( empty( $message ) ) {
			$errors['message'] = __( 'Message is required.', 'zontact' );
		}
		if ( ! empty( $website ) ) {
			wp_send_json_error(
				[ 'message' => __( 'Spam detected.', 'zontact' ) ],
				400
			);
		}

        $options = Options::get();

        if ( ! empty( $options['consent_text'] ) && ! $consent ) {
			$errors['consent'] = __( 'Consent is required.', 'zontact' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( [ 'errors' => $errors ], 422 );
		}

        $submitted_at = current_time( 'mysql' );

        $entry_id = $this->store_message( $name, $email, $message, $consent, $options, $submitted_at );

        $email_result = EmailService::instance()->send_submission(
            [
                'name'        => $name,
                'email'       => $email,
                'message'     => $message,
                'consent'     => $consent,
                'form_key'    => 'default',
                'entry_id'    => $entry_id ?: null,
                'submitted_at'=> $submitted_at,
                'ip_address'  => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ),
                'user_agent'  => sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) ),
            ],
            $options
        );

        if ( $entry_id && method_exists( Database::class, 'update_message_email_status' ) ) {
            if ( $email_result['sent'] ) {
                Database::update_message_email_status( $entry_id, 'sent', null, current_time( 'mysql' ) );
            } else {
                Database::update_message_email_status( $entry_id, 'failed', $email_result['error'] ?? null, null );
            }
        }

        $response = [ 'message' => $options['success_message'] ?? __( 'Message sent successfully.', 'zontact' ) ];

        if ( ! $email_result['sent'] ) {
            /**
             * Fires when email delivery fails but the message is stored.
             *
             * @param array $email_result The result array with error details.
             * @param int|null $entry_id The stored entry ID if available.
             */
            do_action( 'zontact_email_delivery_failed', $email_result, $entry_id ?: null );

            $response['warning'] = __( 'We saved your message, but email delivery failed. Our team will review it shortly.', 'zontact' );
        }

        wp_send_json_success( $response );
	}

	/**
	 * Store message in database if enabled.
	 *
	 * @param string $name    Sender name.
	 * @param string $email   Sender email.
	 * @param string $message Message body.
     * @param bool   $consent      Consent given.
     * @param array  $options      Plugin options.
     * @param string $submitted_at Submission timestamp.
     * @return int                 Entry ID when stored, 0 otherwise.
	 */
    private function store_message( string $name, string $email, string $message, bool $consent, array $options, string $submitted_at ): int {
		if ( empty( $options['save_messages'] ) ) {
            return 0;
		}

		$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		$ua = sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ?? '' ) );

		$meta = [
			'consent' => $consent ? 'yes' : 'no',
		];

		// Allow extensions to augment stored meta before insert.
		$meta = (array) apply_filters( 'zontact_store_meta', $meta, compact( 'name', 'email', 'message', 'consent', 'options' ) );

		// Ensure table exists before attempting insert (self-healing in case activation didn't run).
		Database::maybe_install();

        return Database::insert_message(
            [
                'form_key'     => 'default',
                'name'         => $name,
                'email'        => $email,
                'phone'        => null,
                'subject'      => $options['subject'] ?? null,
                'message'      => $message,
                'meta'         => wp_json_encode( $meta ),
                'ip_address'   => $ip,
                'user_agent'   => $ua,
                'email_status' => 'pending',
                'email_error'  => null,
                'email_sent_at'=> null,
                'created_at'   => $submitted_at,
            ]
        );
	}
}
