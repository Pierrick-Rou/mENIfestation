<?php

namespace App\DTO;



class VilleDTO
{
    private ?string $nom;
    private ?string $codePostal;

    public function __construct()
    {
        $this->nom = null;
        $this->codePostal = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): FiltrageSortieDTO
    {
        $this->nom = $nom;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): FiltrageSortieDTO
    {
        $this->codePostal = $codePostal;
        return $this;
    }


}
