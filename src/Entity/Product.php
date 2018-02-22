<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product {

	/**
	 * @var integer
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @Groups({"product"})
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="text")
	 * @Groups({"product"})
	 */
	private $cip;

	/**
	 * @var
	 *
	 * @ORM\Column(type="text")
	 * @Groups({"product"})
	 */
	private $name;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @Groups({"product"})
	 */
	private $stock;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer")
	 * @Groups({"product"})
	 */
	private $inventory = 1;

	/**
	 * Product constructor.
	 * @param int $id
	 * @param string $cip
	 * @param $name
	 * @param int $stock
	 * @param int $inventory
	 */
	public function __construct($id, $cip, $name, $stock, $inventory) {
		$this->id = $id;
		$this->cip = $cip;
		$this->name = $name;
		$this->stock = $stock;
		$this->inventory = $inventory;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return Product
	 */
	public function setId($id) {
		$this->id = $id;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCip() {
		return $this->cip;
	}

	/**
	 * @param string $cip
	 * @return Product
	 */
	public function setCip($cip) {
		$this->cip = $cip;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 * @return Product
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getStock() {
		return $this->stock;
	}

	/**
	 * @param int $stock
	 * @return Product
	 */
	public function setStock($stock) {
		$this->stock = $stock;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getInventory() {
		return $this->inventory;
	}

	/**
	 * @param int $inventory
	 * @return Product
	 */
	public function setInventory($inventory) {
		$this->inventory = $inventory;

		return $this;
	}

	/**
	 * @param int $inventory
	 * @return Product
	 */
	public function addInventory($inventory) {
		$this->inventory += $inventory;

		return $this;
	}


}