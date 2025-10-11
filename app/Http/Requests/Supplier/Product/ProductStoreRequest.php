<?php

namespace App\Http\Requests\Supplier\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductStoreRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_en'           => ['required','string','max:255'],
            'name_ar'           => ['required','string','max:255'],
            'description_en'    => ['required','string','max:255'],
            'description_ar'    => ['required','string','max:255'],
            'sku'               => ['required','string','max:255' , Rule::unique('products','sku')],
            'price_before'      => ['required','numeric' , 'min:0'],
            'price_after'       => ['required','numeric' , 'min:0' , 'lte:price_before'],
            'discount_value'    => ['nullable','numeric' , 'min:0'],
            'stock'             => ['required','integer' , 'min:0'],
            'tax'               => ['nullable','numeric' , 'min:0'],
            'shipping'          => ['nullable','numeric' , 'min:0'],
            'status'            => ['required','boolean'],
            'categories'        => ['required','array'],
            'categories.*'      => ['required','exists:categories,id'],
            'attachment'        => ["required","array"],
            'attachment.*'      => ["required","image","mimes:jpeg,png,jpg,gif,svg,webp","max:2048"],
        ];
    }
}
