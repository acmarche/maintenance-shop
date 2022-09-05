<?php

namespace AcMarche\MaintenanceShop\Form;

use AcMarche\MaintenanceShop\Entity\Categorie;
use AcMarche\MaintenanceShop\Entity\Produit;
use AcMarche\MaintenanceShop\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add(
                'categorie',
                EntityType::class,
                [
                    'class' => Categorie::class,
                    'placeholder' => 'Sélectionnez une catégorie',
                    'query_builder' => function (CategorieRepository $categorieRepository) {
                        return $categorieRepository->getQbl();
                    },
                ]
            )
            ->add(
                'unite',
                TextType::class,
                [
                    'label' => 'Unité',
                    'help' => 'pièce, boite de x...',
                    'required' => false,
                ]
            )
            ->add(
                'quantite',
                IntegerType::class,
                [
                    'label' => 'Quantité',
                    'required' => false,
                ]
            )
            ->add('description')
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Produit::class,
            ]
        );
    }
}
