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

    public function getCommentById($commentId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['comments.*', 'users.username'])
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->where('comments.id', '=', $commentId)
            ->getOne();
    }

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

    public function updateComment($commentId, $content)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->update(['content' => $content])
            ->where('id', '=', $commentId)
            ->execute();
    }

    public function deleteComment($commentId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->where('id', '=', $commentId)
            ->delete()
            ->execute();
    }

    public function getCommentsCountByPostId($postId)
    {
        return $this->queryBuilder
            ->table($this->table)
            ->select(['COUNT(*) as total_comments'])
            ->where('post_id', '=', $postId)
            ->getOne();
    }
}