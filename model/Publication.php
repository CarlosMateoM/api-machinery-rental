<?php

namespace model;

class Publication
{
    private int $id;
    private string $title;
    private float $pricePeHour;
    private User $user; // Objeto User en lugar de user_id
    private Machinery $machinary; // Objeto Machinary en lugar de machinary_id
    private Municipality $municipality;

    public static $rules = [
        'title' => 'required|string',
        'user_id' => 'required', // Validación de objeto User
        'machinery_id' => 'required', // Validación de objeto Machinary
        'municipality_id' => 'required',
        'price_per_hour' => 'required'
    ];


    public function __construct()
    {
        $this->id = -1;
        $this->title = "";
        $this->user = new User(); // Inicializar un objeto User por defecto
        $this->machinary = new Machinery(); // Inicializar un objeto Machinary por defecto
        $this->municipality = new Municipality();
    }

    // Getters and Setters para cada propiedad...

    public function getJson(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'preci_per_hour' => $this->getPricePeHour(),
            'user_id' => $this->getUser()->getId(), // Obtener el arreglo JSON del objeto User
            'machinary_id' => $this->getMachinary()->getId(), // Obtener el arreglo JSON con relaciones del objeto Machinary
            'municipality_id' => $this->getMunicipality()->getId()
        ];
    }

    public function getJsonWithRelations(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'price_per_hour' => $this->getPricePeHour(),
            'user' => $this->getUser()->getJson(), // Obtener el arreglo JSON del objeto User
            'machinary' => $this->getMachinary()->getJsonWithRelations(), // Obtener el arreglo JSON con relaciones del objeto Machinary
            'municipality' => $this->getMunicipality()->getJsonWithRelations()
        ];
    }

    public static function fillPublicationFromRequestData(array $requestData): Publication
    {
        $publication = new Publication();

        if (isset($requestData['title'])) {
            $publication->setTitle($requestData['title']);
        }

        if (isset($requestData['price_per_hour'])) {
            $publication->setPricePeHour($requestData['price_per_hour']);
        }

        if (isset($requestData['user_id'])) {
            $user = new User();
            $user->setId($requestData['user_id']);
            $publication->setUser($user);
        }

        if (isset($requestData['machinery_id'])) {
            $machinary = new Machinery();
            $machinary->setId($requestData['machinery_id']);
            $publication->setMachinary($machinary);
        }

        if (isset($requestData['municipality_id'])) {
            $municipality = new Municipality();
            $municipality->setId($requestData['municipality_id']);
            $publication->setMunicipality($municipality);
        }

        return $publication;
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
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }


    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */ 
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of machinary
     */ 
    public function getMachinary()
    {
        return $this->machinary;
    }

    /**
     * Set the value of machinary
     *
     * @return  self
     */ 
    public function setMachinary($machinary)
    {
        $this->machinary = $machinary;

        return $this;
    }

    /**
     * Get the value of municipality
     */ 
    public function getMunicipality()
    {
        return $this->municipality;
    }

    /**
     * Set the value of municipality
     *
     * @return  self
     */ 
    public function setMunicipality($municipality)
    {
        $this->municipality = $municipality;

        return $this;
    }

    /**
     * Get the value of pricePeHour
     */ 
    public function getPricePeHour()
    {
        return $this->pricePeHour;
    }

    /**
     * Set the value of pricePeHour
     *
     * @return  self
     */ 
    public function setPricePeHour($pricePeHour)
    {
        $this->pricePeHour = $pricePeHour;

        return $this;
    }
}
