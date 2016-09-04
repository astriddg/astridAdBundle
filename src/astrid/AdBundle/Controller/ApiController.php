<?php

namespace astrid\AdBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use astrid\AdBundle\Entity\Advert;
use astrid\AdBundle\Entity\City;
use astrid\AdBundle\Entity\Category;
use astrid\AdBundle\Entity\Photo;
use astrid\AdBundle\Form\ApiAdvertType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


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

		$advertList = $this->paginate($advertList, $request);

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

		$advertList = $this->paginate($advertList, $request);

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

		$advertList = $this->paginate($advertList, $request);

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
	        return new JsonResponse($error);
	       
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


		if ($cityCat = $this->requestValid($data)) {
			$advert = $this->hydrateAdvert($data, $cityCat);
			$em = $this->getDoctrine()->getManager();
			$em->persist($advert);
	      	$em->flush();

	      	$response = array('status' => 200, 'message' => 'the request was successful. The advert has been added.');
			return new JsonResponse($response);
		}

		$error = array('status' => 400, 'error' => 'Oops, there was a mistake in your request, did you appropriately fill all the fields?');
	        return new JsonResponse($error);

	}

/**
    *@Route("/api/addphoto")
    *@Method("POST")
    */
	public function addPhotoAction(Request $request) {
		$photos = $request->files->all();

		$em = $this->getDoctrine()->getManager();

		if (is_numeric($request->query->get('advert'))) {
			$advert = $em->getRepository('astridAdBundle:Advert')->findOneById($request->query->get('advert'));
		}
		else {
			$advert = $em->getRepository('astridAdBundle:advert')->findOneBySlug($request->query->get('advert'));
		}

		if ($advert == null) {
			$error = array('status' => 404, 'message' => 'The advert you entered does not exist.');
	        return new JsonResponse($error);
	       
		}

		if ($this->photosValid($photos, $advert)) {
			foreach($photos as $file) {
				$photo = new Photo();
				$photo->setFile($file);
				$photo->setAdvert($advert);
				$em->persist($photo);
			}

			$em->flush();
		}
		return new JsonResponse('reponse');
	}
	
	/**
	*@Route("/api/addcity")
	*@Method("POST")
	*/
	public function addCityAction(Request $request) {

		$body = json_decode($request->getContent(), true);
		$cityName = $body['city'];

		$em = $this->getDoctrine()->getManager();

		if (!$em->getRepository('astridAdBundle:City')->findOneByName($cityName)) {
			$city = new City();
			$city->setName($cityName);
			$em->persist($city);
	      	$em->flush();

	      	$response = array('status' => 200, 'message' => 'the request was successful. The city has been added.');
			return new JsonResponse($response);
		}

		$error = array('status' => 409, 'error' => 'Oops, looks like this city exists already..');
	     return new JsonResponse($error);		


	} 

	/**
	*@Route("/api/addcat")
	*@Method("POST")
	*/
	public function addCatAction(Request $request) {

		$body = json_decode($request->getContent(), true);
		$categoryName = $body['category'];

		$em = $this->getDoctrine()->getManager();

		if (!$em->getRepository('astridAdBundle:Category')->findOneByName($categoryName)) {
			$category = new Category();
			$category->setName($categoryName);
			$em->persist($category);
	      	$em->flush();

	      	$response = array('status' => 200, 'message' => 'the request was successful. The category has been added.');
			return new JsonResponse($response);
		}

		$error = array('status' => 409, 'error' => 'Oops, looks like this category exists already..');
	    return new JsonResponse($error);		


	} 


	/**
	*@Route("/api/edit")
	*@Method("POST")
	*/
	public function editAction(Request $request) {

		$em = $this->getDoctrine()->getManager();

		if (is_numeric($request->query->get('advert'))) {
			$advert = $em->getRepository('astridAdBundle:Advert')->findOneById($request->query->get('advert'));
		}
		else {
			$advert = $em->getRepository('astridAdBundle:advert')->findOneBySlug($request->query->get('advert'));
		}

		if ($advert == null) {
			$error = array('status' => 404, 'message' => 'The advert you entered does not exist.');
	        return new JsonResponse($error);
	       
		}


			$body = $request->getContent();
			$data= json_decode($body, true);

			if (isset($data['title'])) {
				$advert->setTitle($data['title']);
			}

			if (isset($data['description'])) {
				$advert->setDescription($data['description']);
			}

			if (isset($data['price'])) {
				$advert->setPrice($data['price']);
			}

			if (isset($data['category'])) {

				if (is_numeric($data['category'] )) {
					$category = $em->getRepository('astridAdBundle:Category')->findOneById($data['category']);
				}
				else {
					$category  = $em->getRepository('astridAdBundle:Category')->findOneBySlug($data['category']);
				}

				if ($category == null) {
			        $error = array('status' => 400, 'error' => 'Oops, this category does not exist. Please add it using "api/addcat".');
	     			return new JsonResponse($error);
				}

				$advert->setCategory($category);
				
			}

			if (isset($data['city'])) {

				if (is_numeric($data['city'] )) {
					$advert = $em->getRepository('astridAdBundle:City')->findOneById($data['city']);
				}
				else {
					$advert  = $em->getRepository('astridAdBundle:City')->findOneByName($data['city']);
				}

				if ($city == null) {
			        $error = array('status' => 400, 'error' => 'Oops, this city does not exist. Please add it using "api/addcity".');
	     			return new JsonResponse($error);
				}

				$advert->setCity($city);
				
			}

			$em->flush();

			$response = array('status' => 200, 'message' => 'the request was successful. The advert was edited..');
			return new JsonResponse($response);

	}

	/**
	*@Route("/api/delete")
	*@Method("DELETE")
	*/
	public function deleteAction(Request $request) {
		$em = $this->getDoctrine()->getManager();

		if (is_numeric($request->query->get('advert'))) {
			$advert = $em->getRepository('astridAdBundle:Advert')->findOneById($request->query->get('advert'));
		}
		else {
			$advert = $em->getRepository('astridAdBundle:advert')->findOneBySlug($request->query->get('advert'));
		}

		if ($advert == null) {
			$error = array('status' => 404, 'message' => 'The advert you entered does not exist.');
	        return new JsonResponse($error);
	       
		}

		$photos = $em->getRepository('astridAdBundle:Photo')->findBy(array('advert' => $advert));

		foreach($photos as $photo) {
			$em->remove($photo);
		}

		$em->remove($advert);
		$em->flush();	

		$response = array('status' => 200, 'message' => 'the request was successful. The advert was deleted..');
			return new JsonResponse($response);	

	}


	public function requestValid($data) {
		if (isset($data['title']) && isset($data['description']) && isset($data['city']) && isset($data['category'] )) {

			if (strlen($data['title'])> 5 && strlen($data['description']) > 10) {

				$cityCat = array();
				$em = $this->getDoctrine()->getManager();

				if($em->getRepository('astridAdBundle:Advert')->findOneByTitle($data['title'])) {
					$error = array('status' => 409, 'message' => 'Looks like this title already exists. Please use a new title.');
			        return new JsonResponse($error);
				}

				if (is_numeric($data['city'])) {
					$cityCat['city'] = $em->getRepository('astridAdBundle:City')->findOneById($data['city']);

				}
				else {
					$cityCat['city'] = $em->getRepository('astridAdBundle:City')->findOneByName($data['city']);
				}

				if ($cityCat['city'] == null) {
			        $error = array('status' => 404, 'message' => 'The city you entered does not exist.');
			        return new JsonResponse($error);
				}

				if (is_numeric($data['category'] )) {
					$cityCat['cat'] = $em->getRepository('astridAdBundle:Category')->findOneById($data['category']);
				}
				else {
					$cityCat['cat']  = $em->getRepository('astridAdBundle:Category')->findOneBySlug($data['category']);
				}

				if ($cityCat['cat']  == null) {
					$error = array('status' => 404, 'message' => 'The category you entered does not exist.');
			        return new JsonResponse($error);
			        
				}

				if(isset($data['price'])) {
					if(!is_numeric($data['price'])) {
						$error = array('status' => 400, 'message' => 'Please enter price in numerical value.');
			        	return new JsonResponse($error);
					}
				}

				return $cityCat;
			}
		}

		return false;
	}

	public function photosValid($photos, $advert) {

		$em = $this->getDoctrine()->getManager();
		$numberPhotos = count($em->getRepository('astridAdBundle:Photo')->findBy(array('advert' => $advert)));

		if ((count($photos) + $numberPhotos) > 3) {
			$error = array('status' => 400, 'message' => 'There are too many pictures for this advert, please upload fewer.');
			return new JsonResponse($error);
		}


		foreach ($photos as $photo) {

			if (!$photo->guessExtension() == ('jpg' || 'jpeg' || 'png') || filesize($photo) > pow(8, 6)) { 
				$error = array('status' => 400, 'message' => 'The picture' . $photo . 'you have uploaded is either too large or the wrong format.');
				return new JsonResponse($error);
			} 
		}

		return true;
	}

	public function hydrateAdvert($data, $cityCat) {

		$advert = new Advert();

		$advert->setTitle($data['title']);
		$advert->setDescription($data['description']);
		$advert->setCity($cityCat['city']);
		$advert->setCategory($cityCat['cat']);
		if (isset($data['price'])) {
			$advert->setPrice($data['price']);
		}
		
		return $advert;

	}

	public function paginate($advertList, Request $request) {
		if (count($advertList) > 10) {
			if ($request->query->has('page') && $request->query->get('page') > 1) {
				$page = $request->query->get('page');
				$offset = ($page - 1) * 10;
				$advertList = array_slice($advertList, $offset, 10);
			}

			else {
				$advertList = array_slice($advertList, 0, 10);
			}
		}

		return $advertList;
	}
}