<?php

namespace dao\impl;

require_once __DIR__ . '../../../autoload.php';

use \util\DatabaseConnection;
use \dao\PublicationDao;
use \model\Publication;
use \model\Municipality;
use \model\User;
use \model\Category;
use model\Departament;
use \model\Machinery;
use \model\MachineryPhoto;
use \PDO;

class PublicationMySqlDao implements PublicationDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function searchPublicationsByName(string $name): array
    {
        $publications = [];
        $sql = "SELECT p.title, p.id FROM publications AS p WHERE p.title LIKE :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', "%".$name."%");
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $publication = [
                "id" => $row['id'],
                "title" => $row['title'] 
            ];

            $publications[] = $publication;
        }

        return $publications;
    }

    public function createPublication(Publication $publication): ?Publication
    {
        $sql = "INSERT INTO publications (title, user_id, price_per_hour, machinary_id, municipality_id) 
                VALUES (:title, :user_id, :price_per_hour, :machinary_id, :municipality_id)";

        $stmt = $this->pdo->prepare($sql);

        $title = $publication->getTitle();
        $pricePerHour = $publication->getPricePeHour();
        $user_id = $publication->getUser()->getId();
        $machinary_id = $publication->getMachinary()->getId();
        $municipality_id = $publication->getMunicipality()->getId();

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':price_per_hour', $pricePerHour);
        $stmt->bindParam(':machinary_id', $machinary_id);
        $stmt->bindParam(':municipality_id', $municipality_id);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $publication->setId($lastInsertId);
            return $publication;
        }

        return null;
    }

    public function readPublicationById(int $id): ?Publication
    {
        $sql = "SELECT 
        p.*,
        u.names,
        u.last_names,
        m.id as machinary_id,
        m.brand,
        m.description,
        c.name as category_name, 
        mu.name as municipality_name,
        d.name as departament_name
        FROM
        publications AS p 
        INNER JOIN machinaries AS m ON p.machinary_id = m.id
        INNER JOIN users AS u ON p.user_id = u.id
        INNER JOIN categories AS c ON m.category_id = c.id
        INNER JOIN municipalities AS mu ON mu.id = p.municipality_id
        INNER JOIN departaments AS d ON d.id = mu.departament_id
        WHERE p.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $publication = new Publication();
            $publication->setId($row['id']);
            $publication->setTitle($row['title']);
            $publication->setPricePeHour($row['price_per_hour']);
            
            $user = new User();
            $user->setId($row['user_id']); // Ajusta según el nombre real de la columna
            $user->setNames($row['names']);
            $user->setLastNames($row['last_names']);
            $publication->setUser($user);

            $machinary = new Machinery(); // Debes cargar la maquinaria real desde la base de datos
            $machinary->setId($row['machinary_id']); // Ajusta según el nombre real de la columna
            $machinary->setBrand($row['brand']);
            $machinary->setDescription($row['description']);
            
            $category = new Category();
            $category->setName($row['category_name']);
            $machinary->setCategory($category);

            $publication->setMachinary($machinary);
            
            $municipality = new Municipality(); // Debes cargar el municipio real desde la base de datos
            $municipality->setId($row['municipality_id']); // Ajusta según el nombre real de la columna
            $municipality->setName($row['municipality_name']);

            $departament = new Departament();
            $departament->setName($row['departament_name']);
            $municipality->setDepartament($departament);
    
            $publication->setMunicipality($municipality);


            $machineryId = $machinary->getId();


            $sqlPhotos = "SELECT * FROM machinery_photos WHERE machinery_id = :machinery_id";
            $stmtPhotos = $this->pdo->prepare($sqlPhotos);
            $stmtPhotos->bindParam(':machinery_id', $machineryId);
            $stmtPhotos->execute();
            
    
            while ($rowPhoto = $stmtPhotos->fetch(PDO::FETCH_ASSOC)) {
                $photo = new MachineryPhoto();
                $photo->setId($rowPhoto['id']);
                $photo->setSrc($rowPhoto['src']);
                $photo->setMachineryId($rowPhoto['machinery_id']);
    
                $machinary->addPhoto($photo);
            }

            return $publication;
        } else {
            return null;
        }
    }


    public function updatePublication(Publication $publication): ?Publication
    {
        $sql = "UPDATE publications SET title = :title, price_per_hour = :price_per_hour, machinary_id = :machinary_id, user_id = :user_id, municipality_id = :municipality_id 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $id = $publication->getId();
        $title = $publication->getTitle();
        $pricePerHour = $publication->getPricePeHour();
        $machinary_id = $publication->getMachinary()->getId();
        $user_id = $publication->getUser()->getId();
        $municipality_id = $publication->getMunicipality()->getId();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':price_per_hour', $pricePerHour);
        $stmt->bindParam(':machinary_id', $machinary_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':municipality_id', $municipality_id);

        if ($stmt->execute()) {
            return $publication;
        }

        return null;
    }

    public function deletePublication(int $id): ?Publication
    {
        $publication = $this->readPublicationById($id);

        if ($publication) {
            $sql = "DELETE FROM publications WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return $publication;
            }
        }

        return null;
    }



    public function allPublications(): array
    {
        $sql = "SELECT 
        p.id as publication_id,
        p.title,
        p.price_per_hour,
        u.names,
        u.last_names,
        m.*,
        c.name as category_name, 
        mp.src,
        mu.name as municipality_name
        FROM
        publications AS p 
        INNER JOIN machinaries AS m ON p.machinary_id = m.id
        LEFT JOIN machinery_photos AS mp ON m.id = mp.machinery_id
        INNER JOIN users AS u ON p.user_id = u.id
        INNER JOIN categories AS c ON m.category_id = c.id
        INNER JOIN municipalities AS mu ON mu.id = p.municipality_id
        GROUP BY p.id";
        
        $stmt = $this->pdo->query($sql);
        $publications = [];
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $publication = new Publication();
            
            $publication->setId($row['publication_id']);
            $publication->setTitle($row['title']);
            $publication->setPricePeHour($row['price_per_hour']);
    
            $user = new User();
            $user->setNames($row['names']); // Ajusta según el nombre real de la columna
            $user->setLastNames($row['last_names']); // Ajusta según el nombre real de la columna
            $publication->setUser($user);
    
            $machinery = new Machinery();
            $machinery->setBrand($row['brand']);
            $machinery->setDescription($row['description']);
            // ... otros atributos ...
            $publication->setMachinary($machinery);
    
            $category = new Category();
            $category->setName($row['category_name']);
            $machinery->setCategory($category);

            $municipality = new Municipality();
            $municipality->setName($row['municipality_name']);
            $publication->setMunicipality($municipality);
    
            // Añadir la foto solo si está presente
            if ($row['src'] !== null) {
                $photo = new MachineryPhoto();
                $photo->setSrc($row['src']);
                $machinery->addPhoto($photo);
            }
    
            $publications[] = $publication->getJsonWithRelations();
        }
    
        return $publications;
    }
    

   

    

    
}
