<?php

// app/Http/Controllers/CartController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        // Validation can be added here based on your requirements
        // return 21;
        // return auth()->user();
        $cart = Cart::firstOrCreate(['customer_id' => auth()->user()->id]);

        $product = Product::findOrFail($request->product_id);
        $quantity = (int) $request->quantity;

        // Check if the product already exists in the cart, update quantity
        $cartItem = $cart->items()->where('product_id', $product->id)->first();
        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Add the product to the cart with the specified quantity
            $cartItem = new CartItem([
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
            // return $product->product_id;
            $cart->items()->save($cartItem);
        }

        return response()->json(['message' => 'Product added to cart successfully']);
    }

    public function updateCartItem(Request $request, $cartItemId)
    {
        // Validation can be added here based on your requirements
        $cartItem = CartItem::findOrFail($cartItemId);
        $quantity = (int) $request->quantity;

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return response()->json(['message' => 'Cart item updated successfully', "quantity"=> $cartItem->quantity]);
    }

    public function removeFromCart($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cartItem->delete();

        return response()->json(['message' => 'Product removed from cart successfully']);
    }

    public function getCart()
    {
        $cart = Cart::query()
        ->with([
            'items.product.product_name', 
            'items.product.prices' => function($query) {
                $query->where('price_category_id', 4);
            },
        ])
            ->where('customer_id', auth()->user()->id)->first();
        return response()->json($cart);
    }

    public function clearCart()
    {
        $cart = Cart::where('customer_id', auth()->user()->id)->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }

        return response()->json(['message' => 'Cart cleared successfully']);
    }
}
