<?php
/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 10/02/2018
 * Time: 10:46
 */

namespace App\Server;

use App\Entity\Message;
use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketComponent implements MessageComponentInterface {
	const INVENTORY_SCAN = 'inventory_scan';
	const UPDATE_INVENTORY_SCAN = 'update_inventory_scan';
	const UPDATE_STOCK = 'update_stock';
    const READY = 'ready';

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
						$this->broadcastMessage($this->processInventoryScan($server_message->result, ProductRepository::INVENTORY_INCREASE | ProductRepository::STOCK_UPDATE), $from, true);
						break;
					case self::UPDATE_INVENTORY_SCAN:
						$this->broadcastMessage($this->processInventoryScan($server_message->result, ProductRepository::INVENTORY_SET), $from, true);
						break;
					case self::UPDATE_STOCK:
						$this->broadcastMessage($this->processUpdateStock($server_message->result), $from, true);
						break;
                    case self::READY:
                        $this->logger->warning('Database engine ready');
                        break;
					default:
						$this->logger->warning('Action unknown ' . $server_message->action);
						$this->broadcastMessage($message, $from, false);
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
	private function processUpdateStock($message) {
		if (isset($message->id) && isset($message->qty)) {
			/** @var Product $product */
			$product = $this->product_repository->find($message->id);
			if ($product != null) {
				$product->setStock($message->qty);

				$product = $this->product_repository->setOrUpdateProduct($product, ProductRepository::STOCK_UPDATE);

				return $this->getMessage($product, self::UPDATE_STOCK);
			} else {
				// TODO : Manager error
			}
		}
		return null;
	}

	/**
	 * @param $scanned_product (json result message)
	 * @param int $mode
	 * @return null|string
	 */
	private function processInventoryScan($scanned_product, $mode) {
		if (isset($scanned_product->cip)) {
			// SCAN ACTION
			$this->logger->debug('CIP received : ' . $scanned_product->cip);

			$product = new Product($scanned_product->id, $scanned_product->cip, $scanned_product->name, $scanned_product->stock, $scanned_product->inventory);

			return $this->getMessage($this->product_repository->setOrUpdateProduct($product, $mode), self::INVENTORY_SCAN);
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
	 * @param string $action
	 * @return string
	 */
	private function getMessage(Product $product, $action) {
		$message = new Message();
		$message->setAction($action);
		$message->setResult($product);

		$broadcast_messaged = $this->serializer->serialize($message, 'json', SerializationContext::create()->setGroups(['product']));
		$this->logger->info('Message to send : ' . $broadcast_messaged);
		return $broadcast_messaged;
	}

}