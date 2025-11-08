<?php
/**
 * Default email template for Zontact submissions.
 *
 * Variables available in scope:
 * - $email_data (array): Normalized submission payload.
 * - $email_options (array): Plugin options.
 *
 * @package ThirtyEightZo\Zontact
 */

defined( 'ABSPATH' ) || exit;

$name        = isset( $email_data['name'] ) ? esc_html( $email_data['name'] ) : '';
$email       = isset( $email_data['email'] ) ? esc_html( $email_data['email'] ) : '';
$message_raw = isset( $email_data['message'] ) ? (string) $email_data['message'] : '';
$message     = wpautop( wp_kses_post( $message_raw ) );
$submitted   = isset( $email_data['submitted_at'] ) ? esc_html( $email_data['submitted_at'] ) : esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) );

?><!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo esc_html__( 'New Zontact message', 'zontact' ); ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f6f7f9; margin: 0; padding: 24px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 6px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        h1 { font-size: 20px; margin-bottom: 16px; color: #111827; }
        .meta { margin-bottom: 16px; color: #4b5563; font-size: 14px; }
        .meta strong { color: #111827; }
        .message { border-top: 1px solid #e5e7eb; padding-top: 16px; color: #111827; font-size: 15px; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo esc_html__( 'You have a new message', 'zontact' ); ?></h1>
        <div class="meta">
            <p><strong><?php esc_html_e( 'Name:', 'zontact' ); ?></strong> <?php echo esc_html( $name ); ?></p>
            <p><strong><?php esc_html_e( 'Email:', 'zontact' ); ?></strong> <?php echo esc_html( $email ); ?></p>
            <p><strong><?php esc_html_e( 'Submitted:', 'zontact' ); ?></strong> <?php echo esc_html( $submitted ); ?></p>
        </div>
        <div class="message">
            <strong><?php esc_html_e( 'Message', 'zontact' ); ?></strong>
            <?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    </div>
</body>
</html>

