<?php
namespace Requests;

use Requests\BaseRequest;

class PostsRequest extends BaseRequest
{
    // Common validation rules for both create and edit operations
    protected static $rules = [
        'title' => ['required', 'string', 'min:3'],
        'description' => ['required', 'string', 'max:5000']
    ];

    // Additional validation rules for post creation (cover photo required)
    protected static $creationRules = [
        'cover_photo' => ['required', 'file', 'image', 'maxFileSize:5']
    ];

    // Validate post data with optional cover photo requirement for edits
    public static function validate($data, $isEdit = false)
    {
        $rules = self::$rules;

        // Require cover photo only for new posts, not edits
        if (!$isEdit) {
            $rules = array_merge($rules, self::$creationRules);
        }

        return self::validateRules($data, $rules);
    }
}