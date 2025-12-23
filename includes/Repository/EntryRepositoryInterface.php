<?php
/**
 * Entry repository contract.
 *
 * @package ThirtyEightZo\Zontact\Repository
 */

namespace ThirtyEightZo\Zontact\Repository;

defined( 'ABSPATH' ) || exit;

/**
 * Defines operations for accessing Zontact entries.
 */
interface EntryRepositoryInterface {

	/**
	 * Fetch a page of entries.
	 *
	 * @param int         $page       1-based page number.
	 * @param int         $per_page   Items per page (free capped to 30).
	 * @param string|null $search     Optional search term.
	 * @return array                  Array of associative arrays representing rows.
	 */
	public function list( int $page, int $per_page, ?string $search = null ): array;

	/**
	 * Count total entries, optionally filtered by search.
	 *
	 * @param string|null $search Optional search term.
	 * @return int
	 */
	public function count( ?string $search = null ): int;

	/**
	 * Delete one or more entries by ID.
	 *
	 * @param int[] $ids Entry IDs.
	 * @return int       Number of rows deleted.
	 */
	public function delete( array $ids ): int;

	/**
	 * Get all entries for export (no pagination).
	 *
	 * @return array
	 */
	public function get_all_for_export(): array;

	/**
	 * Get specific entries by IDs for export.
	 *
	 * @param int[] $ids Entry IDs to export.
	 * @return array
	 */
	public function get_by_ids_for_export( array $ids ): array;
}
