<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserSelfRequest extends FormRequest
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
        return [
            'name' => 'nullable|string',
            'email' => 'nullable|email',
            'password' => 'nullable',
            // Tiltott mező: Ha a role mező megérkezik a kérésben, a validáció elbukik.
            'role' => 'prohibited',
        ];
        // return [
        //     'name' => 'nullable|string',
        //     'email' => 'nullable|email',
        //     'password' => [
        //         'nullable',
        //         'string',
        //         Password::min(10) // Minimum 10 karakter
        //             ->mixedCase() // Kevert kis- és nagybetű
        //             ->letters()   // Legalább egy betű
        //             ->numbers()   // Legalább egy szám
        //             ->symbols()   // Legalább egy szimbólum
        //             ->uncompromised(), // Ne legyen kiszivárgott
        //     ],
        //     // Tiltott mező: Ha a role mező megérkezik a kérésben, a validáció elbukik.
        //     'role' => 'prohibited',
        // ];
        
    }
}
