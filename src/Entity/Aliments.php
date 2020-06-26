<?php

namespace App\Entity;

use App\Repository\AlimentsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AlimentsRepository::class)
 */
class Aliments
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
    private $nom_aliment;

    /**
     * @ORM\ManyToOne(targetEntity=CategorieAliment::class, inversedBy="aliments")
     */
    private $categorie_aliment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAliment(): ?string
    {
        return $this->nom_aliment;
    }

    public function setNomAliment(string $nom_aliment): self
    {
        $this->nom_aliment = $nom_aliment;

        return $this;
    }

    public function getCategorieAliment(): ?CategorieAliment
    {
        return $this->categorie_aliment;
    }

    public function setCategorieAliment(?CategorieAliment $categorie_aliment): self
    {
        $this->categorie_aliment = $categorie_aliment;

        return $this;
    }

    public function __tostring()
    {
        return $this->getNomAliment();
    }
}
