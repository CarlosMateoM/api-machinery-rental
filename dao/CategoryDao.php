<?php

namespace dao;

use model\Category;

interface CategoryDao
{
    public function createCategory(Category $category): ?Category;
    public function readCategoryById(int $id): ?Category;
    public function updateCategory(Category $category): ?Category;
    public function deleteCategory(int $id): ?Category;
    public function allCategories(): array;
}
