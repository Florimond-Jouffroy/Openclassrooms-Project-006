<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('email', EmailType::class, [
        'label' => "Adresse Email",
        'attr' => [],
        'constraints' => [
          new Assert\NotBlank(message: "Ce champ ne peux pas etre vide !"),
          new Assert\Email(message: "Votre Email {{ value }} n'est pas valide !")
        ]
      ])
      ->add('password', RepeatedType::class, [
        'type' => PasswordType::class,
        'invalid_message' => 'Les mots de passes doivent etre identique !',
        'options' => ['attr' => ['class' => 'password-field']],
        'required' => true,
        'first_options' => ['label' => 'Mot de passe'],
        'second_options' => ['label' => 'Confirmez votre mot de passe'],

      ])
      ->add('firstname', TextType::class, [
        'label' => "Prénom",
        'attr' => [],
        'constraints' => [
          new Assert\NotBlank(message: "Ce champ ne peux pas etre vide !"),
          new Assert\Length(
            min: 3,
            minMessage: "Votre prénom doit faire plus de {{ limit }} caractères",
            max: 20,
            maxMessage: "Votre prénom ne doit pas faire plus de {{ limit }} caractères"
          )
        ]
      ])
      ->add('lastname', TextType::class, [
        'label' => "Nom",
        'attr' => [],
        'constraints' => [
          new Assert\NotBlank(message: "Ce champ ne peux pas etre vide !"),
          new Assert\Length(
            min: 3,
            minMessage: "Votre nom doit faire plus de {{ limit }} caractères",
            max: 20,
            maxMessage: "Votre nom ne doit pas faire plus de {{ limit }} caractères"
          )
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
      'constraints' => [
        new UniqueEntity("email", message: "Cette email {{ value }} est déjà utilisé")
      ]
    ]);
  }
}
