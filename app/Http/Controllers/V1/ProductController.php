<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product =  Product::with('category')->latest()->paginate(10);
        return ProductResource::collection($product);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:80',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|max:4000',
            'slug' => 'unique:products,slug',
        ]);

        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'stock' => $request->stock,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => new ProductResource($product),
            'message' => 'Product created successfully.'
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource(
            $product->load('category')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|integer|min:0',
            'stock' => 'sometimes|integer|min:0',
            'description' => 'nullable|string|max:4000',
        ]);

        $product->update([
            'category_id' => $request->category_id ?? $product->category_id,
            'name' => $request->name ?? $product->name,
            'slug' => $request->name
                ? Str::slug($request->name)
                : $product->slug,
            'price' => $request->price ?? $product->price,
            'stock' => $request->stock ?? $product->stock,
            'description' => $request->description ?? $product->description,
            'image' => $request->image ?? $product->image,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => new ProductResource($product),
            'message' => 'Product updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}
