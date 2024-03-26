<?php

namespace model;

class User
{
    private int $id;
    private string $names;
    private string $lastNames;
    private string $email;
    private string $dateOfBirth;
    private string $username;
    private string $password;
    private string $phone;
    private string $resetToken;
    public static $rules = [
        'names' => 'required|string',
        'lastNames' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'dateOfBirth' => 'required|date',
        'username' => 'required|string',
        'password' => 'required',
        'phone' => 'required',
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->names = "";
        $this->lastNames = "";
        $this->email = "";
        $this->dateOfBirth = "";
        $this->username = "";
        $this->password = "";
        $this->phone = "";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getNames(): string
    {
        return $this->names;
    }

    public function setNames(string $names)
    {
        $this->names = $names;
    }

    public function getLastNames(): string
    {
        return $this->lastNames;
    }

    public function setLastNames(string $lastNames)
    {
        $this->lastNames = $lastNames;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getDateOfBirth(): string
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(string $dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }
    
    public function getResetToken()
    {
        return $this->resetToken;
    }

  
    public function setResetToken($resetToken)
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getJson(): array
    {
        return array(
            'id' => $this->getId(),
            'names' => $this->getNames(),
            'lastNames' => $this->getLastNames(),
            'email' => $this->getEmail(),
            'dateOfBirth' => $this->getDateOfBirth(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'phone' => $this->getPhone(),
        );
    }

    public function getJsonWithRelations(): array
    {

        return array(
            'id' => $this->getId(),
            'names' => $this->getNames(),
            'lastNames' => $this->getLastNames(),
            'email' => $this->getEmail(),
            'dateOfBirth' => $this->getDateOfBirth(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'phone' => $this->getPhone(),
        );
    }


    public static function fillUserFromRequestData(array $requestData): User
    {
        $user = new User();

        if (isset($requestData['names'])) {
            $user->setNames($requestData['names']);
        }

        if (isset($requestData['lastNames'])) {
            $user->setLastNames($requestData['lastNames']);
        }

        if (isset($requestData['email'])) {
            $user->setEmail($requestData['email']);
        }

        if (isset($requestData['dateOfBirth'])) {
            $user->setDateOfBirth($requestData['dateOfBirth']);
        }

        if (isset($requestData['username'])) {
            $user->setUsername($requestData['username']);
        }

        if (isset($requestData['password'])) {
            $user->setPassword($requestData['password']);
        }

        if (isset($requestData['phone'])) {
            $user->setPhone($requestData['phone']);
        }

        return $user;
    }
}
