<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ORM\NamedNativeQueries({
 *      @ORM\NamedNativeQuery(
 *          name                = "getFromEan",
 *          resultSetMapping	= "mappingFromEan",
 *          query               = "SELECT pdt.t_produit_id AS id, pdt.codecip AS codecip, pdt.designation AS designation, geo.quantite stock FROM t_produit pdt JOIN t_produitgeographique geo ON pdt.t_produit_id = geo.t_produit_id WHERE pdt.codecip = :code OR pdt.codecip7 = :code OR pdt.t_produit_id IN (SELECT t_produit_id from t_code_ean13 where code_ean13 = :code)"
 *      ),
 * })
 * @ORM\SqlResultSetMappings({
 *      @ORM\SqlResultSetMapping(
 *          name    			= "mappingFromEan",
 *          entities			= {
 *              @ORM\EntityResult(
 *                  entityClass = "__CLASS__",
 *                  fields      = {
 *                      @ORM\FieldResult(name = "id", column = "ID"),
 *                      @ORM\FieldResult(name = "codecip", column = "CODECIP"),
 *                      @ORM\FieldResult(name = "designation", column = "DESIGNATION"),
 *                      @ORM\FieldResult(name = "stock", column = "STOCK"),
 *                  }
 *              )
 *          }
 *      )
 * })
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
	private $codecip;

	/**
	 * @var
	 *
	 * @ORM\Column(type="text")
	 * @Groups({"product"})
	 */
	private $designation;

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
	private $inventaire = 1;

	/**
	 * Product constructor.
	 * @param int $id
	 * @param string $codecip
	 * @param $designation
	 * @param int $stock
	 * @param int $inventaire
	 */
	public function __construct($id, $codecip, $designation, $stock, $inventaire) {
		$this->id = $id;
		$this->codecip = $codecip;
		$this->designation = $designation;
		$this->stock = $stock;
		$this->inventaire = $inventaire;
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
	public function getCodecip() {
		return $this->codecip;
	}

	/**
	 * @param string $codecip
	 * @return Product
	 */
	public function setCodecip($codecip) {
		$this->codecip = $codecip;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getDesignation() {
		return $this->designation;
	}

	/**
	 * @param mixed $designation
	 * @return Product
	 */
	public function setDesignation($designation) {
		$this->designation = $designation;

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
	public function getInventaire() {
		return $this->inventaire;
	}

	/**
	 * @param int $inventaire
	 * @return Product
	 */
	public function setInventaire($inventaire) {
		$this->inventaire = $inventaire;

		return $this;
	}
}