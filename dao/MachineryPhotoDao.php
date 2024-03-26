<?php

namespace dao;

require_once './model/MachineryPhoto.php';

use \model\MachineryPhoto;

interface MachineryPhotoDao
{
    public function createPhoto(MachineryPhoto $photo): ?MachineryPhoto;
    public function readPhotoById(int $id): ?MachineryPhoto;
    public function updatePhoto(MachineryPhoto $photo): ?MachineryPhoto;
    public function deletePhoto(int $id): ?MachineryPhoto;
    public function getPhotosByMachineryId(int $machineryId): array;
}
