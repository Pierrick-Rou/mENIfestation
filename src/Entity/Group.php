<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    /**
     * @var Collection<int, Participant>
     */
    #[ORM\ManyToMany(targetEntity: Participant::class, inversedBy: 'groupe')]
    private Collection $participants;

    /**
     * @var Collection<int, Sortie>
     */
    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'groupes')]
    private Collection $sortie;

    #[ORM\ManyToOne(inversedBy: 'groupFounded')]
    #[ORM\JoinColumn(nullable: false)]
    private ?participant $groupFounder = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Sortie $sortie): static
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): static
    {
        $this->sortie->removeElement($sortie);

        return $this;
    }

    public function getGroupFounder(): ?participant
    {
        return $this->groupFounder;
    }

    public function setGroupFounder(?participant $groupFounder): static
    {
        $this->groupFounder = $groupFounder;

        return $this;
    }

    public function __toString(): string
    {
        return $this->Name;
    }
}
