<?php

namespace App\Common;

class DistanceCalculator {

    /**
     * Gets the distance from google api.
     *
     * @params string $origin
     * @params string destination
     *
     * @return int
     */
    public function getDistanceMatrix($origin, $destination)
    {
        $googleApiKey = env('GOOGLE_API_KEY');

        $queryString = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=" . $origin . "&destinations=" . $destination . "&key=" . $googleApiKey;

        $cURL=curl_init();
        curl_setopt($cURL,CURLOPT_URL,$queryString);
        curl_setopt($cURL,CURLOPT_RETURNTRANSFER, TRUE);
        $cResponse=trim(curl_exec($cURL));
        curl_close($cURL);

        $data=json_decode($cResponse);

        if (!$data || $data->status == 'REQUEST_DENIED' || $data->status == 'OVER_QUERY_LIMIT' || $data->status == 'NOT_FOUND' || $data->status == 'ZERO_RESULTS') {
            return (isset($data->status))?$data->status:'GOOGLE_API_NULL_RESPONSE';
        }

        $distanceValue = (int) $data->rows[0]->elements[0]->distance->value;

        return $distanceValue;
    }
}
