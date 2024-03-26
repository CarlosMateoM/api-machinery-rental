<?php

namespace model;


class Machinery
{

    private int $id;
    private string $name;
    private string $brand;
    private string $description;
    private int $category_id;
    private ?Category $category;  // Objeto de tipo Category
    private array $photos;
    public static $rules = [
        'name' => 'required|string',
        'brand' => 'required|string',
        'description' => 'required|string',
        'category_id' => 'required|numeric',
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->name = "";
        $this->brand = "";
        $this->description = "";
        $this->category_id = 0;
        $this->category = null;
        $this->photos = [];
    }

    public function setCategory(?Category $category)
    {
        $this->category = $category;
    }


    /**
     * Get the value of category_id
     */
    public function getCategory_id()
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @return  self
     */
    public function setCategory_id($category_id)
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of brand
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set the value of brand
     *
     * @return  self
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of category
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function getPhotos(): array
    {
        return $this->photos;
    }

    public function setPhotos(array $photos): void
    {
        $this->photos = $photos;
    }

    public function addPhoto($machineryPhoto)
    {
        $this->photos[] = $machineryPhoto;
    }


    public function getJsonWithRelations(): array
    {

        $jsonArray = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'brand' => $this->getBrand(),
            'description' => $this->getDescription(),
            'category_id' => $this->getCategory()->getId(),
            'category' => $this->getCategory()->getJson(),
        ];

        foreach ($this->photos as $municipality) {
            $jsonArray['photos'][] = $municipality->getJson();
        }

        return $jsonArray;
    }

    public static function fillMachinaryFromRequestData(array $requestData): Machinery
    {
        $machinary = new Machinery();

        if (isset($requestData['name'])) {
            $machinary->setName($requestData['name']);
        }

        if (isset($requestData['brand'])) {
            $machinary->setBrand($requestData['brand']);
        }

        if (isset($requestData['description'])) {
            $machinary->setDescription($requestData['description']);
        }

        if (isset($requestData['category_id'])) {
            // Aquí asumimos que $requestData['category_id'] contiene el ID de la categoría
            // Puedes ajustar según la estructura de tu formulario o petición
            $category = new Category();
            $category->setId($requestData['category_id']);
            $machinary->setCategory($category);
        }

        return $machinary;
    }
}
