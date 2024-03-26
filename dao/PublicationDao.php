<?php

namespace dao;

use model\Publication;

interface PublicationDao
{
    public function createPublication(Publication $publication): ?Publication;
    public function readPublicationById(int $id): ?Publication;
    public function updatePublication(Publication $publication): ?Publication;
    public function deletePublication(int $id): ?Publication;
    public function allPublications(): array;
    public function searchPublicationsByName(string $name): array;
}
