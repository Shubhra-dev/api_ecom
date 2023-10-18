<?php

// app/Http/Controllers/EcommerceOrderController.php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\EcommerceOrder;
use App\Models\EcommerceOrderItem;
use App\Models\EcommercePayment;

class EcommerceOrderController extends Controller
{
    // Retrieve details of a specific e-commerce order
    public function getOrder($orderId)
    {
        $order = EcommerceOrder::with('orderItems', 'payments', 'customer')->find($orderId);
        return response()->json($order);
    }

    // Create a new e-commerce order
    public function createOrder(Request $request)
    {
        // Validation can be added here based on your requirements
        $orderData = $request->all();

        // Assuming the request contains customer_id, order_date, status, and other relevant data
        $order = EcommerceOrder::create($orderData);

        // Assuming the request also contains the order items data in an array format
        foreach ($orderData['order_items'] as $itemData) {
            EcommerceOrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'],
                'item_price' => $itemData['item_price'],
            ]);
        }

        // Assuming the request contains payment data
        EcommercePayment::create([
            'order_id' => $order->order_id,
            'payment_date' => $orderData['payment_date'],
            'amount' => $orderData['amount'],
            'payment_method' => $orderData['payment_method'],
        ]);

        return response()->json(['message' => 'Order created successfully', 'order_id' => $order->order_id]);
    }

    // Update the status of an e-commerce order
    public function updateOrderStatus(Request $request, $orderId)
    {
        // Validation can be added here based on your requirements
        $statusData = $request->only('status');

        $order = EcommerceOrder::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->update($statusData);

        return response()->json(['message' => 'Order status updated successfully']);
    }

    public function placeOrder(Request $request)
    {
        // Assuming you have the necessary data in the request for payment, shipping, etc.
        $paymentData = $request->only('payment_date', 'amount', 'payment_method');
        $shippingAddress = $request->only('shipping_address', 'city', 'postal_code', 'country');

        // Create the order
        $order = EcommerceOrder::create([
            'customer_id' => auth()->user()->id,
            'order_date' => now(),
            'status' => 'pending', // You can set the initial status of the order as 'pending'
            'shipping_address' => json_encode($shippingAddress),
            // Add other order-related attributes as needed
        ]);

        // Transfer cart items to the order
        $cart = Cart::with('items.product')->where('user_id', auth()->user()->id)->first();
        foreach ($cart->items as $cartItem) {
            EcommerceOrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'item_price' => $cartItem->product->price,
                // Add other item-related attributes as needed
            ]);
        }

        // Add the payment information to the order
        EcommercePayment::create([
            'order_id' => $order->order_id,
            'payment_date' => $paymentData['payment_date'],
            'amount' => $paymentData['amount'],
            'payment_method' => $paymentData['payment_method'],
            // Add other payment-related attributes as needed
        ]);

        // Clear the cart after the order is successfully placed
        $cart->items()->delete();
        $cart->delete();

        return response()->json(['message' => 'Order placed successfully.', 'order_id' => $order->order_id]);
    }
}

