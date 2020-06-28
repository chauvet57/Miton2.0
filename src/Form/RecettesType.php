<?php

namespace App\Form;


use App\Entity\Difficulte;
use App\Entity\Recettes;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RecettesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('categorie')
            ->add('nomRecette', TextType::class)
            ->add('prix')
            ->add('difficulte', EntityType::class, array(
                'class' => Difficulte::class,
                'choice_label' => 'nomdifficulte',
                ))
            ->add('temps', TempsType::class)
            ->add('nombrePersonne', IntegerType::class)
            ->add('image', FileType::class, array(
                'label' => 'Image(JPG/PNG)',
                'required' => false
            ))
            ->add('images', FileType::class, array(
                'label' => 'Images(JPG/PNG)',
                'multiple' => true,
                'required' => false
            ))
            ->add('etape', CollectionType::class, [
                'entry_type' => TextareaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ])
            ->add('ingredient', CollectionType::class,[
                    'entry_type' => IngredientType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                 ]);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recettes::class,
        ]);
    }
}
