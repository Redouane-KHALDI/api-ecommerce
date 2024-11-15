<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
