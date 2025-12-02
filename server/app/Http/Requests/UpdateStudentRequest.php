<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(int $studentId): array
    {
         $studentId = $this->route('studentId');
        return [
            'igazolvanyszam' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students')
                    ->ignore($studentId)
                    ->where(fn($query) =>
                        $query->where('schoolclassId', $this->schoolclassId)
                    ),
            ],
        ];
    }
}
