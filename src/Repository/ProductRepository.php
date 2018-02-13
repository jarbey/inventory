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
	 * @param Product $product
	 * @param bool $increase_inventory
	 * @return Product
	 */
	public function setOrUpdateProduct(Product $product, $increase_inventory = true) {
		/** @var Product $existing_product */
		$existing_product = $this->find($product->getId());
		if ($existing_product == null) {
			$this->getEntityManager()->persist($product);
			$this->getEntityManager()->flush($product);
			return $product;
		} else if ($increase_inventory) {
			$existing_product->addInventory($product->getInventory());
			$this->getEntityManager()->flush($existing_product);
		}
		return $existing_product;
	}

}
