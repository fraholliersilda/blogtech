<?php

namespace Requests;

use Requests\BaseRequest;

class UpdateProfilePictureRequest extends BaseRequest
{
    // Validate profile picture upload (must be image file under 5MB)
    public static function validate($data)
    {
        $rules = [
            'profile_picture' => ['required', 'file', 'image', 'maxFileSize:5']
        ];

        return self::validateRules($data, $rules);
    }
}