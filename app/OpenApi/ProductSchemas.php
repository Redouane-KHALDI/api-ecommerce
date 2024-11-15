<?php

namespace App\OpenApi\Schemas;

/**
 * @OA\Schema(
 *     schema="ProductRequest",
 *     required={"name", "price"},
 *     @OA\Property(property="name", type="string", example="Sample Product"),
 *     @OA\Property(property="price", type="number", format="float", example=19.99),
 *     @OA\Property(property="description", type="string", example="This is a sample product description."),
 *     @OA\Property(property="stock", type="integer", example=100),
 *     @OA\Property(property="categories", type="array", @OA\Items(type="integer"), example={1, 2})
 * )
 */

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Sample Product"),
 *     @OA\Property(property="price", type="number", format="float", example=19.99),
 *     @OA\Property(property="description", type="string", example="This is a sample product description."),
 *     @OA\Property(property="stock", type="integer", example=100),
 *     @OA\Property(property="categories", type="array", @OA\Items(type="integer"), example={1, 2}),
 * )
 */

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API endpoints for managing products"
 * )
 */

/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Get a list of products",
 *     tags={"Products"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/ProductResource")
 *         )
 *     )
 * )
 */

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
 *         response=400,
 *         description="Invalid input"
 *     )
 * )
 */

/**
 * @OA\Get(
 *     path="/api/products/{id}",
 *     summary="Get a single product by ID",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */

/**
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     summary="Delete a product",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Product deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 */
