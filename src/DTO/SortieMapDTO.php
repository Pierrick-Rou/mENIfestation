<?php

namespace App\DTO;

class SortieMapDTO
{
    private int $id;
    private float $latitude;
    private float $longitude;
    private string $title;
    private ?string $description = null;
    private string $date;
    private string $lieu;
    private string $Adresse;

    public function __construct()
    {
        $this->id = 0;
        $this->latitude = 0;
        $this->longitude = 0;
        $this->title = '';
        $this->description = null;
        $this->date = '';
        $this->lieu = '';
        $this->Adresse = '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): SortieMapDTO
    {
        $this->id = $id;
        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): SortieMapDTO
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): SortieMapDTO
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): SortieMapDTO
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): SortieMapDTO
    {
        $this->description = $description;
        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): SortieMapDTO
    {
        $this->date = $date;
        return $this;
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): SortieMapDTO
    {
        $this->lieu = $lieu;
        return $this;
    }

    public function getAdresse(): string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): SortieMapDTO
    {
        $this->Adresse = $Adresse;
        return $this;
    }


}
