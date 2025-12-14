<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayingsportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tableName = 'playingsports';

        return [
            // A szintaktika: unique:tábla,oszlop,kizárt_id,kulcsoszlop,extra_oszlop,extra_érték
            'studentId' => "required|integer|unique:$tableName,studentId,NULL,studentId,sportId," . $this->sportId,
            'sportId' => 'required|integer',
        ];
    }
}
