<?php

namespace Requests;
use Exceptions\ValidationException;

class BaseRequest
{
    // Validate data against defined rules and throw exception if validation fails
    public static function validateRules($data, $rules)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $errors = [];

        // Check each field against its validation rules
        foreach ($rules as $field => $fieldRules) {
            foreach ($fieldRules as $rule) {
                // Parse rule name and parameter (e.g., "min:8" -> "min" and "8")
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $param] = explode(':', $rule);
                } else {
                    $ruleName = $rule;
                    $param = null;
                }

                $error = self::applyRule($data[$field] ?? null, $field, $ruleName, $param);
                if ($error) {
                    $errors[$field] = $error;
                    break; // Stop checking rules for this field after first error
                }
            }
        }

        // Throw exception with first error if any validation failed
        if (!empty($errors)) {
            setErrors([$errors]);
            $firstError = array_values($errors)[0];
            throw new ValidationException($firstError);
        }

        return null;
    }

    // Apply individual validation rule to a field value
    protected static function applyRule($value, $field, $rule, $param)
    {
        switch ($rule) {
            case 'required':
                // Check if value exists or if file was uploaded successfully
                if (empty($value) && (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK)) {
                    return ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                }
                return null;

            case 'string':
                return !is_string($value) ? ucfirst(str_replace('_', ' ', $field)) . ' must be a string.' : null;

            case 'min':
                return strlen($value) < $param ? ucfirst(str_replace('_', ' ', $field)) . " must be at least $param characters." : null;

            case 'max':
                return strlen($value) > $param ? ucfirst(str_replace('_', ' ', $field)) . " must be at most $param characters." : null;

            case 'file':
                return !isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK ? ucfirst(str_replace('_', ' ', $field)) . ' must be a file.' : null;

            case 'image':
                // Validate file extension is an allowed image type
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                return !in_array($extension, $allowedExtensions) ? ucfirst(str_replace('_', ' ', $field)) . ' must be an image file.' : null;

            case 'maxFileSize':
                // Convert MB parameter to bytes and check file size
                $maxFileSize = $param * 1024 * 1024;
                return $_FILES[$field]['size'] > $maxFileSize ? ucfirst(str_replace('_', ' ', $field)) . " must be smaller than $param MB." : null;

            case 'email':
                return !filter_var($value, FILTER_VALIDATE_EMAIL) ? ucfirst(str_replace('_', ' ', $field)) . ' must be a valid email address.' : null;

            case 'different':
                // Ensure field value is different from another field's value
                return isset($param) && $value === $param ? ucfirst(str_replace('_', ' ', $field)) . ' must be different from ' . str_replace('_', ' ', $param) . '.' : null;


            default:
                return null;
        }
    }
}