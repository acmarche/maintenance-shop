<?php

namespace AcMarche\MaintenanceShop\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\MaintenanceShop\Repository\ProduitRepository")
 * @ORM\Table(name="produit")
 *
 */
class Produit implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"nom"="ASC"})
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $cacher = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $unite;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\MaintenanceShop\Entity\Categorie", inversedBy="produits")
     *
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $categorie;

    /**
     * @var Produit[]
     * @ORM\ManyToMany(targetEntity="AcMarche\MaintenanceShop\Entity\Produit")
     * @ORM\JoinTable(name="produits_associated")
     */
    protected $associatedProducts;

    public function __construct()
    {
        $this->associatedProducts = new ArrayCollection();
    }

    public function __toString()
    {
       return $this->nom;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCacher(): ?bool
    {
        return $this->cacher;
    }

    public function setCacher(bool $cacher): self
    {
        $this->cacher = $cacher;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(?string $unite): self
    {
        $this->unite = $unite;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getAssociatedProducts(): Collection
    {
        return $this->associatedProducts;
    }

    public function addAssociatedProduct(Produit $associatedProduct): self
    {
        if (!$this->associatedProducts->contains($associatedProduct)) {
            $this->associatedProducts[] = $associatedProduct;
        }

        return $this;
    }

    public function removeAssociatedProduct(Produit $associatedProduct): self
    {
        $this->associatedProducts->removeElement($associatedProduct);

        return $this;
    }


}
