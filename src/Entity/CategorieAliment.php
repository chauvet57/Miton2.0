<?php

namespace App\Entity;

use App\Repository\CategorieAlimentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieAlimentRepository::class)
 */
class CategorieAliment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_categorie_aliment;

    /**
     * @ORM\OneToMany(targetEntity=Aliments::class, mappedBy="categorie_aliment")
     */
    private $aliments;

    public function __construct()
    {
        $this->aliments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCategorieAliment(): ?string
    {
        return $this->nom_categorie_aliment;
    }

    public function setNomCategorieAliment(string $nom_categorie_aliment): self
    {
        $this->nom_categorie_aliment = $nom_categorie_aliment;

        return $this;
    }

    /**
     * @return Collection|Aliments[]
     */
    public function getAliments(): Collection
    {
        return $this->aliments;
    }

    public function addAliment(Aliments $aliment): self
    {
        if (!$this->aliments->contains($aliment)) {
            $this->aliments[] = $aliment;
            $aliment->setCategorieAliment($this);
        }

        return $this;
    }

    public function removeAliment(Aliments $aliment): self
    {
        if ($this->aliments->contains($aliment)) {
            $this->aliments->removeElement($aliment);
            // set the owning side to null (unless already changed)
            if ($aliment->getCategorieAliment() === $this) {
                $aliment->setCategorieAliment(null);
            }
        }

        return $this;
    }

    public function __tostring()
    {
        return $this->getNomCategorieAliment();
    }
}
