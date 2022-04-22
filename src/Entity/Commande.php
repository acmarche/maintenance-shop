<?php

namespace AcMarche\MaintenanceShop\Entity;

use AcMarche\MaintenanceShop\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: 'commande')]
class Commande implements TimestampableInterface, Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected int $id;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $nom;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $prenom;
    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $lieu;
    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    protected bool $envoye = false;
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $commentaire;
    /**
     * Trace des produits commandes.
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $archives_produits;
    #[ORM\OneToMany(targetEntity: CommandeProduit::class, mappedBy: 'commande', cascade: ['remove'])]
    protected Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'De '.$this->nom.' '.$this->prenom.' le '.$this->createdAt->format('d-m-Y H:i:s');
    }

    public function getTotalQuantite(): int
    {
        $quantite = 0;
        foreach ($this->getProduits() as $commandeProduit) {
            $quantite += $commandeProduit->getQuantite();
        }

        return $quantite;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getEnvoye(): bool
    {
        return $this->envoye;
    }

    public function setEnvoye(bool $envoye): self
    {
        $this->envoye = $envoye;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getArchivesProduits(): ?string
    {
        return $this->archives_produits;
    }

    public function setArchivesProduits(?string $archives_produits): self
    {
        $this->archives_produits = $archives_produits;

        return $this;
    }

    /**
     * @return Collection|CommandeProduit[]
     */
    public function getProduits(): array|Collection
    {
        return $this->produits;
    }

    public function addProduit(CommandeProduit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setCommande($this);
        }

        return $this;
    }

    public function removeProduit(CommandeProduit $produit): self
    {
        // set the owning side to null (unless already changed)
        if ($this->produits->removeElement($produit) && $produit->getCommande() === $this) {
            $produit->setCommande(null);
        }

        return $this;
    }
}
