<?php

namespace App\Entity;

use App\Repository\RecettesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Tag;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * 
 * @ORM\Entity(repositoryClass=RecettesRepository::class)
 */
class Recettes
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
    private $nom_recette;

    /**
     * @Groups({"list"})
     * @ORM\ManyToMany(targetEntity=Categories::class, inversedBy="recettes")
     */
    private $categorie;

    /**
     * @Groups({"list"})
     * @ORM\ManyToOne(targetEntity=Prix::class, inversedBy="recettes")
     */
    private $prix;

    /**
     * @Groups({"list"})
     * @ORM\ManyToOne(targetEntity=Difficulte::class, inversedBy="recettes")
     */
    private $difficulte;

    /**
     * @Groups({"list"})
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="recette")
     */
    private $commentaires;

    /**
     * @Groups({"list"})
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @Groups({"list"})
     * @ORM\Column(type="array")
     */
    private $images;

    /**
     * @Groups({"list"})
     * @ORM\Column(type="integer")
     */
    private $nombre_personne;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

    /**
     * @Groups({"list"})
     * @ORM\Column(type="text")
     */
    private $ingredient;

    /**
     * @Groups({"list"})
     * @ORM\Column(type="text")
     */
    private $etape;

    /**
     * @Groups({"list"})
     * @ORM\Column(type="string", length=255)
     */
    private $temps;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $editor;

  

    public function __construct()
    {
        $this->categorie = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomRecette(): ?string
    {
        return $this->nom_recette;
    }

    public function setNomRecette(string $nom_recette): self
    {
        $this->nom_recette = $nom_recette;

        return $this;
    }

    /**
     * 
     * @return Collection|Categories[]
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categories $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(Categories $categorie): self
    {
        if ($this->categorie->contains($categorie)) {
            $this->categorie->removeElement($categorie);
        }

        return $this;
    }

    public function getPrix(): ?Prix
    {
        return $this->prix;
    }

    public function setPrix(?Prix $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDifficulte(): ?Difficulte
    {
        return $this->difficulte;
    }

    public function setDifficulte(?Difficulte $difficulte): self
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    /**
     * 
     * @return Collection|Commentaires[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImages()
    {
        return $this->images;
    }

    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    public function getNombrePersonne(): ?int
    {
        return $this->nombre_personne;
    }

    public function setNombrePersonne(int $nombre_personne): self
    {
        $this->nombre_personne = $nombre_personne;

        return $this;
    }

    public function getValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    public function getIngredient()
    {
        return $this->ingredient;
    }

    public function setIngredient($ingredient)
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getEtape()
    {
        return $this->etape;
    }

    public function setEtape($etape)
    {
        $this->etape = $etape;

        return $this;
    }

    public function getTemps()
    {
        return $this->temps;
    }

    public function setTemps( $temps)
    {
        $this->temps = $temps;

        return $this;
    }


    /**
     * @return $moyenne float
     */
    public function getMoyenneNote(): float {
        $moyenne = 0;
        $commentaire = $this->getCommentaires();
        foreach ($commentaire as $note) {

            $moyenne += $note->getNotes()->getNote();
        }
        //boucle verif/0
        if($commentaire->count()){
            $moyenne = round($moyenne/$commentaire->count(),1);
        } return $moyenne;
    }

    public function setMoyenneNote($moyNote)
    {
        $this->moyNote = $moyNote;

        return $this;
    }



    public function getTotalNote(): int {
        
        $commentaire = $this->getCommentaires();

        return $commentaire->count();
    }

    public function setTotalNote($note)
    {
        $this->note = $note;

        return $this;
    }


    public function deserializer($param)
    {
        return json_decode($param);
    }

    public function serializer($param)
    {
        return serialize($param);
    }
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    
}
