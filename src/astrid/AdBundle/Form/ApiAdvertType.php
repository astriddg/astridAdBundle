<?php

namespace astrid\AdBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\OptionsResolver\OptionsResolver;


class ApiAdvertType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
			->add('title', TextType::class)
			->add('description', TextType::class, array('required' => TRUE))
			->add('price', MoneyType::class, array('required' => FALSE))
			->add('category', EntityType::class, array(
				'class' => 'astridAdBundle:Category',
				'choice_label'  => 'slug',
				'required' => TRUE))
			->add('city', EntityType::class, array(
				'class' => 'astridAdBundle:City',
				'choice_label'  => 'name',
				'required' => TRUE));

	}

	public function SetDefaultOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array(
            'data_class'        => 'astrid\AdBundle\Entity\Advert',
            'csrf_protection'   => false,
        ));
	}

}