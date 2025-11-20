<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
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
