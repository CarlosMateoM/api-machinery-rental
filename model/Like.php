<?php

namespace model;

class Like
{
    private int $id;
    private Publication $publication;
    private User $user;
    public static $rules = [
        "publication_id" => "required|numeric",
        "user_id" => "required|numeric"
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->publication = new Publication();
        $this->user = new User();
    }

    // Getters and setters

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public static function fillLikeFromRequestData(array $requestData): Like
    {
        $like = new Like();

        if (isset($requestData['publication_id'])) {
            $publication = new Publication();
            $publication->setId($requestData['publication_id'] ?? null);
            $like->setPublication($publication);
        }

        if (isset($requestData['user_id'])) {
            $user = new User();
            $user->setId($requestData['user_id']);
            $like->setUser($user);
        }

        return $like;
    }

    /**
     * Get the value of publication
     */ 
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Set the value of publication
     *
     * @return  self
     */ 
    public function setPublication($publication)
    {
        $this->publication = $publication;

        return $this;
    }

    public function getJson(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->getUser()->getId(),
            'publication_id' => $this->getPublication()->getId()
        ];
    }

}
