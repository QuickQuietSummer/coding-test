<?php

namespace App\Http\Requests\Request;

use Illuminate\Foundation\Http\FormRequest;

class RequestPutFormRequest extends FormRequest
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
            'comment' => ['required', 'string', 'max:10000']
        ];
    }

    public function bodyParameters()
    {
        return [
            'comment' => [
                'description' => 'Comment of resolve.'
            ]
        ];
    }
}
