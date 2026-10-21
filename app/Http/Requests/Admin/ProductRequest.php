<?php

namespace App\Http\Requests\Admin;

use App\Enums\Availability;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : null;

        return [
            'name' => ['required', 'string', 'max:180'],
            'sku' => [
                'required', 'string', 'max:60',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'slug' => [
                'nullable', 'string', 'max:190', 'alpha_dash',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'availability' => ['required', Rule::enum(Availability::class)],
            'description' => ['nullable', 'string', 'max:5000'],
            'specifications' => ['nullable', 'array'],
            'specifications.*.key' => ['nullable', 'string', 'max:80'],
            'specifications.*.value' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'sku' => 'SKU',
            'category_id' => 'categoría',
            'brand_id' => 'marca',
            'price' => 'precio',
            'availability' => 'disponibilidad',
            'description' => 'descripción',
            'image' => 'imagen',
        ];
    }
}
