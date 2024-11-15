<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ProductCreated;
use App\Events\ProductUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Notifications\LowStockNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Info(
 *      title="ProductController",
 *      description="Product Controller",
 *      version="1",
 *  )
 * @OA\Tag(
 *     name="Products",
 *     description="API endpoints for managing products"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get a list of products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="Filter products by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         required=false,
     *         description="Field to sort by (name, price, created_at, updated_at)",
     *         @OA\Schema(type="string", enum={"name", "price", "created_at", "updated_at"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         required=false,
     *         description="Order of sorting (asc or desc)",
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::with('categories');

        // Filtering
        if ($request->has('category_id')) {
            $products->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Sorting
        if ($request->has('sort_by')) {
            $products->orderBy($request->sort_by, $request->sort_order ?? 'asc');
        }

        return ProductResource::collection($products->paginate(10));
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * @param ProductRequest $request
     * @return ProductResource
     */
    public function store(ProductRequest $request): ProductResource
    {
        $product = Product::create($request->validated());

        $product->categories()->attach($request->categories);

        event(new ProductCreated($product));

        return new ProductResource($product);
    }

    /**
     * @OA\Get(
     * path="/api/products/{id}",
     * summary="Get a single product",
     * tags={"Products"},
     * @OA\Parameter(
     * name="id",
     * in="path",
     * required=true,
     * description="ID of the product to retrieve",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful response",
     * @OA\JsonContent(ref="#/components/schemas/ProductResource")
     * ),
     * @OA\Response(
     *  response=404,
     *  description="Product not found",
     *      @OA\JsonContent(
     *          @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     * @param Product $product
     * @return ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product->load('categories'));
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     * @param ProductRequest $request
     * @param $id
     * @return ProductResource
     */
    public function update(ProductRequest $request, $id): ProductResource
    {
        $product = Product::findOrFail($id);

        $product->update($request->validated());

        event(new ProductUpdated($product));

        $product->categories()->sync($request->categories);

        $this->sendLowStockNotification($product);

        return new ProductResource($product);
    }

    private function sendLowStockNotification(Product $product): void
    {
        if ($product->stock < 10) {
            $adminUser = Auth::user();
            $adminUser->notify(new LowStockNotification($product));
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     * @param $id
     * @return Response
     */
    public function destroy($id): Response
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->noContent();
    }

    /**
     * @OA\Post(
     *     path="/api/products/search",
     *     summary="Search for products",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="query", type="string", description="Search query")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No products found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     * @param SearchRequest $request
     * @return JsonResponse|AnonymousResourceCollection
     */
    public function search(SearchRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $searchQuery = $request->input('query');

        $products = Product::where('name', 'LIKE', "%{$searchQuery}%")
            ->orWhere('description', 'LIKE', "%{$searchQuery}%")
            ->paginate(10);

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found matching your search criteria.'], 404);
        }

        return ProductResource::collection($products);
    }
}
