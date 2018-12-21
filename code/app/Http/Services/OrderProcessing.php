<?php
namespace App\Http\Services;

use App\Http\Repository\Distance as DistanceRepository;
use App\Http\Repository\Order as OrderRepository;
use App\Validators\DistanceValidator;
use Illuminate\Http\JsonResponse;

class OrderProcessing
{
	/**
     * @var null|string
     */
    public $error = null;

    /**
     * @var int
     */
    public $errorCode;

    /**
     * @var DistanceValidator
     */
    protected $distanceValidator;

    /**
     * @var \App\Http\Repository\Order
     */
    protected $orderRepository;

    /**
     * @var \App\Http\Repository\Distance
     */
    protected $distanceRepository;

    public function __construct(
    	DistanceValidator $distanceValidator,
    	OrderRepository $orderRepository,
    	DistanceRepository $distanceRepository
    ) {
        $this->distanceValidator = $distanceValidator;
        $this->orderRepository = $orderRepository;
        $this->distanceRepository = $distanceRepository;
    }

    public function processOrder($requestData) {
    	$initialLatitude = $requestData->origin[0];
        $initialLongitude = $requestData->origin[1];
        $finalLatitude = $requestData->destination[0];
        $finalLongitude = $requestData->destination[1];

        $validateDistanceParameter = $this->distanceValidator
            ->validate($initialLatitude,
                $initialLongitude,
                $finalLatitude,
                $finalLongitude
            );

        if (!$validateDistanceParameter) {
            $this->error = $this->distanceValidator->getError();
            $this->errorCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

            return false;
        }

        $distance = $this->distanceRepository->getDistance(
        	$initialLatitude,
        	$initialLongitude,
        	$finalLatitude,
            $finalLongitude
        );

        if(!$distance instanceof \App\Http\Models\Distance) {
            $this->error = $distance;
            $this->errorCode = JsonResponse::HTTP_BAD_REQUEST;

            return false;
        }

        return $this->orderRepository->createOrder($distance);
    }

    public function getList($page, $limit) {
    	$records = $this->orderRepository->getList($page, $limit);

    	if(!empty($records)) {
    		$orders = [];

            foreach ($records as $record) {
                $orders[] = $this->response->formatOrderAsResponse($record);
            }
        }

        return $orders;
    }

    public function updateOrder($id) {
    	$affected = $this->orderRepository->update($order);

    	if(!$affected) {
    		return false;
    	}

    	return true;
    }
}