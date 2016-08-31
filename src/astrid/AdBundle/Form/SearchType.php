<?php

namespace astrid\AdBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class SearchType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
			->add('category', EntityType::class, array(
				'placeholder' => 'choose a category',
				'class' => 'astridAdBundle:Category',
				'choice_label'  => 'name',
				'required' => TRUE))
			->add('city', EntityType::class, array(
				'placeholder' => 'choose a city',
				'class' => 'astridAdBundle:City',
				'choice_label'  => 'name',
				'required' => TRUE))
			->add('Search',      SubmitType::class);

	}

}