<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthenticateRequest extends FormRequest
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
            'firstname'=>['required','min:2'],
            'lastname'=>['required','min:2'],
            'email'=>['required','email',Rule::unique('users', 'email')],
            'phone'=>['required','min:9','max:13'],
            'country'=>['required'],
            'city'=>['required'],
        ];
    }
}
