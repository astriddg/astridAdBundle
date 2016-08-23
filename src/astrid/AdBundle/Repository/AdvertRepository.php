<?php

namespace astrid\AdBundle\Repository;

use astrid\AdBundle\Entity\Category;
use astrid\AdBundle\Entity\City;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAds(Category $category, City $city) {


		$expiryDate = date_sub(new \Datetime(), date_interval_create_from_date_string('10 days'));

		return 	$this->createQueryBuilder('a')
	      ->where('a.date >= :date')                  
	      ->setParameter('date', $date)
	      ->andwhere('a.city = :city')
	      ->setParameter('city', $city)
	      ->andwhere('a.category = :category')
	      ->setParameter('category', $category)
	      ->getQuery()
	      ->getResult()
	      ;

	}
}
