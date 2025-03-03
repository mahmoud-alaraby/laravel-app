<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Http\Requests\CartRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();
        return response()->json($cartItems);
    }

    public function store(CartRequest $request)
    {
        $cartItem = Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json($cartItem, 201);
    }

    public function update(CartRequest $request, $id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->update($request->only(['quantity']));

        return response()->json($cartItem);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $request->validate(['id' => $id], ['id' => 'required|exists:carts,id']);
            $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
            $cartItem->delete();
            return response()->json(['message' => 'Item removed from cart']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Item not found in cart'], 404);
        }
    }
}
