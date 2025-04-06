<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrderResource;
use App\Http\Resources\V1\OrderCollection;
use Illuminate\Http\Request;
use App\Filters\V1\OrderFilter;
use App\Http\Requests\V1\BulkStoreOrderRequest;
use Illuminate\Support\Arr;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new OrderFilter();
        $queryItems = $filter->transform($request); //['column', 'operator', 'value']

        if (count($queryItems) == 0) {
            return new OrderCollection(Order::paginate());    
        } else {
            //ensure that paginate has filters
            $orders = Order::where($queryItems)->paginate();
            return new OrderCollection($orders->appends($request->query())); 
        }
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        //
    }

    /**
     * Bulk store orders
     */

    public function bulkStore(BulkStoreOrderRequest $request)
    {
        $bulk = collect($request->all())->map(function($arr, $key) {
            // removemos os campos em camel case pois já vao se tratados e passados para o formato da db
            return Arr::except($arr, ['customerId', 'creationDate', 'paidDate']);
        });

        Order::insert($bulk->toArray());
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
