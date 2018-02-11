<?php

namespace App\Controller;

use App\Entity\Db;
use App\Repository\DbRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Created by PhpStorm.
 * User: jarbey
 * Date: 05/02/2018
 * Time: 22:40
 */
class IndexController extends Controller {

	/**
	 * @Route("/", name="home")
	 */
	public function home(Request $request) {
		return $this->render('home.html.twig', [
			'ws_url' => 'inventory.pharmacie-pouvreau.fr/ws',
		]);
	}
}