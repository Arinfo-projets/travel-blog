<?php
namespace App\Entity;

class ImageDto{
    private ?int $id = null;

    private ?string $filePath = null;

    public function __construct(
        int $id,
        string $filePath
    ){
        $this->id = $id;
        $this->filePath = $filePath;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }
}