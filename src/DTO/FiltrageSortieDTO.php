<?php

namespace App\DTO;

use App\Entity\Group;
use App\Entity\Site;
use App\Entity\Ville;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class FiltrageSortieDTO
{
    private ?string $nomSortie;
    private ?Site $site;
    private ?DateTime $dateDebut;
    private ?DateTime $dateFin;
    private ?Ville $ville;
    private ?bool $organisateur;
    private ?bool $inscrit;
    private ?bool $nonInscrit;
    private ?string $etat;

    /**
     * @var Collection<int, Group>
     */
    private Collection $groupes;



    public function __construct()
    {
        $this->nomSortie = null;
        $this->site = null;
        $this->dateDebut = null;
        $this->dateFin = null;
        $this->ville = null;
        $this->organisateur = null;
        $this->inscrit = null;
        $this->nonInscrit = null;
        $this->etat = null;
        $this->groupes = new ArrayCollection();

    }

    public function getNomSortie(): ?string
    {
        return $this->nomSortie;
    }

    public function setNomSortie(?string $nomSortie): FiltrageSortieDTO
    {
        $this->nomSortie = $nomSortie;
        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): FiltrageSortieDTO
    {
        $this->site = $site;
        return $this;
    }

    public function getDateDebut(): ?DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?DateTime $dateDebut): FiltrageSortieDTO
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?DateTime $dateFin): FiltrageSortieDTO
    {
        $this->dateFin = $dateFin;
        return $this;
    }


    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): FiltrageSortieDTO
    {
        $this->ville = $ville;
        return $this;
    }

    public function getOrganisateur(): ?bool
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?bool $organisateur): FiltrageSortieDTO
    {
        $this->organisateur = $organisateur;
        return $this;
    }

    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    public function setInscrit(?bool $inscrit): FiltrageSortieDTO
    {
        $this->inscrit = $inscrit;
        return $this;
    }

    public function getNonInscrit(): ?bool
    {
        return $this->nonInscrit;
    }

    public function setNonInscrit(?bool $nonInscrit): FiltrageSortieDTO
    {
        $this->nonInscrit = $nonInscrit;
        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): FiltrageSortieDTO
    {
        $this->etat = $etat;
        return $this;
    }
    /**
     * @return Collection<int, Group>
     */

    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    /**
     * @param Collection<int, Group> $groupes
     */
    public function setGroupes(Collection $groupes): FiltrageSortieDTO
    {
        $this->groupes = $groupes;
        return $this;
    }
//    public function addGroup(Group $group): void
//    {
//        $this->groupes->add($group);
//    }
//    public function removeGroup(Group $group): void
//    {
//        $this->groupes->removeElement($group);
//    }

}
