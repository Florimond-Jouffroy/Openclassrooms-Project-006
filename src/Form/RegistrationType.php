<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('email', EmailType::class, [
        'label' => "Adresse Email",
        'attr' => [],
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
      ])
      ->add('lastname', TextType::class, [
        'label' => "Nom",
        'attr' => [],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
