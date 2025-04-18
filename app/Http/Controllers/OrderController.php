<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Mail\TrackingNumberShipped;
use Illuminate\Support\Facades\Mail;
use App\Mail\PackageReadyForPickup;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('client')->get();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shein_order_number' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        return Order::create($validatedData);
    }

    public function show(Order $order)
    {
        return $order->load('client');
    }

    public function update(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'shein_order_number' => 'required|string',
            'amount' => 'required|numeric',
            'tracking_number' => 'nullable|string',
            'status' => 'required|string',
        ]);

        $order->update($validatedData);

        return $order;
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }

    public function processPayment(Request $request, Order $order)
    {
        $order->update(['status' => 'paid']);

        return response()->json(['message' => 'Payment processed']);
    }

    public function addTrackingNumber(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            'tracking_number' => 'required|string',
        ]);
    
        $order->update($validatedData);
    
        Mail::to($order->client->email)->send(new TrackingNumberShipped($order));
    
        return $order;
    }

    public function markForPickup(Request $request, Order $order)
    {
        $order->update(['status' => 'ready_for_pickup']);

        Mail::to($order->client->email)->send(new PackageReadyForPickup($order));
    
        return $order;
    }
}