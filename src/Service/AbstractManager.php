<?php
/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 27/12/2017
 * Time: 21:44
 */

namespace App\Service;


use Psr\Log\LoggerInterface;

abstract class AbstractManager {

	/** @var LoggerInterface */
	private $logger;

	/**
	 * AbstractManager constructor.
	 * @param LoggerInterface $logger
	 */
	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	/**
	 * @return LoggerInterface
	 */
	protected function getLogger() {
		return $this->logger;
	}


}