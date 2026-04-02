<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('upload', $this->route('property')->documents()->make([
            'is_confidential' => $this->is_confidential ?? false,
            'property' => $this->route('property')
        ]));
    }

    public function rules(): array
    {
        return [
            'document' => 'required|file|max:51200|mimes:pdf,doc,docx,jpg,jpeg,png',
            'document_type_id' => 'required|exists:document_types,id',
            'is_confidential' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'يجب اختيار ملف',
            'document.max' => 'حجم الملف لا يجب أن يتجاوز 50 ميجابايت',
            'document_type_id.required' => 'يجب اختيار نوع المستند'
        ];
    }
}