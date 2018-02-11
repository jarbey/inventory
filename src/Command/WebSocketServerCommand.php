<?php

namespace App\Command;

use App\Server\WebSocketComponent;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebSocketServerCommand extends AbstractCommand {
	protected function configure() {
		$this
			->setName('cellar:websocket:server')
			->setDescription('Start websocket server');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$server = IoServer::factory(
			new HttpServer(new WsServer(new WebSocketComponent())),
			8082,
			'127.0.0.1'
		);

		$server->run();
	}
}