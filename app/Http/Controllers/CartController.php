<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartService $cartService)
    {
        dd($cartService);
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
    public function store(Request $request, CartService $cartService, Product $product)
    {
        $request->mergeIfMissing([
            'quantity' => 1
        ]);

        $data = $request->validate([
            'option_ids' => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $cartService->addItemToCart($product, $data['quantity'], $data['option_ids']);

        return back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartService $cartService, Product $product)
    {
        $request->validate([
            'quantity' => ['integer', 'min:1']
        ]);

        $optionIds = $request->input('option_ids');
        $quantity = $request->input('quantity');

        $cartService->updateItemQuantity($product->id, $quantity, $optionIds);

        return back()->with('success', 'Quantity was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CartService $carService, Product $product)
    {
        //
    }
}
