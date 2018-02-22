<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;


class Message {
	/**
	 * @var string
	 *
	 * @Groups({"product"})
	 */
	private $action;

	/**
	 * @var Product
	 *
	 * @Groups({"product"})
	 */
	private $result;

	/**
	 * @var Product[]
	 *
	 * @Groups({"product"})
	 */
	private $results;

	/**
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @param string $action
	 * @return Message
	 */
	public function setAction($action) {
		$this->action = $action;

		return $this;
	}

	/**
	 * @return Product
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * @param Product $result
	 * @return Message
	 */
	public function setResult($result) {
		$this->result = $result;

		return $this;
	}

	/**
	 * @return Product[]
	 */
	public function getResults() {
		return $this->results;
	}

	/**
	 * @param Product[] $results
	 * @return Message
	 */
	public function setResults($results) {
		$this->results = $results;

		return $this;
	}

}