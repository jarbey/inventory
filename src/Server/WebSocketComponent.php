<?php
/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 10/02/2018
 * Time: 10:46
 */

namespace App\Server;


use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class WebSocketComponent implements MessageComponentInterface
{
	/** @var \SplObjectStorage */
	private $clients;

	/** @var string */
	private $last_message;

	public function __construct()
	{
		$this->clients = new \SplObjectStorage();
	}

	public function onOpen(ConnectionInterface $conn)
	{
		$this->clients->attach($conn);
		if ($this->last_message != '') {
			$conn->send($this->last_message);
		}
	}

	public function onClose(ConnectionInterface $closedConnection)
	{
		$this->clients->detach($closedConnection);
	}

	public function onError(ConnectionInterface $conn, \Exception $e)
	{
		$conn->send('An error has occurred: '.$e->getMessage());
		$conn->close();
	}

	public function onMessage(ConnectionInterface $from, $message)
	{
		$this->last_message = $message;
		foreach ($this->clients as $client) {
			if ($from !== $client) {
				$client->send($message);
			}
		}
	}
}