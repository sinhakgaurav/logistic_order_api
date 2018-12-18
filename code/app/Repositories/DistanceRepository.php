<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

/**
 * Repository for the Database interaction for the distance module related operations
 */
class DistanceRepository implements RepositoryContract
{
    public function paginate() {
        #no body required for now
    }

    public function find($conditions=[]){
        
    }

    public function update($data=[], $conditions=[]){
        
    }

    public function create($data=[]){
        
    }
}