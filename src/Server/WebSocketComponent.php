<?php
/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 10/02/2018
 * Time: 10:46
 */

namespace App\Server;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketComponent implements MessageComponentInterface {
	const INVENTORY_SCAN = 'inventory_scan';
	const UPDATE_QTY = 'update_qty';

	/** @var \SplObjectStorage */
	private $clients;

	/** @var ProductRepository */
	private $product_repository;

	/** @var LoggerInterface */
	private $logger;

	/** @var SerializerInterface */
	private $serializer;

	/** @var string */
	private $last_message;

	public function __construct(LoggerInterface $logger, ProductRepository $product_repository, SerializerInterface $serializer) {
		$this->logger = $logger;
		$this->clients = new \SplObjectStorage();
		$this->product_repository = $product_repository;
		$this->serializer = $serializer;

		$this->logger->info('Create WebSocketComponent');
	}

	public function onOpen(ConnectionInterface $conn) {
		$this->clients->attach($conn);
		if ($this->last_message != '') {
			$conn->send($this->last_message);
		}
	}

	public function onClose(ConnectionInterface $closedConnection) {
		$this->clients->detach($closedConnection);
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		$this->logger->info('An error has occurred: ' . $e->getMessage());
		$conn->send('An error has occurred: ' . $e->getMessage());
	}

	public function onMessage(ConnectionInterface $from, $message) {
		$last_message = null;

		// Try to decode message
		$server_message = json_decode($message);
		if ($server_message != null) {
			$this->logger->info('Message received : ' . $message);
			if (isset($server_message->action)) {
				switch ($server_message->action) {
					case self::INVENTORY_SCAN:
						$this->broadcastMessage($this->processInventoryScan($server_message), $from, true);
						break;
					case self::UPDATE_QTY:
						$this->broadcastMessage($this->processUpdateQuantity($server_message), $from, true);
						break;
					default:
						$this->logger->warning('Action unkonwn ' . $server_message->action);
						break;
				}
			} else {
				$this->logger->warning('No action attribute in ' . $message);
			}
		} else {
			$this->logger->warning('Cannot decode json : ' . $message);
		}
	}

	/**
	 * @param $message
	 * @return string
	 */
	private function processUpdateQuantity($message) {
		if (isset($message->id) && isset($message->qty)) {
			/** @var Product $product */
			$product = $this->product_repository->find($message->id);
			if ($product != null) {
				$product->setStock($message->qty);

				return $this->saveProductAndGetMessage($product);
			} else {
				// TODO : Manager error
			}
		}
	}

	/**
	 * @param $message (json product)
	 * @return null|string
	 */
	private function processInventoryScan($scanned_product) {
		if (isset($scanned_product->cip)) {
			// SCAN ACTION
			$this->logger->debug('CIP received : ' . $scanned_product->cip);
			return $this->saveProductAndGetMessage(new Product($scanned_product->id, $scanned_product->cip, $scanned_product->name, $scanned_product->stock, $scanned_product->inventory));
		}

		// TODO : Manage error
		return null;
	}

	/**
	 * @param $message
	 * @param ConnectionInterface $source_connection
	 * @param bool $remember_message
	 */
	private function broadcastMessage($message, ConnectionInterface $source_connection, $remember_message = false) {
		if ($message != null) {
			// Send message to all clients except the source_connection
			foreach ($this->clients as $client) {
				if ($source_connection !== $client) {
					$client->send($message);
				}
			}
		}

		// if need to remember message to deliver it on new connection
		if ($remember_message) {
			$this->last_message = $remember_message;
		}
	}

	/**
	 * @param Product $product
	 * @return string
	 */
	private function saveProductAndGetMessage(Product $product) {
		$product = $this->product_repository->setOrUpdateProduct($product);

		$broadcast_messaged = $this->serializer->serialize($product, 'json', SerializationContext::create()->setGroups(['product']));
		$this->logger->debug('Message to send : ' . $broadcast_messaged);
		return $broadcast_messaged;
	}

}