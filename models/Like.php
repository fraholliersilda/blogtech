<?php
namespace Models;

use Exception;
class Like extends Model
{
    public $table = 'likes';
    
    public $fields = [
        'id',
        'user_id',
        'post_id',
        'created_at'
    ];

    public function getLikesByPostId($postId)
    {
        try {
            $result = $this->queryBuilder
                ->reset() // Add reset here
                ->table($this->table)
                ->select(['COUNT(*) as total_likes'])
                ->where('post_id', '=', $postId)
                ->getOne();
                
            return $result ?: ['total_likes' => 0];
        } catch (Exception $e) {
            error_log("Error getting likes count: " . $e->getMessage());
            return ['total_likes' => 0];
        }
    }

    public function hasUserLikedPost($userId, $postId)
    {
        $like = $this->queryBuilder
            ->reset() // Clear previous state
            ->table($this->table)
            ->select(['id'])
            ->where('user_id', '=', $userId)
            ->where('post_id', '=', $postId)
            ->getOne();
            
        return $like !== null;
    }

    public function addLike($userId, $postId)
    {
        // Check if like already exists
        if ($this->hasUserLikedPost($userId, $postId)) {
            return false; // Already liked
        }

        return $this->queryBuilder
            ->reset() // Add reset here
            ->table($this->table)
            ->insert([
                'user_id' => $userId,
                'post_id' => $postId
            ]);
    }

    public function removeLike($userId, $postId)
    {
        return $this->queryBuilder
            ->reset() // Add reset here
            ->table($this->table)
            ->where('user_id', '=', $userId)
            ->where('post_id', '=', $postId)
            ->delete()
            ->execute();
    }

    public function getAllLikesForPost($postId)
    {
        return $this->queryBuilder
            ->reset() // Add reset here
            ->table($this->table)
            ->select(['likes.*', 'users.username'])
            ->join('users', 'likes.user_id', '=', 'users.id')
            ->where('post_id', '=', $postId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}