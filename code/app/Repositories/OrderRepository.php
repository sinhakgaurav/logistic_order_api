<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

/**
 * Repository for the Database interaction for the order module related operations
 */
class OrderRepository implements RepositoryContract
{
	
	public function paginate($limit, $start, $conditions) {
		return DB::table('orders')
                    ->join('distance', 'orders.distance_id', '=', 'distance.distance_id')
                    ->select('orders.id', 'distance.distance', 'orders.status')
                    ->orderBy('orders.id', 'asc')
                    ->skip($start)
                    ->take($limit)
                    ->get();
	}
}