<?php

namespace model;

class Category
{
    private ?int $id;
    private string $name;

    public function __construct()
    {
        $this->id = null;
        $this->name = '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
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
}
