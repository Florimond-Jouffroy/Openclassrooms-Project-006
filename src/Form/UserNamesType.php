<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserNamesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => [],
                'constraints' => [
                    new Assert\NotBlank(message: 'Votre prénom ne peux pas etre vide'),
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Votre Prénom doit faire plus de {{ limit }} caractères !',
                        max: 20,
                        maxMessage: 'Votre prénom ne peux pas faire plus {{ limit }} caractères !',
                    ),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => [],
                'constraints' => [
                    new Assert\NotBlank(message: 'Votre nom ne peux pas etre vide !'),
                    new Assert\Length(
                        min: 3,
                        minMessage: 'Votre nom doit faire plus de {{ limit }} caractères !',
                        max: 20,
                        maxMessage: 'Votre nom ne peux pas faire plus {{ limit }} caractères !',
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
