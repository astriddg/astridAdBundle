<?php

namespace astrid\AdBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use astrid\AdBundle\Form\SearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use astrid\AdBundle\Entity\Advert;
use astrid\AdBundle\Form\AdvertType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AdController extends Controller
{
    public function homeAction(Request $request)
    {
    	$form   = $this->get('form.factory')->create(SearchType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

	      $em = $this->getDoctrine()->getManager();

	      $data = $form->getData();

	      $ads = $em->getRepository('astridAdBundle:Advert')->findBy(array('category' => $data['category'], 'city' => $data['city']));

	      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

	      return $this->render('astridAdBundle::index.html.twig', array('ads' => $ads, 'category' => $data['category'], 'city' => $data['city']));
    	}

    return $this->render('astridAdBundle::home.html.twig', array('form'  => $form->createView()));
    }

    public function newAdAction(Request $request) {

    	$advert = new Advert();
	    $form   = $this->get('form.factory')->create(AdvertType::class, $advert);

	    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

	      $em = $this->getDoctrine()->getManager();
	      
	      $photos = $advert->getPhotos();

	      foreach($photos as $photo) {
	      	$photo->setAdvert($advert);
	      	$em->persist($photo);
	      }
	      
	      $em->persist($advert);
	      $em->flush();


	      return $this->render('astridAdBundle::view.html.twig', array('advert' => $advert));
	    }

	    return $this->render('astridAdBundle::add.html.twig', array(
	      'form' => $form->createView(),
	    ));
    }

    public function viewAction(Advert $advert) {

    	$em = $this->getDoctrine()->getManager();

    	return $this->render('astridAdBundle::view.html.twig', array('advert' => $advert));
    }

    public function editAdAction(Request $request, Advert $advert) {

	    $form   = $this->get('form.factory')->create(AdvertType::class, $advert);

	    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

	      $em = $this->getDoctrine()->getManager();
	      
	      $photos = $advert->getPhotos();

	      foreach($photos as $photo) {
	      	if ($photo->getAdvert() == null) {
	      		$photo->setAdvert($advert);
	      		$em->persist($photo);
	      	}
	      	
	      }
	      
	      $em->flush();

	      return $this->render('astridAdBundle::view.html.twig', array('advert' => $advert));
	    }

	    return $this->render('astridAdBundle::add.html.twig', array(
	      'advert' => $advert,
	      'form' => $form->createView(),
	    ));
    }


      public function deleteAdAction(Request $request, Advert $advert)
	  {
	    $em = $this->getDoctrine()->getManager();


	    $form = $this->get('form.factory')->create();

	    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
	      $em->remove($advert);
	      $em->flush();

	      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

	      return $this->redirectToRoute('astrid_ad_home');
	    }
	    
	    return $this->render('astridAdBundle::delete.html.twig', array(
	      'advert' => $advert,
	      'form'   => $form->createView(),
	    ));
	  }
}
