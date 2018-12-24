<?php

namespace App\Http\Repository;

use App\Http\Models\Distance as DistanceModel;
use Illuminate\Database\Eloquent\Model;

class Distance
{
	public function getDistance(
        $initialLatitude,
        $initialLongitude,
        $finalLatitude,
        $finalLongitude
    ) {
        $model = new DistanceModel;

        return $model->getOrSetDistance($initialLatitude, $initialLongitude, $finalLatitude, $finalLongitude);
    }
}
