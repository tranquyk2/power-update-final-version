<?php

namespace Modules\BarcodeScan\App\Http\Requests;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class BarcodeRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'line_id' => 'required|exists:barcode_lines,id',
            'factory_id' => 'required|exists:barcode_factories,id',
            'code' => 'required|string',
            'device_name' => 'required|string',
            'datetime' => 'required|date_format:Y-m-d H:i:s',
            'type_id' => 'required|integer:1,2',
            'status' => 'required|string|in:ok,ng,duplicate',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
