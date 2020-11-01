<?php

namespace AcMarche\MaintenanceShop\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\MaintenanceShop\Repository\CommandeRepository")
 * @ORM\Table(name="commande")
 */
class Commande implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $nom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $prenom;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lieu;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $envoye = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $commentaire;

    /**
     * Trace des produits commandes
     * @ORM\Column(type="text", nullable=true)
     */
    protected $archives_produits;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\MaintenanceShop\Entity\CommandeProduit", mappedBy="commande", cascade={"remove"})
     *
     */
    protected $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function __toString()
    {
        return 'De '.$this->nom.' '.$this->prenom.' le '.$this->createdAt->format('d-m-Y H:i:s');
    }

    public function getTotalQuantite()
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

    public function getEnvoye(): ?bool
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
    public function getProduits(): Collection
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
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCommande() === $this) {
                $produit->setCommande(null);
            }
        }

        return $this;
    }


}
