<?php

namespace App\Http\Repository;

use Illuminate\Support\Facades\DB;
use App\Http\Model\Distance;

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
        $this->distanceModel->start_latitude = $data['startLatitude'];
        $this->distanceModel->start_longitude = $data['startLongitude'];
        $this->distanceModel->end_latitude = $data['endLatitude'];
        $this->distanceModel->end_longitude = $data['endLongitude'];
        $this->distanceModel->distance = $data['total_distance'];
        $this->distanceModel->save();

        return (int) DB::getPdo()->lastInsertId();
    }

    public function processCreate($data = [], $distanceResult = []) {
        //inserting data in distance table
        $distanceID = $distanceResult['distance_id'];
        if ($distanceID === 0) {
            $data['total_distance'] = $distanceResult['total_distance'];

            $distanceID = $this->create($data);
        }

        return $distanceID;
    }
}