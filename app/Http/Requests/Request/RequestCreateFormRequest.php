<?php

namespace App\Http\Requests\Request;

use Illuminate\Foundation\Http\FormRequest;

class RequestCreateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => ['required', 'string', 'max:255']
        ];
    }

    public function bodyParameters()
    {
        return [
            'message' => [
                'description' => 'Message of request.'
            ]
        ];
    }
}
