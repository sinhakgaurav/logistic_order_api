<?php

namespace App\Http\Repository;

use Illuminate\Support\Facades\DB;

/**
 * Repository for the Database interaction for the order module related operations
 */
class OrderRepository implements RepositoryContract {

    public function paginate($limit = 10, $start = 0, $conditions) {
        return DB::table('orders')
            ->join('distance', 'orders.distance_id', '=', 'distance.distance_id')
            ->select('orders.id', 'distance.distance', 'orders.status')
            ->orderBy('orders.id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();
    }

    public function find($conditions=[]){
        
    }

    public function update($data=[], $conditions=[]){
        
    }

    public function create($data=[]){
        
    }
}
