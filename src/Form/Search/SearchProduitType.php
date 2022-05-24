<?php

namespace AcMarche\MaintenanceShop\Form\Search;

use AcMarche\MaintenanceShop\Entity\Categorie;
use AcMarche\MaintenanceShop\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchProduitType extends AbstractType
{
    public function __construct(private CategorieRepository $categorieRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Mot clef',
                    ],
                ]
            )
            ->add(
                'categorie',
                EntityType::class,
                [
                    'class' => Categorie::class,
                    'query_builder' => $this->categorieRepository->getQbl(),
                    'required' => false,
                    'placeholder' => 'Choisissez une catégorie',
                ]
            );
    }
}
