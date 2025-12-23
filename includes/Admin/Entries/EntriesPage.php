<?php
/**
 * Admin entries page.
 *
 * @package ThirtyEightZo\Zontact\Admin
 */

namespace ThirtyEightZo\Zontact\Admin\Entries;

use ThirtyEightZo\Zontact\Repository\DbEntryRepository;
use ThirtyEightZo\Zontact\Repository\EntryRepositoryInterface;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and renders the entries list screen.
 */
final class EntriesPage {

	/**
	 * Repository used to fetch data.
	 *
	 * @var EntryRepositoryInterface
	 */
	private EntryRepositoryInterface $repository;

	/**
	 * Constructor.
	 *
	 * @param EntryRepositoryInterface|null $repository Optional repository; defaults to DB repository.
	 */
	public function __construct( EntryRepositoryInterface $repository = null ) {
		$this->repository = $repository ?: new DbEntryRepository();
	}

	/**
	 * Add submenu page under Zontact.
	 *
	 * @return void
	 */
	public function add_menu(): void {
		$capability = 'manage_options';
		$menu_slug  = zontact_top_level_menu_slug();

		$hook = add_submenu_page(
			$menu_slug,
			__( 'Entries', 'zontact' ),
			__( 'Entries', 'zontact' ),
			$capability,
			'zontact-entries',
			[ $this, 'render' ]
		);

		add_action( "load-{$hook}", [ $this, 'load_screen' ] );
	}

	/**
	 * Screen load handler: set screen options if needed.
	 *
	 * @return void
	 */
	public function load_screen(): void {
		// Handle CSV export for Pro users
		$this->maybe_handle_export();
	}

	/**
	 * Handle CSV export if requested (Pro only).
	 *
	 * @return void
	 */
	private function maybe_handle_export(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['action'] ) || 'export_csv' !== $_GET['action'] ) {
			return;
		}

		// Verify nonce
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'zontact_export_csv' ) ) {
			wp_die( esc_html__( 'Invalid security token.', 'zontact' ) );
		}

		// Check capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export entries.', 'zontact' ) );
		}

		// Pro check
		$is_pro = function_exists( 'zontact_is_pro' ) && zontact_is_pro();
		if ( ! $is_pro ) {
			wp_die( esc_html__( 'CSV export is a Pro feature. Please upgrade to unlock this functionality.', 'zontact' ) );
		}

		// Get all entries (no pagination for export)
		$entries = $this->repository->get_all_for_export();

		// Generate CSV
		$this->generate_csv( $entries );
		exit;
	}

	/**
	 * Generate and download CSV file.
	 *
	 * @param array $entries Array of entry data.
	 * @return void
	 */
	private function generate_csv( array $entries ): void {
		// Set headers for CSV download
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=zontact-entries-' . gmdate( 'Y-m-d-His' ) . '.csv' );
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

	/**
	 * Render the page output.
	 *
	 * @return void
	 */
	public function render(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'zontact' ) );
		}

		$is_pro = function_exists( 'zontact_is_pro' ) && zontact_is_pro();

		$list_table = new EntriesListTable( $this->repository );
		$list_table->prepare_items();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search_value = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html__( 'Zontact Entries', 'zontact' ); ?></h1>

			<?php if ( $is_pro ) : ?>
				<?php
				$export_url = wp_nonce_url(
					add_query_arg(
						[
							'page'   => 'zontact-entries',
							'action' => 'export_csv',
						],
						admin_url( 'admin.php' )
					),
					'zontact_export_csv'
				);
				?>
				<a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action">
					<span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
					<?php esc_html_e( 'Export CSV', 'zontact' ); ?>
				</a>
			<?php else : ?>
				<button 
					type="button" 
					class="page-title-action" 
					onclick="alert('<?php echo esc_js( __( 'CSV export is a Pro feature. Please upgrade to unlock this functionality.', 'zontact' ) ); ?>')"
					style="opacity: 0.6; cursor: not-allowed;"
				>
					<span class="dashicons dashicons-download" style="margin-top: 3px;"></span>
					<?php esc_html_e( 'Export CSV', 'zontact' ); ?>
					<span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: 600; margin-left: 4px;">PRO</span>
				</button>
			<?php endif; ?>

			<hr class="wp-header-end" />

			<?php if ( ! $is_pro ) : ?>
				<div class="notice notice-info" style="margin-top: 20px;">
					<p>
						<strong><?php esc_html_e( 'Want to export your entries?', 'zontact' ); ?></strong>
						<?php
						printf(
							/* translators: %s: upgrade link */
							esc_html__( 'Upgrade to Zontact Pro to unlock CSV export and other powerful features. %s', 'zontact' ),
							'<a href="' . esc_url( admin_url( 'admin.php?page=zontact-pricing' ) ) . '">' . esc_html__( 'Learn more', 'zontact' ) . '</a>'
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<form method="post">
				<?php wp_nonce_field( 'bulk-' . $list_table->_args['plural'] ); ?>
				<?php $list_table->search_box( __( 'Search entries', 'zontact' ), 'zontact-entry' ); ?>
				<?php $list_table->display(); ?>
			</form>
		</div>
		<?php
	}
}
