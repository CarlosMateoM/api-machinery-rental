<?php
namespace dao;

require_once './model/Departament.php';

use model\Departament;

interface DepartamentDao
{
    public function createDepartament(Departament $departament): ?Departament;
    public function readDepartamentById(int $id): ?Departament;
    public function updateDepartament(Departament $departament): ?Departament;
    public function deleteDepartament(int $id): ?Departament;
    public function allDepartaments(): array;
}
