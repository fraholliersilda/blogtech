<?php

namespace Requests;

use Exceptions\ValidationException;
use Requests\BaseRequest;

class PasswordResetRequest extends BaseRequest
{
    // Validate email format for password reset request
    public function validateEmail($data)
    {
        $rules = [
            'email' => ['required', 'string', 'email']
        ];

        return self::validateRules($data, $rules);
    }

    // Validate password reset form with password confirmation match
    public function validatePasswordReset($data)
    {
        $rules = [
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'confirm_password' => ['required', 'string'],
            'token' => ['required', 'string']
        ];

        self::validateRules($data, $rules);

        // Ensure password and confirmation match
        if ($data['password'] !== $data['confirm_password']) {
            throw new ValidationException("Passwords do not match.");
        }

        return true;
    }
}