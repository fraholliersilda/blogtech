<?php
namespace Models;

class Post extends Model
{
    public $table = 'posts';
    public $fields = [
        'id',
        'title',
        'description',
        'user_id'
    ];

    public function getAllPost()
    {
        return $this->queryBuilder
            ->table('posts')
            ->select(['posts.id', 'posts.title', 'posts.description', 'posts.user_id', 'media.path AS cover_photo_path', 'users.username'])
            ->leftJoin('media', 'posts.id', '=', 'media.post_id AND (media.photo_type = "cover" OR media.photo_type IS NULL)')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->orderBy('posts.created_at', 'DESC')
            ->get();
    }

    public function getLatestPosts()
    {
        return $this->queryBuilder
            ->table('posts')
            ->select(['posts.id', 'posts.title', 'posts.description', 'posts.user_id', 'media.path AS cover_photo_path', 'users.username'])
            ->leftJoin('media', 'posts.id', '=', 'media.post_id')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->where('media.photo_type', '=', 'cover')
            ->orderBy('posts.created_at', 'DESC')
            ->limit(2)
            ->get();
    }

    public function getPostById($postId)
    {
        $result = $this->queryBuilder
            ->table('posts')
            ->select(['posts.id', 'posts.title', 'posts.description', 'posts.user_id', 'media.path AS cover_photo_path', 'users.username'])
            ->leftJoin('media', 'posts.id', '=', 'media.post_id')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->where('posts.id', '=', $postId)
            ->where('media.photo_type', '=', 'cover')
            ->limit(1) 
            ->get();
    
        return !empty($result) ? $result[0] : null; 
    }

    public function getUserPosts($userId)
    {
        return $this->queryBuilder
            ->table('posts')
            ->select(['posts.id', 'posts.title', 'posts.description', 'posts.user_id', 'posts.created_at', 'media.path AS cover_photo_path', 'users.username'])
            ->leftJoin('media', 'posts.id', '=', 'media.post_id AND (media.photo_type = "cover" OR media.photo_type IS NULL)')
            ->leftJoin('users', 'posts.user_id', '=', 'users.id')
            ->where('posts.user_id', '=', $userId)
            ->orderBy('posts.created_at', 'DESC')
            ->get();
    }

    public function updatePost($postId, $data)
    {
        return $this->queryBuilder
            ->table('posts')
            ->update([
                'title' => $data['title'],
                'description' => $data['description']
            ])
            ->where('id', '=', $postId)
            ->execute();
    }

    public function createPost($data)
    {
        // Add user_id to the data
        $data['user_id'] = $_SESSION['user_id'];
        
        return $this->queryBuilder
            ->table('posts')
            ->insert($data);
    }

    public function deletePost($postId)
    {
        error_log("Deleting post with ID: $postId");
        return $this->queryBuilder
            ->table('posts')
            ->where('id', '=', $postId)
            ->delete()
            ->execute();
    }
}