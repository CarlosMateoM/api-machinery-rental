<?php

namespace model;

class Comment
{
    private int $id;
    private int $publicationId;
    private string $content;
    private string $date;
    private ?User $user;
    public static $rules = [
        "publication_id" => "required|numeric",
        "user_id" => "required|numeric",
        "content" => "required|string"
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->publicationId = -1;
        $this->content = "";
        $this->date = "";
        $this->user = null;
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

    public function getPublicationId(): int
    {
        return $this->publicationId;
    }

    public function setPublicationId(int $publicationId): void
    {
        $this->publicationId = $publicationId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    // Método para obtener la representación JSON del comentario
    public function getJson(): array
    {
        return [
            'id' => $this->id,
            'publicationId' => $this->publicationId,
            'content' => $this->content,
            'date' => $this->date,
        ];
    }

    public function getJsonWithRelations(): array
    {
        return [
            'id' => $this->id,
            'publicationId' => $this->publicationId,
            'content' => $this->content,
            'date' => $this->date,
            'user' => $this->getUser()->getJson(),
        ];
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

    public static function fillCommentFromRequestData(array $requestData): Comment
    {
        $comment = new Comment();
        $comment->setPublicationId($requestData['publication_id'] ?? null);

        if(isset($requestData['user_id'])){
            $user = new User();
            $user->setId($requestData['user_id']);
            $comment->setUser($user);
        }

        $comment->setContent($requestData['content'] ?? '');
        
        return $comment;
    }


}
