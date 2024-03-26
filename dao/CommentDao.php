<?php
namespace dao;

use model\Comment;

interface CommentDao
{
    public function createComment(Comment $comment): ?Comment;
    public function readCommentById(int $id): ?Comment;
    public function updateComment(Comment $comment): ?Comment;
    public function deleteComment(int $id): ?Comment;
    public function getCommentsByPublicationId(int $publicationId): array;
}
