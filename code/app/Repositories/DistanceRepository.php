<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;
use App\Distance;

/**
 * Repository for the Database interaction for the distance module related operations
 */
class DistanceRepository implements RepositoryContract
{
    private $distanceModel;

    function __construct(AppModel $distanceModel) {
        $this->distanceModel = $distanceModel;
    }
    public function paginate() {
        #no body required for now
    }

    public function find($conditions=[]){
        return $this->distanceModel->where($conditions)->get();
    }

    public function update($data=[], $conditions=[]){
        #no body required for now
    }

    public function create($data=[]){
        
    }
}