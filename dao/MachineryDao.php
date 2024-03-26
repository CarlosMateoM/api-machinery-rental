<?php

namespace dao;

require_once './model/Machinery.php';

use model\Machinery;

interface MachineryDao
{
    public function createMachinery(Machinery $machinary): ?Machinery;
    public function readMachineryById(int $id): ?Machinery;
    public function updateMachinery(Machinery $machinary): ?Machinery;
    public function deleteMachinery(int $id): ?Machinery;
    public function allMachinaries(): array;
}
