<?php
/**
 * WP_List_Table implementation for Zontact entries.
 *
 * @package ThirtyEightZo\Zontact\Admin
 */

namespace ThirtyEightZo\Zontact\Admin\Entries;

use ThirtyEightZo\Zontact\Repository\EntryRepositoryInterface;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Entries table view.
 */
final class EntriesListTable extends \WP_List_Table {

	/**
	 * Repository for data access.
	 *
	 * @var EntryRepositoryInterface
	 */
	private EntryRepositoryInterface $repository;

	/**
	 * Constructor.
	 *
	 * @param EntryRepositoryInterface $repository Repository instance.
	 */
	public function __construct( EntryRepositoryInterface $repository ) {
		parent::__construct( [
			'plural'   => 'zontact_entries',
			'singular' => 'zontact_entry',
			'ajax'     => false,
		] );
		$this->repository = $repository;
	}

	/** @inheritDoc */
	public function get_columns(): array {
		return [
			'cb'          => '<input type="checkbox" />',
			'id'          => __( 'ID', 'zontact' ),
			'name'        => __( 'Name', 'zontact' ),
			'email'       => __( 'Email', 'zontact' ),
			'subject'     => __( 'Subject', 'zontact' ),
			'email_status'=> __( 'Email Status', 'zontact' ),
			'message'     => __( 'Message', 'zontact' ),
			'created_at'  => __( 'Date', 'zontact' ),
		];
	}

	/** @inheritDoc */
	protected function get_sortable_columns(): array {
		return [
			'id'         => [ 'id', true ],
			'created_at' => [ 'created_at', true ],
		];
	}

	/** @inheritDoc */
	protected function column_cb( $item ): string {
		return sprintf( '<input type="checkbox" name="ids[]" value="%d" />', (int) $item['id'] );
	}

	/** @inheritDoc */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return (int) $item['id'];
			case 'name':
				return esc_html( (string) $item['name'] );
			case 'email':
				return esc_html( (string) $item['email'] );
			case 'subject':
				return esc_html( (string) ( $item['subject'] ?? '' ) );
			case 'email_status':
				$status = isset( $item['email_status'] ) ? (string) $item['email_status'] : 'pending';
				$label  = __( 'Pending', 'zontact' );

				switch ( $status ) {
					case 'sent':
						$label = __( 'Sent', 'zontact' );
						break;
					case 'failed':
						$label = __( 'Failed', 'zontact' );
						break;
				}
				$output = esc_html( $label );

				if ( ! empty( $item['email_sent_at'] ) ) {
					$sent_time = strtotime( (string) $item['email_sent_at'] );
					if ( $sent_time ) {
						$output .= '<br><span class="description">' . esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $sent_time ) ) . '</span>';
					}
				}

				if ( 'failed' === $status && ! empty( $item['email_error'] ) ) {
					$output .= '<br><span class="description">' . esc_html( (string) $item['email_error'] ) . '</span>';
				}

				return $output;
			case 'message':
				return esc_html( wp_trim_words( wp_strip_all_tags( (string) $item['message'] ), 20, '…' ) );
			case 'created_at':
				$time = strtotime( (string) $item['created_at'] );
				return esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $time ) );
			default:
				return '';
		}
	}

	/** @inheritDoc */
	protected function get_bulk_actions(): array {
		$actions = [
			'delete' => __( 'Delete', 'zontact' ),
		];

		// Add export selected action for Pro users
		$is_pro = function_exists( 'zontact_is_pro' ) && zontact_is_pro();
		if ( $is_pro ) {
			$actions['export_selected'] = __( 'Export Selected (CSV)', 'zontact' );
		}

		return $actions;
	}

	/**
	 * Process bulk actions.
	 *
	 * @return void
	 */
	public function process_bulk_action(): void {
		$action = $this->current_action();

		if ( ! $action ) {
			return;
		}

		// Process delete action
		if ( 'delete' === $action && ! empty( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
			check_admin_referer( 'bulk-' . $this->_args['plural'] );
			$ids = array_map( 'intval', wp_unslash( $_POST['ids'] ) );
			$this->repository->delete( $ids );

			// Redirect to avoid resubmission
			wp_safe_redirect( remove_query_arg( [ 'action', 'action2', 'ids', '_wpnonce', '_wp_http_referer' ] ) );
			exit;
		}

		// Process export selected action (Pro only)
		if ( 'export_selected' === $action && ! empty( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
			check_admin_referer( 'bulk-' . $this->_args['plural'] );

			$is_pro = function_exists( 'zontact_is_pro' ) && zontact_is_pro();
			if ( ! $is_pro ) {
				wp_die( esc_html__( 'CSV export is a Pro feature.', 'zontact' ) );
			}

			$ids = array_map( 'intval', wp_unslash( $_POST['ids'] ) );
			$this->export_selected_entries( $ids );
			exit;
		}
	}

	/**
	 * Export selected entries to CSV (Pro only).
	 *
	 * @param array $ids Entry IDs to export.
	 * @return void
	 */
	private function export_selected_entries( array $ids ): void {
		// Use the repository method instead of direct query
		$entries = $this->repository->get_by_ids_for_export( $ids );

		if ( empty( $entries ) ) {
			wp_die( esc_html__( 'No entries found to export.', 'zontact' ) );
		}

		// Generate CSV
		$this->generate_csv_output( $entries, 'selected' );
	}

	/**
	 * Generate CSV output.
	 *
	 * @param array  $entries Array of entries.
	 * @param string $type    Export type (selected|all).
	 * @return void
	 */
	private function generate_csv_output( array $entries, string $type = 'all' ): void {
		// Set headers for CSV download
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=zontact-entries-' . $type . '-' . gmdate( 'Y-m-d-His' ) . '.csv' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Open output stream
		$output = fopen( 'php://output', 'w' );

		// Add BOM for Excel UTF-8 support
		fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) );

		// CSV Headers
		$headers = [
			__( 'ID', 'zontact' ),
			__( 'Name', 'zontact' ),
			__( 'Email', 'zontact' ),
			__( 'Subject', 'zontact' ),
			__( 'Message', 'zontact' ),
			__( 'Email Status', 'zontact' ),
			__( 'Email Sent At', 'zontact' ),
			__( 'Email Error', 'zontact' ),
			__( 'Created At', 'zontact' ),
		];

		fputcsv( $output, $headers );

		// Add data rows
		foreach ( $entries as $entry ) {
			$row = [
				$entry['id'] ?? '',
				$entry['name'] ?? '',
				$entry['email'] ?? '',
				$entry['subject'] ?? '',
				$entry['message'] ?? '',
				$entry['email_status'] ?? 'pending',
				$entry['email_sent_at'] ?? '',
				$entry['email_error'] ?? '',
				$entry['created_at'] ?? '',
			];

			fputcsv( $output, $row );
		}

		fclose( $output );
	}

	/** @inheritDoc */
	public function prepare_items() {
		$per_page  = 30;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged_raw = isset( $_GET['paged'] ) ? absint( wp_unslash( $_GET['paged'] ) ) : 1;

		$page      = max( 1, (int) $paged_raw );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search    = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : null;

		$this->_column_headers = [ $this->get_columns(), [], $this->get_sortable_columns() ];

		$this->process_bulk_action();

		$total_items = $this->repository->count( $search );
		$this->items = $this->repository->list( $page, $per_page, $search );

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => (int) ceil( $total_items / $per_page ),
		] );
	}
}
