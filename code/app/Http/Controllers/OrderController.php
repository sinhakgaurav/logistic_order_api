<?php

namespace App\Http\Controllers;

use App\Http\Models\Order;
use App\Http\Requests\OrderListRequest;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Response\Response;
use App\Http\Services\OrderProcessing as OrderProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

class OrderController extends Controller
{
    /**
     * @var \App\Http\Services\OrderProcessing
     */
    protected $orderProcessingService;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param \App\Http\Repository\Order $orderRepository
     * @param Response                 $response
     */
    public function __construct(
        OrderProcessingService $orderProcessingService,
        Response $response
    ) {
        $this->orderProcessingService = $orderProcessingService;
        $this->response = $response;
    }

    /**
     * Places a new order
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(OrderStoreRequest $request)
    {
        try {
            if ($model = $this->orderProcessingService->processOrder($request)) {
                $formattedResponse = $this->response->formatOrderAsResponse($model);

                return $this->response->setSuccessResponse($formattedResponse);
            } else {
                $messages = $this->orderProcessingService->error;
                $errorCode = $this->orderProcessingService->errorCode;

                return $this->response->setError($messages, $errorCode);
            }

        } catch (Exception $e) {
            return $this->response->sendError($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Updates an order
     *
     * @param OrderUpdateRequest $request
     * @param int                $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            if(!is_numeric($id)) {
                return $this->response->setError('Invalid Order Id', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $order = Order::findOrFail($id);

            if ($order->status !== Order::UNASSIGNED_ORDER_STATUS) {
                return $this->response->setError('Order already Taken', JsonResponse::HTTP_CONFLICT);
            }

            if ($this->orderProcessingService->updateOrder($id)) {
                return $this->response->setSuccess('SUCCESS', JsonResponse::HTTP_OK);
            } else {
                return $this->response->setError('Order already Taken', JsonResponse::HTTP_CONFLICT);
            }
        } catch (\Exception $e) {
            return $this->response->setError('Unable to process.', JsonResponse::HTTP_EXPECTATION_FAILED);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function list(OrderListRequest $request)
    {
        try {
            $page = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 1);

            $records = $this->orderProcessingService->getList($page, $limit);

            if(!empty($records)) {
                $orders = [];

                foreach ($records as $record) {
                    $orders[] = $this->response->formatOrderAsResponse($record);
                }
            }

            if (!empty($orders)) {
                return $this->response->setSuccessResponse($orders);
            } else {
                return $this->response->setError('No Content Found', JsonResponse::HTTP_NO_CONTENT);
            }
        } catch (Exception $exception) {
            return $this->response->setError($exception->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
