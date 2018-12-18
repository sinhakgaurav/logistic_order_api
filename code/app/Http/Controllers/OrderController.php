<?php

namespace App\Http\Controllers;

use App\Common\Common;
use App\Common\Distance;
use App\Common\Validator;
use App\Distance;
use App\Orders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use App\Repository\OrderRepository;
use App\Repository\DistanceRepository;


class OrderController extends Controller
{
    protected $common;
    protected $orderRepository;
    protected $distanceRepository;

    public function __construct(
        Common $common,
        OrderRepository $orderRepository,
        DistanceRepository $distanceRepository
    ) {
        $this->common = $common;
        $this->orderRepository = $orderRepository;
        $this->distanceRepository = $distanceRepository;
    }

    public function orders(Request $request)
    {
        if (!isset($request->limit) || !isset($request->page)) {
            return response()->json([
                'error' => $this->common->getMessages('REQUEST_PARAMETER_MISSING'),
            ], 406);
        }

        if (!is_numeric($request->input('limit')) || !is_numeric($request->input('page'))) {
            return response()->json([
                'error' => $this->common->getMessages('INVALID_PARAMETER_TYPE'),
            ], 406);
        }

        if ($request->limit < 1 || $request->page < 1) {
            return response()->json([
                'error' => $this->common->getMessages('INVALID_PARAMETERS'),
            ], 406);
        }

        if(isset($request->limit))
            $limit = $request->limit;

        $offset = 0;
        if(isset($request->page))
            $offset = ($request->page - 1) * $limit;

        $orderRepository = new OrderRepository;
        $orders = $orderRepository->paginate($limit, $offset);

        if (count($orders) == 0) {
            return response()->json(['error' => $this->common->getMessages('NO_DATA_FOUND')],
             204);
        }

        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        if(!isset($request->origin) || !isset($request->destination) || empty($request->origin) || empty($request->destination) || count($request->origin) <> 2 || count($request->destination) <> 2) {
            return response()->json([
                'error' => $this->common->getMessages('INVALID_PARAMETERS'),
            ], 406);
        }

        $startLatitude = $request->origin[0];
        $startLongitude = $request->origin[1];
        $endLatitude = $request->destination[0];
        $endLongitude = $request->destination[1];

        //validating input parameters
        $validatorObj = new Validator;
        $validate = $validatorObj->validateInputParameters($startLatitude, $startLongitude, $endLatitude, $endLongitude);
        if('failed' === $validate['status']) {
            return response()->json([
                'error' => $this->common->getMessages($validate['error']),
            ], 406);
        }

        $distanceObj = new Distance;
        $distanceResult = $distanceObj->calculateDistance($startLatitude, $startLongitude, $endLatitude, $endLongitude);

        if( !is_int($distanceResult['total_distance'])) {
            return response()->json(['error' => $this->common->getMessages($distanceResult['total_distance'])],
             400);
        }

        //inserting data in distance table
        $distanceID = $distanceResult['distance_id'];
        if ($distanceID === 0) {
            $distance = new Distance;
            $distance->start_latitude = $startLatitude;
            $distance->start_longitude = $startLongitude;
            $distance->end_latitude = $endLatitude;
            $distance->end_longitude = $endLongitude;
            $distance->distance = $distanceResult['total_distance'];
            $distance->save();
            $distanceID = (int) DB::getPdo()->lastInsertId();
        }

        //inserting data in orders table
        $order = new Orders;
        $order->distance_id = $distanceID;
        $order->status = 'UNASSIGN';

        if ($order->save()) {
            return response()->json([
                'id' => $order->id,
                'distance' => $distanceResult['total_distance'],
                'status' => $this->common->getMessages('unassign')
            ], 200);
        }

        return response()->json(['error' => $this->common->getMessages('invalid_data')], 406);
    }

    public function update(Request $request, $id)
    {
        if (!isset($request->status) || 'TAKEN' !== $request->status) {
            return response()->json(['error' => $this->common->getMessages('status_is_invalid')],
             406);
        }

        $order = DB::table('orders')->where([
                    ['id', '=', $id],
                ])->get();

        if(0 == count($order)) {
            return response()->json(['error' => $this->common->getMessages('invalid_id')], 406);
        }

        if ('TAKEN' === $order[0]->status) {
           return response()->json(['error' => $this->common->getMessages('order_taken')], 409);
        }

        $affected = DB::table('orders')
        ->where([
            ["orders.id", '=', $id],
            ['status', '=', 'UNASSIGN'],
        ])
        ->update(['orders.status' => 'TAKEN']);

        if($affected) {
            return response()->json(['status' => $this->common->getMessages('success')], 200);
        }

        return response()->json(['error' => $this->common->getMessages('order_taken')], 409);
    }

}
