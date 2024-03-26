<?php

namespace dao;

use model\Municipality;

interface MunicipalityDao
{
    public function createMunicipality(Municipality $municipality): ?Municipality;
    public function readMunicipalityById(int $id): ?Municipality;
    public function updateMunicipality(Municipality $municipality): ?Municipality;
    public function deleteMunicipality(int $id): ?Municipality;
    public function allMunicipalities(): array;
}
