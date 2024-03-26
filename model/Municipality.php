<?php

namespace model;

class Municipality
{
    private ?int $id;
    private string $name;
    private ?Departament $departament;
    private ?int $departamentId;
        public static $rules = [
        "name" => "required|string",
        "departament_id" => "required|numeric",
    ];

    public function __construct()
    {
        $this->id = null;
        $this->name = "";
        $this->departamentId = null;
        $this->departament = new Departament();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDepartamentId(): ?int
    {
        return $this->departamentId;
    }

    public function setDepartamentId(int $departamentId): void
    {
        $this->departamentId = $departamentId;
    }

  
    public function getJson(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'departamentId' => $this->departamentId,
        ];
    }

    
    public function getJsonWithRelations(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'departament' => $this->departament->getJson()
        ];
    }
  
    public function fillFromRequestData(array $requestData): void
    {
        $this->name = $requestData['name'] ?? '';
        $this->departamentId = isset($requestData['departament_id']) ? (int)$requestData['departament_id'] : null;
    }

    /**
     * Get the value of departament
     */ 
    public function getDepartament()
    {
        return $this->departament;
    }

    /**
     * Set the value of departament
     *
     * @return  self
     */ 
    public function setDepartament($departament)
    {
        $this->departament = $departament;

        return $this;
    }
}
