<?php
/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 25/12/2017
 * Time: 12:15
 */

namespace App\Service;

use App\Entity\Product;
use App\Entity\SensorDataGroup;
use JMS\Serializer\SerializationContext;
use Psr\Log\LoggerInterface;
use JMS\Serializer\SerializerInterface;

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
		\Ratchet\Client\connect('ws://' . $this->websocket_host)->then(function(\Ratchet\Client\WebSocket $conn) use ($product) {
			$conn->send($this->serializer->serialize($product, 'json', SerializationContext::create()->setGroups(['product'])));
			$conn->close();
		}, function (\Exception $e) {
			echo "Could not connect: {$e->getMessage()}\n";
		});
	}
}