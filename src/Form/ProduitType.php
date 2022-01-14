<?php

namespace AcMarche\MaintenanceShop\Form;

use AcMarche\MaintenanceShop\Entity\Categorie;
use AcMarche\MaintenanceShop\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('description');
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
