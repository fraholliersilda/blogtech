<?php
namespace Models;

class Comment extends Model
{
    public $table = 'comments';
    
    public $fields = [
        'id',
        'content',
        'user_id',
        'post_id',
        'created_at',
        'updated_at'
    ];

    // Retrieve all comments for a specific post with user information
    public function getCommentsByPostId($postId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['comments.*', 'users.username'])
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('post_id', '=', $postId)
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    // Retrieve a single comment by ID with user information
    public function getCommentById($commentId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['comments.*', 'users.username'])
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('comments.id', '=', $commentId)
            ->getOne();
    }

    // Create a new comment
    public function addComment($data)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->insert([
                'content' => $data['content'],
                'user_id' => $data['user_id'],
                'post_id' => $data['post_id']
            ]);
    }

    // Update comment content
    public function updateComment($commentId, $content)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->update(['content' => $content])
            ->where('id', '=', $commentId)
            ->execute();
    }

    // Delete a comment by ID
    public function deleteComment($commentId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('id', '=', $commentId)
            ->delete()
            ->execute();
    }

    // Get total number of comments for a specific post
    public function getCommentsCountByPostId($postId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['COUNT(*) as total_comments'])
            ->where('post_id', '=', $postId)
            ->getOne();
    }
}