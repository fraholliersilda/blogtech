<?php

namespace Requests;

use Exceptions\ValidationException;
use Requests\BaseRequest;

class RegistrationRequest extends BaseRequest
{
    public static function validateSignup($data)
    {
        $rules = [
            'username' => ['required', 'string', 'min:3'],
            'email' => ['required', 'string', 'email'], // Ensure 'email' rule is implemented
            'password' => ['required', 'string', 'min:8', 'max:255']
        ];

        return self::validateRules($data, $rules);
    }

    public static function validateLogin($data)
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string']
        ];

        return self::validateRules($data, $rules);
    }

public static function validatePasswordReset($data)
{
    if (empty($data['password'])) {
        throw new ValidationException("Password is required.");
    }
    
    if (strlen($data['password']) < 8) {
        throw new ValidationException("Password must be at least 8 characters long.");
    }
    
    if (empty($data['confirm_password'])) {
        throw new ValidationException("Please confirm your password.");
    }
    
    if ($data['password'] !== $data['confirm_password']) {
        throw new ValidationException("Passwords do not match.");
    }
}
}
