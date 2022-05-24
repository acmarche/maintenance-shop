<?php

namespace AcMarche\MaintenanceShop\Entity;

use AcMarche\MaintenanceShop\Repository\ProduitRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ORM\Table(name: 'produit')]
#[Vich\Uploadable]
class Produit implements TimestampableInterface, Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[ORM\Column(type: 'string', nullable: false)]
    #[ORM\OrderBy(value: ['nom' => 'ASC'])]
    #[Assert\NotBlank]
    protected string $nom;
    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $position;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    protected bool $cacher = false;
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $unite;
    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    protected ?Categorie $categorie = null;
    /**
     * @var Produit[]
     */
    #[ORM\ManyToMany(targetEntity: self::class)]
    #[ORM\JoinTable(name: 'produits_associated')]
    protected Collection $associatedProducts;

    #[Vich\UploadableField(mapping: 'produit_image', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageName = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $imageSize = null;

    public function __construct()
    {
        $this->associatedProducts = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    /**
     * @throws Exception
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
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

    public function getCacher(): bool
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

    public function addAssociatedProduct(self $associatedProduct): self
    {
        if (!$this->associatedProducts->contains($associatedProduct)) {
            $this->associatedProducts[] = $associatedProduct;
        }

        return $this;
    }

    public function removeAssociatedProduct(self $associatedProduct): self
    {
        $this->associatedProducts->removeElement($associatedProduct);

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(?int $imageSize): self
    {
        $this->imageSize = $imageSize;

        return $this;
    }
}
