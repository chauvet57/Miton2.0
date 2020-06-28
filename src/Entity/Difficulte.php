<?php

namespace App\Entity;

use App\Repository\DifficulteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DifficulteRepository::class)
 */
class Difficulte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list"})
     */
    private $id;

    /**
     * @Groups({"list"})
     * @ORM\Column(type="string", length=255)
     */
    private $nom_difficulte;

    /**
     * @ORM\OneToMany(targetEntity=Recettes::class, mappedBy="difficulte")
     */
    private $recettes;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDifficulte(): ?string
    {
        return $this->nom_difficulte;
    }

    public function setNomDifficulte(string $nom_difficulte): self
    {
        $this->nom_difficulte = $nom_difficulte;

        return $this;
    }

    /**
     * @return Collection|Recettes[]
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recettes $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes[] = $recette;
            $recette->setDifficulte($this);
        }

        return $this;
    }

    public function removeRecette(Recettes $recette): self
    {
        if ($this->recettes->contains($recette)) {
            $this->recettes->removeElement($recette);
            // set the owning side to null (unless already changed)
            if ($recette->getDifficulte() === $this) {
                $recette->setDifficulte(null);
            }
        }

        return $this;
    }
}
