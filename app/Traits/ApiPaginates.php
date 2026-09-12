<?php

namespace App\Traits;

use Illuminate\Pagination\Paginator;

/**
 * Trait for handling API pagination, search, and sorting
 * 
 * Usage:
 *   $data = $this->paginate($query, $request, 'nama_sekolah');
 */
trait ApiPaginates
{
    /**
     * Apply pagination, search, and sorting to a query
     * 
     * Query Parameters:
     *   - page: Page number (default: 1)
     *   - per_page: Records per page (default: 10, max: 100)
     *   - search: Search term to filter results
     *   - search_field: The column to search in (e.g., 'nama_sekolah')
     *   - sort_by: Column to sort by (default: first sortable field)
     *   - sort_dir: Sort direction 'asc' or 'desc' (default: 'asc')
     * 
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @param array $searchable Array of column names that can be searched
     * @param array $sortable Array of column names that can be sorted (default: same as $searchable)
     * @param int $defaultPerPage Default records per page
     * 
     * @return array ['data' => $items, 'pagination' => $meta]
     */
    public function paginateApiQuery($query, $request, $searchable = [], $sortable = [], $defaultPerPage = 10)
    {
        // Determine sortable fields (default to searchable if not specified)
        if (empty($sortable)) {
            $sortable = $searchable;
        }

        // Get pagination parameters
        $page = (int) $request->get('page', 1);
        $perPage = min((int) $request->get('per_page', $defaultPerPage), 100); // Max 100 per page
        $perPage = max($perPage, 1); // Min 1 per page

        // Apply search filter if provided
        if (($request->has('search') || $request->has('q')) && !empty($searchable)) {
            $searchTerm = $request->get('search') ?? $request->get('q');
            $searchField = $request->get('search_field', $searchable[0] ?? null);

            // Only search in allowed fields
            if ($searchField && in_array($searchField, $searchable)) {
                $query = $query->where($searchField, 'like', '%' . $searchTerm . '%');
            } elseif ($searchTerm) {
                // Search across all searchable fields
                $query = $query->where(function ($q) use ($searchTerm, $searchable) {
                    foreach ($searchable as $field) {
                        $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                    }
                });
            }
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', $sortable[0] ?? 'id');
        $sortDir = strtoupper($request->get('sort_dir', 'ASC'));
        $sortDir = in_array($sortDir, ['ASC', 'DESC']) ? $sortDir : 'ASC';

        // Only sort by allowed fields
        if (in_array($sortBy, $sortable)) {
            $query = $query->orderBy($sortBy, $sortDir);
        }

        // Get total before pagination
        $total = $query->count();

        // Apply pagination
        $skip = ($page - 1) * $perPage;
        $items = $query->skip($skip)->take($perPage)->get();

        // Calculate pagination metadata
        $lastPage = (int) ceil($total / $perPage);
        $from = ($total > 0) ? $skip + 1 : 0;
        $to = min($skip + $perPage, $total);

        return [
            'data' => $items,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $from,
                'to' => $to,
                'has_more' => $page < $lastPage,
            ],
        ];
    }
}
