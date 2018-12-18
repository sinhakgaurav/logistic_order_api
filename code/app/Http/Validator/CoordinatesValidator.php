<?php

namespace App\Http\Validator;

class CoordinatesValidator {

    /**
     * Validate Input Parameters.
     *
     * @params float $distanceParamArray['startLatitude']
     * @params float $distanceParamArray['startLongitude']
     * @params float $distanceParamArray['endLatitude']
     * @params float $distanceParamArray['endLongitude']
     *
     * @return array
     */
    public function validateInputParameters($distanceParamArray = [])
    {
        $response = ['status' => 'success'];

        if ($distanceParamArray['startLatitude'] == $distanceParamArray['endLatitude'] && $distanceParamArray['startLongitude'] == $distanceParamArray['endLongitude']) {
            return $response = ['status' => 'failed' , 'error'=> 'REQUESTED_ORIGIN_DESTINATION_SAME'];
        } elseif (!$distanceParamArray['startLatitude'] || !$distanceParamArray['startLongitude'] || !$distanceParamArray['endLatitude'] || !$distanceParamArray['endLongitude']) {
            return $response = ['status' => 'failed' , 'error'=> 'REQUEST_PARAMETER_MISSING'];
        } elseif ($$distanceParamArray['startLatitude'] < -90 || $$distanceParamArray['startLatitude'] > 90 || $distanceParamArray['endLatitude'] < -90 || $distanceParamArray['endLatitude'] > 90) {
            return $response = ['status' => 'failed' , 'error'=> 'LATITUDE_OUT_OF_RANGE'];
        } elseif ($distanceParamArray['startLongitude']< -180 || $distanceParamArray['startLongitude']> 180 || $distanceParamArray['endLongitude']< -180 || $distanceParamArray['endLongitude']> 180) {
            return $response = ['status' => 'failed' , 'error'=> 'LONGITUDE_OUT_OF_RANGE'];
        }

        return $response;
    }
}
