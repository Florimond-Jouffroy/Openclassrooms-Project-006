<?php

namespace App\Form\Trick;

use App\Entity\Picture;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints as Assert;

class PictureType extends AbstractType
{
  public function __construct(private string $picturesUploadDirectory)
  {
  }

  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('file', FileType::class, [
        'required' => false,
        'constraints' => []
      ]);

    $builder->setEmptyData(new Picture($this->picturesUploadDirectory));
  }

  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['file_url'] = null !== $form->getData()?->getId() ? sprintf('/pictures/%s', $form->getData()->getName()) : null;
  }


  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Picture::class,
    ]);
  }
}
