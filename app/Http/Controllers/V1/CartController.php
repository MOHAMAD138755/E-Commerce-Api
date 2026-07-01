<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'Cart is empty.'
            ]);
        }

        return new CartResource($cart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $item = $cart->items()
            ->where('product_id', $request->product_id)
            ->first();

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Requested quantity exceeds stock.'
            ], 422);
        }

        if ($item) {

            $item->increment('quantity', $request->quantity);

        } else {

            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        $cart->load('items.product');

        return response()->json([
            'status' => 'success',
            'data' => new CartResource($cart),
            'message' => 'Product added to cart.'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['status' => 'error', 'message' => 'Cart item is not belongs to you.'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = $cartItem->product;

        if ($request->quantity > $product->stock) {
            return response()->json([
                'status' => 'error',
                'message' => 'Requested quantity exceeds stock.'
            ], 422);
        }

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        return response()->json([
            'status' => 'success',
            'data' => new CartItemResource(
                $cartItem->load('product')
            ),
            'message' => 'Cart item updated successfully.'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item deleted successfully.'
        ]);
    }
}
