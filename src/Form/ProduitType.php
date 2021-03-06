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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Produit::class,
            )
        );
    }

}
