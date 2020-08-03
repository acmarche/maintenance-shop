<?php

namespace AcMarche\MaintenanceShop\Form\Search;

use AcMarche\MaintenanceShop\Repository\CategorieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchProduitType extends AbstractType
{
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = $this->categorieRepository->getForSearch();

        $builder
            ->add(
                'nom',
                SearchType::class,
                array(
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Mot clef',
                    ),
                )
            )
            ->add(
                'categorie',
                ChoiceType::class,
                array(
                    'choices' => $categories,
                    'required' => false,
                    'placeholder' => 'Choisissez une catégorie',
                )
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Rechercher',
                )
            )
            ->add(
                'raz',
                SubmitType::class,
                array(
                    'label' => 'Raz',
                    'attr' => array(
                        'class' => 'btn-sm btn-info',
                        'title' => 'Réinitialiser la recherche',
                    ),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
    }

}
