<?php

namespace App\Command;

use App\Repository\ProductRepository;
use App\Service\WebFrontManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 11/02/2018
 * Time: 11:42
 */
class TestCommand extends AbstractCommand {

	/** @var ProductRepository */
	private $product_repository;

	/** @var WebFrontManager */
	private $web_front_manager;

	/**
	 * TestCommand constructor.
	 * @param LoggerInterface $logger
	 * @param ProductRepository $product_repository
	 */
	public function __construct(LoggerInterface $logger, ProductRepository $product_repository, WebFrontManager $web_front_manager) {
		parent::__construct($logger);
		$this->product_repository = $product_repository;
		$this->web_front_manager = $web_front_manager;
	}


	protected function configure() {
		$this
			->setName('inventory:test');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$helper = $this->getHelper('question');
		$question = new Question('Ean code ?');

		while (true) {
			$ean = $helper->ask($input, $output, $question);

			$product = $this->product_repository->getFromEan($ean);

			$output->writeln($product->getDesignation());

			$this->web_front_manager->sendProduct($product);
		}
	}
}