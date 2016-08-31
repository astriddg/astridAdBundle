<?php
// src/astrid/AdBundle/Form/PhotoType.php

namespace astrid\AdBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;



class PhotoType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('file', FileType::class, array('required' => false, 'constraints' => new File(array ('maxSize' => '2M', 'maxSizeMessage' => 'This file is too big, please choose a smaller file', 'mimeTypes' => array('image/jpeg', 'image/png', 'image/jpg'), 'mimeTypesMessage' => 'this file does not have the right extention (PNG, JPEG or JPG)'))));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'astrid\AdBundle\Entity\Photo'
    ));
  }
}