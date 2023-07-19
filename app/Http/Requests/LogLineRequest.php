<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogLineRequest extends FormRequest
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
            'service_name' => 'required|string|max:255',
            'logged_at' => 'required|date_format:Y-m-d H:i:s',
            'method' => 'required|string|max:10',
            'endpoint' => 'required|string|max:255',
            'protocol' => 'required|string|max:20',
            'status' => 'required|integer|min:100|max:599',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'service_name.required' => 'The service name field is required.',
            'service_name.string' => 'The service name must be a string.',
            'service_name.max' => 'The service name may not be greater than :max characters.',
            'logged_at.required' => 'The logged at field is required.',
            'logged_at.date_format' => 'The logged at does not match the format Y-m-d H:i:s.',
            'method.required' => 'The method field is required.',
            'method.string' => 'The method must be a string.',
            'method.max' => 'The method may not be greater than :max characters.',
            'endpoint.required' => 'The endpoint field is required.',
            'endpoint.string' => 'The endpoint must be a string.',
            'endpoint.max' => 'The endpoint may not be greater than :max characters.',
            'protocol.required' => 'The protocol field is required.',
            'protocol.string' => 'The protocol must be a string.',
            'protocol.max' => 'The protocol may not be greater than :max characters.',
            'status.required' => 'The status field is required.',
            'status.integer' => 'The status must be an integer.',
            'status.min' => 'The status must be at least :min.',
            'status.max' => 'The status may not be greater than :max.',
        ];
    }
}
