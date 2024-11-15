<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'integer|min:1|nullable',
            'description' => 'nullable|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ];
    }

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.max' => 'The product name may not be greater than 255 characters.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The product price must be a number.',
            'price.min' => 'The product price must be at least 0.01.',
            'stock.integer' => 'The stock must be an integer.',
            'stock.min' => 'The stock must be at least 1.',
            'description.string' => 'The description must be a string.',
            'categories.required' => 'At least one category is required.',
            'categories.array' => 'The categories must be an array.',
            'categories.*.exists' => 'One or more selected categories do not exist.',
        ];
    }
}
