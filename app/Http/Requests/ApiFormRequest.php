<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class ApiFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize(): bool;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules(): array;

    protected function failedValidation(Validator $validator)
    {
        $data = (collect($validator->errors())->map(function ($item, $key) {
            return [
                'value' => $this->{$key},
                'message' => $item[0],
            ];
        }));
        throw new HttpResponseException(response()->json(
            [
                'status' => false,
                'data' => $data,
                'error_code' => null,
                'message' => $validator->errors()->first(),
            ], 422));
    }


}
