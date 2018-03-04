<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository {
	const INVENTORY_INCREASE = 1;
	const INVENTORY_SET = 2;
	const STOCK_UPDATE = 4;

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
			// Update inventory
			if ($mode & self::INVENTORY_INCREASE) {
				$existing_product->addInventory($product->getInventory());
			} else if ($mode & self::INVENTORY_SET) {
				$existing_product->setInventory($product->getInventory());
			}

			// Update stock
			if ($mode & self::STOCK_UPDATE) {
				$existing_product->setStock($product->getStock());
			}

			// Update date
			$existing_product->setDate(new \DateTime());

			$this->getEntityManager()->flush($existing_product);
			return $existing_product;
		}
	}

	/**
	 * @param int $count
	 * @param int $offset
	 * @return Product[]
	 */
	public function getInventoryHistory($count = 10, $offset = 0) {
		return $this->findBy([], ['date' => Criteria::DESC, $count, $offset]);
	}

}
