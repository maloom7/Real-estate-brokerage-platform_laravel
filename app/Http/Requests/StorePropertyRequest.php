<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('create', \App\Models\Property::class);
    }

    public function rules(): array
    {
        return [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|in:SAR,USD,EUR',
            'payment_type' => 'required|in:sale,rent,lease',
            'category' => 'required|in:residential,commercial,industrial,land',
            'type' => 'required|string',
            'area' => 'nullable|integer|min:0',
            'rooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'amenities' => 'nullable|array',
            'features' => 'nullable|array',
            'status' => 'required|in:draft,pending_review,approved,active',
            'visibility' => 'required|in:public,internal,vip',
            'client_id' => 'nullable|exists:clients,id'
        ];
    }

    public function messages(): array
    {
        return [
            'title_ar.required' => 'عنوان العقار مطلوب',
            'price.required' => 'السعر مطلوب',
            'city.required' => 'المدينة مطلوبة',
            'status.required' => 'حالة العقار مطلوبة'
        ];
    }
}