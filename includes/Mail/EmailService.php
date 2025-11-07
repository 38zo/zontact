<?php
/**
 * Email service for Zontact submissions.
 *
 * @package ThirtyEightZo\Zontact\Mail
 */

namespace ThirtyEightZo\Zontact\Mail;

defined( 'ABSPATH' ) || exit;

/**
 * Provides a reusable email sending service with hooks for integrations.
 */
final class EmailService {

    /**
     * Singleton instance.
     *
     * @var EmailService|null
     */
    private static ?EmailService $instance = null;

    /**
     * Get the singleton instance.
     */
    public static function instance(): EmailService {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Send a submission notification email.
     *
     * @param array $payload Submission data.
     * @param array $options Plugin options.
     * @return array{sent:bool,error: string|null}
     */
    public function send_submission( array $payload, array $options = [] ): array {
        $defaults = [
            'name'        => '',
            'email'       => '',
            'message'     => '',
            'form_key'    => 'default',
            'entry_id'    => null,
            'consent'     => false,
            'ip_address'  => null,
            'user_agent'  => null,
        ];

        $data = wp_parse_args( $payload, $defaults );

        /**
         * Allow developers to filter the raw payload before the email is constructed.
         *
         * @param array $data    Normalized payload data.
         * @param array $options Plugin options.
         */
        $data = apply_filters( 'zontact_email_payload', $data, $options );

        $to = $options['recipient_email'] ?? get_option( 'admin_email' );
        /**
         * Filter the email recipient.
         */
        $to = apply_filters( 'zontact_email_recipient', $to, $data, $options );

        $subject = $options['subject'] ?? __( 'New Zontact message', 'zontact' );
        $subject = apply_filters( 'zontact_email_subject', $subject, $data, $options );

        $headers = $this->build_headers( $data, $options );
        $headers = apply_filters( 'zontact_email_headers', $headers, $data, $options );

        $body = $this->render_template( $data, $options );
        $body = apply_filters( 'zontact_email_body', $body, $data, $options );

        $content_type = apply_filters( 'zontact_email_content_type', 'text/html; charset=UTF-8', $data, $options );

        /**
         * Allows integrations to hook prior to sending the email.
         */
        do_action( 'zontact_email_before_send', $data, $options );

        $content_type_filter = static function () use ( $content_type ) {
            return $content_type;
        };

        add_filter( 'wp_mail_content_type', $content_type_filter );
        $sent = wp_mail( $to, $subject, $body, $headers );
        remove_filter( 'wp_mail_content_type', $content_type_filter );

        $result = [
            'sent'  => (bool) $sent,
            'error' => $sent ? null : __( 'Unable to send email via wp_mail.', 'zontact' ),
        ];

        /**
         * Filter the email result allowing integrations to augment diagnostics.
         */
        $result = apply_filters( 'zontact_email_result', $result, $data, $options );

        do_action( 'zontact_email_after_send', $data, $options, $result );

        return $result;
    }

    /**
     * Build default email headers.
     */
    private function build_headers( array $data, array $options ): array {
        $headers = [];

        if ( ! empty( $data['name'] ) && ! empty( $data['email'] ) && is_email( $data['email'] ) ) {
            $headers[] = sprintf( 'Reply-To: %s <%s>', $data['name'], $data['email'] );
        }

        $site_domain = wp_parse_url( home_url(), PHP_URL_HOST );
        $from_email  = apply_filters( 'zontact_from_email', 'no-reply@' . $site_domain, $data, $options );
        $from_name   = apply_filters( 'zontact_from_name', wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ), $data, $options );

        if ( $from_email ) {
            $headers[] = sprintf( 'From: %s <%s>', $from_name, $from_email );
        }

        return $headers;
    }

    /**
     * Render the email body using a template that can be overridden.
     */
    private function render_template( array $data, array $options ): string {
        $template = '';

        /**
         * Allow forcing a template path via filter or extension.
         */
        $template = apply_filters( 'zontact_email_template_path', $template, $data, $options );

        if ( empty( $template ) ) {
            $theme_template = locate_template( 'zontact/email-submission.php' );
            if ( ! empty( $theme_template ) ) {
                $template = $theme_template;
            }
        }

        if ( empty( $template ) ) {
            $template = trailingslashit( ZONTACT_PATH ) . 'templates/email-submission.php';
        }

        /**
         * Final chance to adjust the template file before inclusion.
         */
        $template = apply_filters( 'zontact_email_template', $template, $data, $options );

        if ( ! file_exists( $template ) ) {
            return $this->render_fallback_text_body( $data );
        }

        ob_start();

        /** @var array $email_data */
        $email_data = $data;
        /** @var array $email_options */
        $email_options = $options;

        include $template;

        $content = ob_get_clean();

        if ( false === $content ) {
            return $this->render_fallback_text_body( $data );
        }

        return $content;
    }

    /**
     * Render a plain text fallback if the template cannot be loaded.
     */
    private function render_fallback_text_body( array $data ): string {
        $lines = [];
        $lines[] = sprintf( 'Name: %s', $data['name'] );
        $lines[] = sprintf( 'Email: %s', $data['email'] );
        $lines[] = '';
        $lines[] = 'Message:';
        $lines[] = wp_strip_all_tags( (string) $data['message'] );

        return implode( "\n", $lines );
    }
}


