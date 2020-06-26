<?php

namespace App\Form;

use App\Entity\Aliments;
use App\Entity\CategorieAliment;
use App\Entity\Unite;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categorie_aliment', EntityType::class, array(
                'class' => CategorieAliment::class,
                'choice_label' => 'nomcategoriealiment',
                ))
            ->add('aliment', EntityType::class, array(
                'class' => Aliments::class,
                'choice_label' => 'nomaliment',
                ))
            ->add('ingredient', TextType::class)
            ->add('quantite', TextType::class)
            ->add('unite', EntityType::class, array(
                'class' => Unite::class,
                'choice_label' => 'nomunite',
                ))
        ;
    }

   
}