<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository {
	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Product::class);
	}

	/**
	 * @param $ean
	 * @return Product
	 */
	public function getFromEan($ean) {
		return $this->createNativeNamedQuery('getFromEan')->setParameter('code', (int)$ean)->getSingleResult();
	}

}
