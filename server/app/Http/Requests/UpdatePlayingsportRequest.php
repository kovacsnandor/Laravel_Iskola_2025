<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayingsportRequest extends FormRequest
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

        // 1. Az ID megszerzése a route-ról (feltételezve, hogy a frissített modellt kapod vissza)
        $idToIgnore = $this->route('playingsport')->id;

        return [
            'studentId' => [
                'required',
                'integer',
                // Az egyediségi szabály string formában:
                "unique:$tableName,studentId,$idToIgnore,id,sportId," . $this->sportokId,
            ],
            'sportId' => 'required|integer',
        ];
    }
}
