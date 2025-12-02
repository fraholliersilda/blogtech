<?php
namespace Models;

class Post extends Model
{
    public $table = 'posts';
    
    public $fields = [
        'id',
        'title',
        'description',
        'user_id',
        'created_at'
    ];

    // Retrieve all posts with user info, cover photo, and comment count
    public function getAllPost()
{
    return $this->queryBuilder
        ->table($this->table)
        ->select([
            'posts.*', 
            'users.username',
            'media.path as cover_photo_path',
            'COUNT(DISTINCT comments.id) as comments_count'
        ])
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->leftJoin('media', function($join) {
            $join->on('posts.id', '=', 'media.post_id')
                 ->where('media.photo_type', '=', 'cover');
        })


        ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
        ->groupBy('posts.id')
        ->orderBy('posts.created_at', 'DESC')
        ->get();
}

    // Retrieve all posts by a specific user with related data
   public function getUserPosts($userId)
{
    return $this->queryBuilder
        ->table($this->table)
        ->select([
            'posts.*', 
            'users.username',
            'media.path as cover_photo_path',
            'COUNT(DISTINCT comments.id) as comments_count'
        ])
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->leftJoin('media', function($join) {
            $join->on('posts.id', '=', 'media.post_id')
                 ->where('media.photo_type', '=', 'cover');
        })


        ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
        ->where('posts.user_id', '=', $userId)
        ->groupBy('posts.id')
        ->orderBy('posts.created_at', 'DESC')
        ->get();
}

    // Retrieve a single post by ID with all related data
public function getPostById($postId)
{
    return $this->queryBuilder
        ->table($this->table)
        ->select([
            'posts.*', 
            'users.username',
            'media.path as cover_photo_path',
            'COUNT(DISTINCT comments.id) as comments_count'
        ])
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->leftJoin('media', function($join) {
            $join->on('posts.id', '=', 'media.post_id')
                 ->where('media.photo_type', '=', 'cover');
        })

        ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
        ->where('posts.id', '=', $postId)
        ->groupBy('posts.id')
        ->getOne();
}

    // Retrieve latest posts, optionally excluding a specific post
    public function getLatestPosts($excludePostId = null, $limit = 2)
{
    $query = $this->queryBuilder
        ->table($this->table)
        ->select([
            'posts.*', 
            'users.username',
            'media.path as cover_photo_path',
            'COUNT(DISTINCT comments.id) as comments_count'
        ])
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->leftJoin('media', function($join) {
            $join->on('posts.id', '=', 'media.post_id')
                 ->where('media.photo_type', '=', 'cover');
        })

        ->leftJoin('comments', 'posts.id', '=', 'comments.post_id');

    // Exclude specific post if provided (useful for "related posts")
    if ($excludePostId) {
        $query->where('posts.id', '!=', $excludePostId);
    }

    return $query->groupBy('posts.id')
                ->orderBy('posts.created_at', 'DESC')
                ->limit($limit)
                ->get();
}

    // Create a new post with current user as author
    public function createPost($data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->insert([
                'title' => $data['title'],
                'description' => $data['description'],
                'user_id' => $_SESSION['user_id']
            ]);
    }

    // Update an existing post
    public function updatePost($postId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->update($data)
            ->where('id', '=', $postId)
            ->execute();
    }

    // Delete a post by ID
    public function deletePost($postId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('id', '=', $postId)
            ->delete()
            ->execute();
    }
}