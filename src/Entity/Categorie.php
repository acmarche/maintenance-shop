<?php

namespace AcMarche\MaintenanceShop\Entity;

use AcMarche\MaintenanceShop\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
#[ORM\Table(name: 'categorie')]
class Categorie implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;
    #[ORM\Column(type: 'string', nullable: false)]
    #[ORM\OrderBy(value: ['nom' => 'ASC'])]
    #[Assert\NotBlank]
    protected $nom;
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'categorie')]
    #[ORM\OrderBy(value: ['position' => 'ASC', 'nom' => 'ASC'])]
    private $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): ArrayCollection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        // set the owning side to null (unless already changed)
        if ($this->produits->removeElement($produit) && $produit->getCategorie() === $this) {
            $produit->setCategorie(null);
        }

        return $this;
    }
}
