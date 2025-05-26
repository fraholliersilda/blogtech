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

    public function getAllPost()
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select([
                'posts.*', 
                'users.username',
                'media.path as cover_photo_path',
                'COUNT(DISTINCT likes.id) as likes_count',
                'COUNT(DISTINCT comments.id) as comments_count'
            ])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('media', function($join) {
                $join->on('posts.id', '=', 'media.post_id')
                     ->where('media.photo_type', '=', 'cover');
            })
            ->leftJoin('likes', 'posts.id', '=', 'likes.post_id')
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->groupBy('posts.id')
            ->orderBy('posts.created_at', 'DESC')
            ->get();
    }

    public function getUserPosts($userId)
{
    return $this->queryBuilder
        ->table($this->table)
        ->select([
            'posts.*', 
            'users.username',
            'media.path as cover_photo_path',
            'COUNT(DISTINCT likes.id) as likes_count',
            'COUNT(DISTINCT comments.id) as comments_count'
        ])
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->leftJoin('media', function($join) {
            $join->on('posts.id', '=', 'media.post_id')
                 ->where('media.photo_type', '=', 'cover');
        })
        ->leftJoin('likes', 'posts.id', '=', 'likes.post_id')
        ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
        ->where('posts.user_id', '=', $userId)
        ->groupBy('posts.id')
        ->orderBy('posts.created_at', 'DESC')
        ->get();
}

    public function getPostById($postId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select([
                'posts.*', 
                'users.username',
                'media.path as cover_photo_path',
                'COUNT(DISTINCT likes.id) as likes_count',
                'COUNT(DISTINCT comments.id) as comments_count'
            ])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('media', function($join) {
                $join->on('posts.id', '=', 'media.post_id')
                     ->where('media.photo_type', '=', 'cover');
            })
            ->leftJoin('likes', 'posts.id', '=', 'likes.post_id')
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->where('posts.id', '=', $postId)
            ->groupBy('posts.id')
            ->getOne();
    }

    public function getLatestPosts($excludePostId = null, $limit = 2)
    {
        $query = $this->queryBuilder
            ->table($this->table)
            ->select([
                'posts.*', 
                'users.username',
                'media.path as cover_photo_path',
                'COUNT(DISTINCT likes.id) as likes_count',
                'COUNT(DISTINCT comments.id) as comments_count'
            ])
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->leftJoin('media', function($join) {
                $join->on('posts.id', '=', 'media.post_id')
                     ->where('media.photo_type', '=', 'cover');
            })
            ->leftJoin('likes', 'posts.id', '=', 'likes.post_id')
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id');

        if ($excludePostId) {
            $query->where('posts.id', '!=', $excludePostId);
        }

        return $query->groupBy('posts.id')
                    ->orderBy('posts.created_at', 'DESC')
                    ->limit($limit)
                    ->get();
    }

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

    public function updatePost($postId, $data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->update($data)
            ->where('id', '=', $postId)
            ->execute();
    }

    public function deletePost($postId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('id', '=', $postId)
            ->delete()
            ->execute();
    }
}