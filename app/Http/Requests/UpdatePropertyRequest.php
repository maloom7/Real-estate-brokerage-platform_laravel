<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('update', $this->route('property'));
    }

    public function rules(): array
    {
        return [
            'title_ar' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:draft,pending_review,approved,active,under_offer,reserved,sold,rented,archived',
            'visibility' => 'sometimes|required|in:public,internal,vip'
        ];
    }
}