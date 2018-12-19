<?php

namespace App\Http\Controllers;

use App\Http\Common\Distance;
use App\Http\Response\Status;
use App\Http\Validator\CoordinatesValidator;
use App\Http\Model\Orders;#needs to be replaced
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use App\Http\Repository\OrderRepository;
use App\Http\Repository\DistanceRepository;


class OrderController extends Controller
{
    protected $response;
    protected $orderRepository;
    protected $distanceRepository;
    protected $coordinatesValidator;
    protected $distance;

    public function __construct(
        Response $response,
        OrderRepository $orderRepository,
        DistanceRepository $distanceRepository,
        Validator $coordinatesValidator,
        Common $distance
    ) {
        $this->response = $response;
        $this->orderRepository = $orderRepository;
        $this->distanceRepository = $distanceRepository;
        $this->coordinatesValidator = $coordinatesValidator;
        $this->distance = $distance;
    }

    public function orders(Request $request)
    {
        if (!isset($request->limit) || !isset($request->page)) {
            return response()->json([
                'error' => $this->response->getMessages('REQUEST_PARAMETER_MISSING'),
            ], 406);
        }

        if (!is_numeric($request->input('limit')) || !is_numeric($request->input('page'))) {
            return response()->json([
                'error' => $this->response->getMessages('INVALID_PARAMETER_TYPE'),
            ], 406);
        }

        if ($request->limit < 1 || $request->page < 1) {
            return response()->json([
                'error' => $this->response->getMessages('INVALID_PARAMETERS'),
            ], 406);
        }

        if(isset($request->limit))
            $limit = $request->limit;

        $offset = 0;
        if(isset($request->page))
            $offset = ($request->page - 1) * $limit;

        $orders = $this->orderRepository->paginate($limit, $offset);

        if (count($orders) == 0) {
            return response()->json(['error' => $this->response->getMessages('NO_DATA_FOUND')],
             204);
        }

        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        if(!isset($request->origin) || !isset($request->destination) || empty($request->origin) || empty($request->destination) || count($request->origin) <> 2 || count($request->destination) <> 2) {
            return response()->json([
                'error' => $this->response->getMessages('INVALID_PARAMETERS'),
            ], 406);
        }

        $distanceParamArray = [];
        $distanceParamArray['startLatitude'] = $request->origin[0];
        $distanceParamArray['startLongitude'] = $request->origin[1];
        $distanceParamArray['endLatitude'] = $request->destination[0];
        $distanceParamArray['endLongitude'] = $request->destination[1];

        //validating input parameters
        $validate = $this->coordinatesValidator->validateInputParameters($distanceParamArray);

        if('failed' === $validate['status']) {
            return response()->json([
                'error' => $this->response->getMessages($validate['error']),
            ], 406);
        }

        $distanceResult = $this->distance->calculateDistance($distanceParamArray);

        if( !is_int($distanceResult['total_distance'])) {
            return response()->json(['error' => $this->response->getMessages($distanceResult['total_distance'])],
             400);
        }

        //inserting data in distance table
        $distanceID = $this->distanceRepository->processCreate($distanceParamArray, $distanceResult);

        $orderData = [];
        $orderData['distanceId'] = $distanceID;
        $orderData['status'] = 'UNASSIGN';
        $orderId = $this->orderRepository->processCreate($orderData);

        if ($orderId) {
            return response()->json([
                'id' => $orderId,
                'distance' => $distanceResult['total_distance'],
                'status' => $this->response->getMessages('unassign')
            ], 200);
        }

        return response()->json(['error' => $this->response->getMessages('invalid_data')], 406);
    }

    public function update(Request $request, $id)
    {
        if (!isset($request->status) || 'TAKEN' !== $request->status) {
            return response()->json(['error' => $this->response->getMessages('status_is_invalid')],
             406);
        }

        $order = DB::table('orders')->where([
                    ['id', '=', $id],
                ])->get();

        if(0 == count($order)) {
            return response()->json(['error' => $this->response->getMessages('invalid_id')], 406);
        }

        if ('TAKEN' === $order[0]->status) {
           return response()->json(['error' => $this->response->getMessages('order_taken')], 409);
        }

        $affected = DB::table('orders')
        ->where([
            ["orders.id", '=', $id],
            ['status', '=', 'UNASSIGN'],
        ])
        ->update(['orders.status' => 'TAKEN']);

        if($affected) {
            return response()->json(['status' => $this->response->getMessages('success')], 200);
        }

        return response()->json(['error' => $this->response->getMessages('order_taken')], 409);
    }

}
