<?php

namespace controller;

require_once __DIR__ . '/../autoload.php';

use validation\Request;
use controller\BaseController;
use dao\impl\MachineryPhotoMySqlDao;
use util\JsonResponse;
use dao\MachineryPhotoDao;
use model\MachineryPhoto;

class MachineryPhotoController extends BaseController
{
    private MachineryPhotoDao $photoDao;

    public function __construct()
    {
        $this->photoDao = new MachineryPhotoMySqlDao();
    }

    public function createPost()
    {
        $serverUrl = 'http://' . $_SERVER['HTTP_HOST'];
        // Obtener los datos del formulario y validarlos
        $requestData = $_POST; // Puedes usar $_POST si los datos vienen del formulario
        $files = $_FILES['photos']; // El nombre 'photos' debe coincidir con el atributo 'name' del input del formulario
    
        Request::validate($requestData, [
            'machinery_id' => 'required|integer',
        ]);
    
        // Crear un array para almacenar las rutas de las fotos subidas
        $uploadedPhotos = [];
    
        // Ruta donde se guardarán las fotos
        $uploadDirectory = 'uploads/'; // Cambia 'uploads/' a la carpeta que prefieras
    
        // Iterar sobre las fotos
        foreach ($files['tmp_name'] as $index => $tmpName) {
            // Obtener el nombre original del archivo
            $originalFileName = basename($files['name'][$index]);
    
            // Generar un nombre único para la foto usando un hash
            $hashedFileName = md5(uniqid()) . '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
    
            // Construir la ruta completa de la foto
            $uploadFilePath = $uploadDirectory . $hashedFileName;
    
            // Mover la foto al directorio de destino
            if (move_uploaded_file($tmpName, $uploadFilePath)) {
                $photoUrl = $serverUrl . '/' . $uploadFilePath;
                $uploadedPhotos[] = $photoUrl; // Almacenar la ruta de la foto subida
            }
        }
    
        // Crear y guardar las fotos en la base de datos
        foreach ($uploadedPhotos as $photoPath) {
            $photo = new MachineryPhoto();
            $photo->setSrc($photoPath);
            $photo->setMachineryId($requestData['machinery_id']);
    
            $photoCreated = $this->photoDao->createPhoto($photo);
    
            if (!$photoCreated) {
                // Manejar el error al guardar la foto en la base de datos
                JsonResponse::send(500, 'Error al crear la foto', [], 'PHOTO_INSERT_ERROR', 500);
            }
        }
    
        // Envía una respuesta exitosa si todas las fotos se crearon correctamente
        JsonResponse::send(200, 'Fotos creadas exitosamente', [], 'PHOTOS_INSERT_OK', 201);
    }
    
    

    public function readPhotoById()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $photoId = (int) $id;

        $photo = $this->photoDao->readPhotoById($photoId);

        if ($photo) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $photo->getJson(), 'PHOTO_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Foto no encontrada', [], 'PHOTO_GET_ERROR', 404);
        }
    }

    public function updatePhoto()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, [
            'src' => 'required|string',
            'machinery_id' => 'required|integer',
        ]);

        $photo = new MachineryPhoto();
        $photo->setId($id);
        $photo->setSrc($requestData['src']);
        $photo->setMachineryId($requestData['machinery_id']);

        $photoUpdated = $this->photoDao->updatePhoto($photo);

        if ($photoUpdated) {
            JsonResponse::send(200, 'Foto actualizada exitosamente', [$photoUpdated->getJson()], 'PHOTO_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar la foto', [], 'PHOTO_UPDATE_ERROR', 500);
        }
    }

    public function deletePhoto()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $photoDeleted = $this->photoDao->deletePhoto($id);

        if ($photoDeleted) {
            JsonResponse::send(200, 'Foto eliminada exitosamente', [$photoDeleted->getJson()], 'PHOTO_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar la foto', [], 'PHOTO_DELETE_ERROR', 500);
        }
    }

    public function getPhotosByMachineryId()
    {
        $machineryId = $_GET['machinery_id'] ?? null;

        $this->validateIdParameter($machineryId);

        $photos = $this->photoDao->getPhotosByMachineryId((int) $machineryId);

        JsonResponse::send(200, 'Lista de fotos de maquinaria', $photos, 'PHOTOS_GET_OK', 200);
    }
}
