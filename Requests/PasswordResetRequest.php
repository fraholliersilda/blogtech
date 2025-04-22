<?php

namespace Requests;

use Exceptions\ValidationException;
use Requests\BaseRequest;

class PasswordResetRequest extends BaseRequest
{
    public function validateEmail($data)
    {
        $rules = [
            'email' => ['required', 'string', 'email']
        ];

        return self::validateRules($data, $rules);
    }

    public function validatePasswordReset($data)
    {
        $rules = [
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'confirm_password' => ['required', 'string'],
            'token' => ['required', 'string']
        ];
        
        self::validateRules($data, $rules);

        if ($data['password'] !== $data['confirm_password']) {
            throw new ValidationException("Passwords do not match.");
        }
        
        return true;
    }
}