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
use Psr\Log\LoggerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketComponent implements MessageComponentInterface {
	/** @var \SplObjectStorage */
	private $clients;

	/** @var ProductRepository */
	private $product_repository;

	/** @var LoggerInterface */
	private $logger;

	/** @var string */
	private $last_message;

	public function __construct(LoggerInterface $logger, ProductRepository $product_repository) {
		$this->logger = $logger;
		$this->clients = new \SplObjectStorage();
		$this->product_repository = $product_repository;

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
		//$conn->close();
	}

	public function onMessage(ConnectionInterface $from, $message) {
		$last_message = null;
		$this->logger->info('Message received : ' . $message);

		$server_message = json_decode($message);
		if ($server_message != null) {
			if (isset($server_message->cip)) {
				$this->logger->debug('Message has CIP : ' . $server_message->cip);
				$scanned_product = $server_message;

				$product = new Product($scanned_product->id, $scanned_product->cip, $scanned_product->name, $scanned_product->stock, $scanned_product->inventory);
				$product = $this->product_repository->setOrUpdateProduct($product);

				// TODO : Use JMS Serializer here
				$last_message = json_encode($product);
				$this->logger->debug('Product to send : ' . $last_message);
			} else {
				$this->logger->debug('Message without CIP');
				$last_message = $message;
			}
		}

		if ($last_message != null) {
			// Send message to all clients
			$this->last_message = $last_message;
			foreach ($this->clients as $client) {
				if ($from !== $client) {
					$client->send($last_message);
				}
			}
		}
	}
}