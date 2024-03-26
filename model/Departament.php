<?php

namespace model;

class Departament
{
    private ?int $id;
    private string $name;
    private $municipalities;
    public static $rules = [
        'name' => 'required|string',
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->name = "";
        $this->municipalities = [];
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

    public function getJson(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function addMunicipality(Municipality $municipality)
    {
        $this->municipalities[] = $municipality;
    }


    public function getJsonWithMunipalities(): array
    {
        $json = [
            'id' => $this->id,
            'name' => $this->name,
            'municipalities' => [],
        ];

        foreach ($this->municipalities as $municipality) {
            $json['municipalities'][] = $municipality->getJson();
        }

        return $json;
    }


    public static function fillDepartamentFromRequestData(array $requestData): Departament
    {
        $departament = new Departament();
        $departament->setName($requestData['name'] ?? '');

        return $departament;
    }
}
