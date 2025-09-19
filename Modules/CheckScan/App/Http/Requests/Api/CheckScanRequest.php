<?php

namespace Modules\CheckScan\App\Http\Requests\Api;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CheckScanRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'scans' => 'required|array',
            'scans.*.file' => 'required|file|mimes:xlsx,xls',
            // 'scans.*.model' => 'required|string',
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
