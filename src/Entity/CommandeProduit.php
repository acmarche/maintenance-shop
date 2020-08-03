<?php

namespace AcMarche\MaintenanceShop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\MaintenanceShop\Repository\CommandeProduitRepository")
 * @ORM\Table(name="commande_produits")
 *
 */
class CommandeProduit
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\MaintenanceShop\Entity\Commande", inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $commande;

     /**
     * @ORM\ManyToOne(targetEntity="AcMarche\MaintenanceShop\Entity\Produit")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $produit;

    /**
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $quantite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }


}
