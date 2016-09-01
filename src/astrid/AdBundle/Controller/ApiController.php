<?php

namespace astrid\AdBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use astrid\AdBundle\Entity\Advert;
use astrid\AdBundle\Form\ApiAdvertType;


class ApiController extends Controller 
{

	/**
	*@Route("/api/get")
	*@Method("POST")
	*/
	public function getAction(Request $request) {

		if($request->query->has('city') && $request->query->has('category')) {
			return $this->cityCatAction($request);
		}
		if($request->query->has('city')) {
			return $this->cityAction($request);
		}
		if($request->query->has('category')) {
			return $this->catAction($request);
		}
		if($request->query->has('advert')) {
			return $this->adAction($request);
		}
		else {
			$error = array('status' => 400, 'error' => 'Please check your request again.');
	        $response = new JsonResponse($error);
	        return $response;
		}
	}

	public function cityCatAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		if (is_numeric($request->query->get('city'))) {
			$city = $em->getRepository('astridAdBundle:City')->findOneById($request->query->get('city'));
		}
		else {
			$city = $em->getRepository('astridAdBundle:City')->findOneByName($request->query->get('city'));
		}

		if ($city == null) {
			$error = array('status' => 404, 'message' => 'The city you entered does not exist.');
	        $response = new JsonResponse($error);
	        return $response;
		}

		if (is_numeric($request->query->get('category'))) {
			$category = $em->getRepository('astridAdBundle:Category')->findOneById($request->query->get('category'));
		}
		else {
			$category = $em->getRepository('astridAdBundle:Category')->findOneBySlug($request->query->get('category'));
		}

		if ($category == null) {
			$error = array('status' => 404, 'message' => 'The category you entered does not exist.');
	        $response = new JsonResponse($error);
	        return $response;
		}

		$adverts = $em->getRepository('astridAdBundle:Advert')->findBy(array('category' => $category, 'city' => $city));

		// pagination

		$advertList = [];

		foreach ($adverts as $advert) {
			$advertList[] = array('id' => $advert->getId(), 'title' => $advert -> getTitle());
		}

		$response = array('status' => 200, 'message' => 'the request was successful.', 'adverts' => $advertList);

		return new JsonResponse($response);
	}

	public function cityAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		if (is_numeric($request->query->get('city'))) {
			$city = $em->getRepository('astridAdBundle:City')->findOneById($request->query->get('city'));
		}
		else {
			$city = $em->getRepository('astridAdBundle:City')->findOneByName($request->query->get('city'));
		}

		if ($city == null) {
			$error = array('status' => 404, 'message' => 'The city you entered does not exist.');
	        $response = new JsonResponse($error);
	        return $response;
		}

		$adverts = $em->getRepository('astridAdBundle:Advert')->findBy(array('city' => $city));

		// pagination

		$advertList = [];

		foreach ($adverts as $advert) {
			$advertList[] = array('id' => $advert->getId(), 'title' => $advert -> getTitle());
		}

		$response = array('status' => 200, 'message' => 'the request was successful.', 'adverts' => $advertList);

		return new JsonResponse($response);
	}

	public function catAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		if (is_numeric($request->query->get('category'))) {
			$category = $em->getRepository('astridAdBundle:Category')->findOneById($request->query->get('category'));
		}
		else {
			$category = $em->getRepository('astridAdBundle:Category')->findOneBySlug($request->query->get('category'));
		}

		if ($category == null) {
			$error = array('status' => 404, 'message' => 'The category you entered does not exist.');
	        $response = new JsonResponse($error);
	        $response->setCharset('UTF-8');
	        return $response;
		}

		$adverts = $em->getRepository('astridAdBundle:Advert')->findBy(array('category' => $category));

		// pagination

		$advertList = [];

		foreach ($adverts as $advert) {
			$advertList[] = array('id' => $advert->getId(), 'title' => $advert -> getTitle());
		}

		$response = array('status' => 200, 'message' => 'the request was successful.', 'adverts' => $advertList);

		return new JsonResponse($response);
	}

	public function adAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		if (is_numeric($request->query->get('advert'))) {
			$advert = $em->getRepository('astridAdBundle:Advert')->findOneById($request->query->get('advert'));
		}
		else {
			$advert = $em->getRepository('astridAdBundle:advert')->findOneBySlug($request->query->get('advert'));
		}

		if ($advert == null) {
			$error = array('status' => 404, 'message' => 'The advert you entered does not exist.');
	        $response = new JsonResponse($error);
	        $response->setCharset('UTF-8');
	        return $response;
		}


		$response = array('status' => 200, 'message' => 'the request was successful.', 'advert' => array('id' => $advert->getId(), 'title' => $advert -> getTitle(), 'description' => $advert->getDescription(), 'price' => $advert->getPrice(), 'category' => $advert->getCategory()->getName(), 'city' => $advert->getCity()->getName(), 'photos' => count($advert->getPhotos()), 'date' => $advert->getDate()));

		return new JsonResponse($response);
	}

	/**
	*@Route("/api/add")
	*@Method("POST")
	*/
	public function addAction(Request $request) {

		$body = $request->getContent();
		$data= json_decode($body, true);

		$advert = new Advert();




		$form = $this->get('form.factory')->create(ApiAdvertType::class, $advert);
		$form->submit($data);


		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($advert);
	      	$em->flush();

	      	$response = array('status' => 200, 'message' => 'the request was successful. The advert has been added.');
			return new JsonResponse($response);

		}
			file_put_contents('errorsapi.txt', $form->getErrors());

			$error = array('status' => 400, 'error' => 'Oops, there was a mistake in your request, did you appropriately fill all the fields?');
	        return new JsonResponse($error);		

	}
}