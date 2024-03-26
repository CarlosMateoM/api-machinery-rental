<?php
namespace dao;


interface ValidationDao
{
    public function existsInDatabase(String $table, String $column, int $id): bool;
    public function uniqueInDatabase(String $table, String $column, int $id): bool;

}