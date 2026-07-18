<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreShippingZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'zone_name' => ['required', 'string', 'max:255'],
            'districts' => ['required', 'string'],
            'base_cost' => ['required', 'numeric', 'min:0'],
            'per_kg_cost' => ['required', 'numeric', 'min:0'],
            'estimated_days' => ['required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
