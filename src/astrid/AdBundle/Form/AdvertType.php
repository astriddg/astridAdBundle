<?php

namespace astrid\AdBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use astrid\AdBundle\Entity\Photo;


class AdvertType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
			->add('title', TextType::class, array('required' => TRUE))
			->add('description', CKEditorType::class, array('required' => TRUE))
			->add('price', MoneyType::class, array('required' => FALSE))
			->add('photos', CollectionType::class, array('entry_type' => PhotoType::class, 'allow_add' => true, 'allow_delete' => true, 'data'=>array(new Photo(),
            new Photo(), new Photo())))
			->add('category', EntityType::class, array(
				'placeholder' => 'choose a category',
				'class' => 'astridAdBundle:Category',
				'choice_label'  => 'name',
				'required' => TRUE))
			->add('city', EntityType::class, array(
				'placeholder' => 'choose a category',
				'class' => 'astridAdBundle:City',
				'choice_label'  => 'name',
				'required' => TRUE))
			->add('save',      SubmitType::class);

	}

}