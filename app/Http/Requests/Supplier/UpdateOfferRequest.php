<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth('supplier')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'delivery_time' => 'required|date|after:today',
            'terms' => 'required|string|min:10|max:1000',
            'discount' => 'nullable|numeric|min:0|max:' . ($this->price ?? 0),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price must be greater than 0.',
            'price.max' => 'Price cannot exceed 999,999.99.',
            'delivery_time.required' => 'Delivery time is required.',
            'delivery_time.date' => 'Delivery time must be a valid date.',
            'delivery_time.after' => 'Delivery time must be a future date.',
            'terms.required' => 'Terms and conditions are required.',
            'terms.min' => 'Terms must be at least 10 characters.',
            'terms.max' => 'Terms cannot exceed 1000 characters.',
            'discount.numeric' => 'Discount must be a valid number.',
            'discount.min' => 'Discount cannot be negative.',
            'discount.max' => 'Discount cannot exceed the price.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'delivery_time' => 'delivery date',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->discount && $this->price && $this->discount > $this->price) {
                $validator->errors()->add('discount', 'Discount cannot exceed the price.');
            }
        });
    }
}
