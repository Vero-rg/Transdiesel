<?php

namespace App\Http\Requests\Admin;

use App\Models\Brand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
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
        $brand = $this->route('brand');
        $brandId = $brand instanceof Brand ? $brand->id : null;

        return [
            'name' => [
                'required', 'string', 'max:120',
                Rule::unique('brands', 'name')->ignore($brandId),
            ],
            'slug' => [
                'nullable', 'string', 'max:140', 'alpha_dash',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'is_active' => ['nullable'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif,svg', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'logo' => 'logo',
            'is_active' => 'estado',
        ];
    }
}
