<?php
/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 25/12/2017
 * Time: 12:15
 */

namespace App\Service;

use App\Entity\Product;
use JMS\Serializer\SerializationContext;
use Psr\Log\LoggerInterface;
use JMS\Serializer\SerializerInterface;
use Ratchet\Client\Connector;
use React\EventLoop\Factory;
use WebSocket\Client;

class WebFrontManager extends AbstractManager {

	/** @var SerializerInterface */
	private $serializer;

	/** @var string */
	private $websocket_host;

	/**
	 * WebFrontManager constructor.
	 * @param LoggerInterface $logger
	 * @param SerializerInterface $serializer
	 */
	public function __construct(LoggerInterface $logger, SerializerInterface $serializer, $websocket_host) {
		parent::__construct($logger);
		$this->serializer = $serializer;

		$this->websocket_host = $websocket_host;
	}

	/**
	 * @param Product $product
	 */
	public function sendProduct(Product $product) {
		$msg = $this->serializer->serialize($product, 'json', SerializationContext::create()->setGroups(['product']));

		$client = new Client('ws://' . $this->websocket_host);
		$client->send($msg);
	}
}