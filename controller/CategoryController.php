<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use dao\impl\CategoryMySqlDao;
use model\Category;
use util\JsonResponse;
use validation\Request;

class CategoryController extends BaseController
{
    private $categoryDao;

    public function __construct()
    {
        $this->categoryDao = new CategoryMySqlDao();
    }

    public function allGet()
    {
        $categories = $this->categoryDao->allCategories();
        JsonResponse::send(200, 'Listado de categorías', $categories, 'CATEGORIES_GET_OK', 200);
    }

    public function createCategory()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, [
            'name' => 'required|string',
        ]);

        $category = new Category();
        $category->setName($requestData['name']);

        $categoryCreated = $this->categoryDao->createCategory($category);

        if ($categoryCreated) {
            JsonResponse::send(200, 'Categoría creada exitosamente', [$categoryCreated->getJson()], 'CATEGORY_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Error al crear la categoría', [], 'CATEGORY_INSERT_ERROR', 500);
        }
    }

    public function readCategoryById()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $categoryId = (int)$id;

        $category = $this->categoryDao->readCategoryById($categoryId);

        if ($category) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $category->getJson(), 'CATEGORY_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Categoría no encontrada', [], 'CATEGORY_GET_ERROR', 404);
        }
    }

    public function updateCategory()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, [
            'name' => 'required|string',
        ]);

        $category = new Category();
        $category->setId($id);
        $category->setName($requestData['name']);

        $categoryUpdated = $this->categoryDao->updateCategory($category);

        if ($categoryUpdated) {
            JsonResponse::send(200, 'Categoría actualizada exitosamente', [$categoryUpdated->getJson()], 'CATEGORY_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar la categoría', [], 'CATEGORY_UPDATE_ERROR', 500);
        }
    }

    public function deleteCategory()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $categoryDeleted = $this->categoryDao->deleteCategory($id);

        if ($categoryDeleted) {
            JsonResponse::send(200, 'Categoría eliminada exitosamente', [$categoryDeleted->getJson()], 'CATEGORY_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar la categoría', [], 'CATEGORY_DELETE_ERROR', 500);
        }
    }
}
