<?php

namespace model;

class MachineryPhoto
{
    private int $id;
    private string $src;
    private int $machineryId;

    public function __construct()
    {
        $this->id = -1;
        $this->src = "";
        $this->machineryId = -1;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function setSrc(string $src): void
    {
        $this->src = $src;
    }

    public function getMachineryId(): int
    {
        return $this->machineryId;
    }

    public function setMachineryId(int $machineryId): void
    {
        $this->machineryId = $machineryId;
    }

    public function getJson(): array
    {
        return [
            'id' => $this->getId(),
            'src' => $this->getSrc(),
            'machineryId' => $this->getMachineryId(),
        ];
    }
}
