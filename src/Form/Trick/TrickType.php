<?php

namespace App\Form\Trick;

use App\Entity\Trick;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('name', TextType::class, [
        'label' => "Nom de la figure",
        'attr' => ['placeholder' => "Nom de la figure..."],
        'constraints' => [
          new Assert\NotBlank(message: "Vous devez entrez un nom !"),
          new Assert\Length(
            min: 3,
            minMessage: "Le nom doit faire plus de {{ limit }} caractères !",
            max: 40,
            maxMessage: "Le nom ne peux pas faire plus de {{ limit }} caractères !"
          )
        ]

      ])
      ->add('description', TextareaType::class, [
        'label' => "Description de la figure",
        'required' => false,
        'attr' => ['placeholder' => "Description de la figure ..."],
        'constraints' => [
          new Assert\NotBlank(message: "Vous devez entrez une description !"),
          new Assert\Length(
            min: 10,
            minMessage: "La figure doit avoir une description d'au-moins {{limit}} caractères"
          )
        ]
      ])
      ->add('category', EntityType::class, [
        'label' => "Catégorie",
        'placeholder' => "-- Choisir une catégorie --",
        'required' => false,
        'class' => Category::class,
        'choice_label' => function (Category $category) {
          return strtoupper($category->getName());
        }
      ])
      ->add('pictures', CollectionType::class, [
        'entry_type' => PictureType::class,
        'allow_add' => true,
        'allow_delete' => true
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Trick::class,
    ]);
  }
}
