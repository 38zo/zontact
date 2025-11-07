<?php
/**
 * Database schema and migrations for Zontact.
 *
 * @package ThirtyEightZo\Zontact
 */

namespace ThirtyEightZo\Zontact;

defined( 'ABSPATH' ) || exit;

/**
 * Handles database table creation and upgrades.
 */
final class Database {

	/**
	 * Current database schema version.
	 *
	 * @var string
	 */
    private const DB_VERSION = '1.1.0';

	/**
	 * Ensure the database is installed/upgraded if version mismatch.
	 *
	 * @return void
	 */
	public static function maybe_install(): void {
		$current = (string) get_option( 'zontact_db_version', '' );
		if ( $current !== self::DB_VERSION || ! self::messages_table_exists() ) {
			self::create_tables();
		}
	}

	/**
	 * Check if the messages table exists.
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @return bool
	 */
	public static function messages_table_exists(): bool {
		global $wpdb;
		$table   = self::table_messages();
		$like    = $wpdb->esc_like( $table );
		$found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $like ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return ( $found === $table );
	}

	/**
	 * Get the fully-qualified messages table name with WordPress prefix.
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @return string
	 */
	public static function table_messages(): string {
		global $wpdb;
		$default = $wpdb->prefix . 'zontact_messages';
		/**
		 * Filter the Zontact messages table name.
		 *
		 * @since 1.0.0
		 *
		 * @param string $table_name Default table name with prefix.
		 */
		return (string) apply_filters( 'zontact_table_messages', $default );
	}

	/**
	 * Create or upgrade plugin tables using dbDelta.
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @return void
	 */
	public static function create_tables(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$messages_table  = self::table_messages();

		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared -- dbDelta requires raw SQL string.
        $sql = "CREATE TABLE {$messages_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			form_key varchar(64) NOT NULL,
			name varchar(191) NULL,
			email varchar(191) NULL,
			phone varchar(50) NULL,
			subject varchar(191) NULL,
			message longtext NULL,
			meta longtext NULL,
			ip_address varchar(45) NULL,
			user_agent varchar(255) NULL,
            email_status varchar(20) NOT NULL DEFAULT 'pending',
            email_error text NULL,
            email_sent_at datetime NULL,
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY form_key (form_key),
			KEY created_at (created_at)
		) {$charset_collate};";

		dbDelta( $sql );

		/**
		 * Fires after Zontact database tables have been created or updated.
		 *
		 * @since 1.0.0
		 */
		do_action( 'zontact_db_installed' );
		// phpcs:enable

		update_option( 'zontact_db_version', self::DB_VERSION );
	}

	/**
	 * Insert a message row into the messages table.
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @param array $data Associative array of values.
	 * @return int Inserted row ID.
	 */
	public static function insert_message( array $data ): int {
		global $wpdb;
		$table = self::table_messages();

        $defaults = [
            'form_key'     => 'default',
            'name'         => null,
            'email'        => null,
            'phone'        => null,
            'subject'      => null,
            'message'      => null,
            'meta'         => null,
            'ip_address'   => null,
            'user_agent'   => null,
            'email_status' => 'pending',
            'email_error'  => null,
            'email_sent_at'=> null,
            'created_at'   => current_time( 'mysql' ),
		];

		$values = wp_parse_args( $data, $defaults );

		// Direct database write operation (INSERT) - caching not applicable for write operations.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert(
			$table,
			[
                'form_key'     => $values['form_key'],
                'name'         => $values['name'],
                'email'        => $values['email'],
                'phone'        => $values['phone'],
                'subject'      => $values['subject'],
                'message'      => $values['message'],
                'meta'         => $values['meta'],
                'ip_address'   => $values['ip_address'],
                'user_agent'   => $values['user_agent'],
                'email_status' => $values['email_status'],
                'email_error'  => $values['email_error'],
                'email_sent_at'=> $values['email_sent_at'],
                'created_at'   => $values['created_at'],
			],
			[
                '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s',
			]
		);

		$insert_id = (int) $wpdb->insert_id;
		
		// Clear cache after successful insert.
		if ( $insert_id > 0 ) {
			// Clear common cache patterns for our plugin.
			wp_cache_delete( 'zontact_entries_all', 'zontact' );
			wp_cache_delete( 'zontact_count_all', 'zontact' );
		}

		return $insert_id;
	}

    /**
     * Update the email delivery status for a stored message.
     *
     * @global \wpdb $wpdb WordPress database abstraction object.
     * @param int         $message_id Message ID.
     * @param string      $status     New status (pending|sent|failed).
     * @param string|null $error      Optional error message.
     * @param string|null $sent_at    Optional sent at datetime (Y-m-d H:i:s).
     * @return bool                   Whether the update affected a row.
     */
    public static function update_message_email_status( int $message_id, string $status, ?string $error = null, ?string $sent_at = null ): bool {
        global $wpdb;

        $table = self::table_messages();
        $data  = [
            'email_status' => $status,
            'email_error'  => $error,
            'email_sent_at'=> $sent_at,
        ];

        // Filter out null values so wpdb can handle NULL correctly.
        foreach ( $data as $key => $value ) {
            if ( null === $value ) {
                $data[ $key ] = null;
            }
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $updated = $wpdb->update(
            $table,
            $data,
            [ 'id' => $message_id ],
            [ '%s', '%s', '%s' ],
            [ '%d' ]
        );

        if ( false !== $updated ) {
            wp_cache_delete( 'zontact_entries_all', 'zontact' );
            wp_cache_delete( 'zontact_count_all', 'zontact' );
        }

        return ( false !== $updated );
    }
}


