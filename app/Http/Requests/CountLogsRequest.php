<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CountLogsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Set the authorization logic based on your requirements
        // For example, you can check if the user is authenticated or has the necessary permissions
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
            'serviceNames' => 'array',
            'serviceNames.*' => 'string|max:255',
            'statusCode' => 'integer|min:100|max:599',
            'startDate' => 'date_format:Y-m-d',
            'endDate' => 'date_format:Y-m-d|after_or_equal:startDate',
        ];
    }

    /**
     * Get the validation messages for the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'serviceNames.array' => 'The serviceNames must be an array.',
            'serviceNames.*.string' => 'Each serviceName must be a string.',
            'serviceNames.*.max' => 'Each serviceName may not be greater than :max characters.',
            'statusCode.integer' => 'The statusCode must be an integer.',
            'statusCode.min' => 'The statusCode must be at least :min.',
            'statusCode.max' => 'The statusCode may not be greater than :max.',
            'startDate.date_format' => 'The startDate does not match the format Y-m-d.',
            'endDate.date_format' => 'The endDate does not match the format Y-m-d.',
            'endDate.after_or_equal' => 'The endDate must be a date after or equal to the startDate.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
