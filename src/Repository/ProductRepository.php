<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository {
	const MODE_INCREASE_INVENTORY = 1;
	const MODE_UPDATE_STOCK = 2;

	public function __construct(RegistryInterface $registry) {
		parent::__construct($registry, Product::class);
	}

	/**
	 * @param Product $product
	 * @param int $mode
	 * @return Product
	 */
	public function setOrUpdateProduct(Product $product, $mode = 0) {
		/** @var Product $existing_product */
		$existing_product = $this->find($product->getId());
		if ($existing_product == null) {
			$this->getEntityManager()->persist($product);
			$this->getEntityManager()->flush($product);
			return $product;
		} else {
			if ($mode & self::MODE_INCREASE_INVENTORY) {
				$existing_product->addInventory($product->getInventory());
			} else if ($mode & self::MODE_UPDATE_STOCK) {
				$existing_product->setStock($product->getStock());
			}

			$this->getEntityManager()->flush($existing_product);
			return $existing_product;
		}
	}

}
