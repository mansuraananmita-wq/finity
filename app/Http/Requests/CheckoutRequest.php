<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $phone = (string) $this->input('phone', '');
        $phone = preg_replace('/[\s\-]/', '', $phone) ?? '';
        if (str_starts_with($phone, '+880')) {
            $phone = '0'.substr($phone, 4);
        } elseif (str_starts_with($phone, '880')) {
            $phone = '0'.substr($phone, 3);
        }

        $this->merge([
            'phone' => $phone,
            'shipping_district' => trim((string) $this->input('shipping_district', '')),
            'shipping_address' => trim((string) $this->input('shipping_address', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'max:1000'],
            'shipping_district' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^01[0-9]{9}$/'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in(['cod', 'bkash', 'nagad'])],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid BD mobile number (01XXXXXXXXX).',
            'shipping_district.required' => 'Please select your delivery district.',
            'shipping_address.required' => 'Please enter your full shipping address.',
            'payment_method.required' => 'Please choose a payment method.',
        ];
    }
}
