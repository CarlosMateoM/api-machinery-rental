<?php

namespace dao;

use model\Like;

interface LikeDao
{
    public function createLike(Like $like): ?Like;
    public function getLikesByUserId(int $userId): array;
    public function readLikesByPublicationId(int $publicationId): array;
    public function deleteLike(int $id): ?Like;
    public function userLikedPublication(int $userId, int $publicationId): bool;
}
