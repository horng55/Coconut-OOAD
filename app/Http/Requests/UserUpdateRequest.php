<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $this->route('id'),
            'email' => 'required|email|unique:users,email,' . $this->route('id'),
            'password' => 'nullable|min:6|confirmed',
            'phone_number' => 'nullable|string',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
            'type' => 'nullable|in:user,admin,editor,teacher,student,parent',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ];
    }
}
