<?php

namespace App\Http\Repository;

use App\Http\Models\Order as OrderModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class Order
{
    /**
     * @param Request $requestData
     *
     * @return Order|false
     */
    public function createOrder($dis)
    {
        //Create new record
        $order = new OrderModel();
        $order->status = OrderModel::UNASSIGNED_ORDER_STATUS;
        $order->distance_id = $distance->id;
        $order->setDistanceValue($distance->distance);
        $order->save();

        return $order;
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getList($page, $limit)
    {
        $skip = ($page -1) * $limit;

        return (new OrderModel())->with('distanceModel')->skip($skip)->take($limit)->orderBy('id', 'asc')->get();

        return $orders;
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function update($id) {
        $affected = DB::table('orders')
        ->where([
            ["orders.id", '=', $id],
            ['status', '=', OrderModel::UNASSIGNED_ORDER_STATUS],
        ])
        ->update(['orders.status' => OrderModel::ASSIGNED_ORDER_STATUS]);
        
        return $affected;
    }
}
