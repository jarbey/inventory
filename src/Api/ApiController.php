<?php

namespace App\Api;

use App\Entity\Db;
use App\Entity\Sensor;
use App\Entity\SensorDataGroup;
use App\Exception\SensorNotFoundException;
use App\Model\ApiResult;
use App\Repository\DbRepository;
use App\Repository\ProductRepository;
use App\Repository\SensorRepository;
use App\Service\RrdManager;
use FOS\RestBundle\Controller\Annotations AS FOS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class ApiController
 * @package App\Api
 */
class ApiController extends FOSRestController {

	/** @var ProductRepository */
	private $product_repository;

	/**
	 * ApiController constructor.
	 * @param ProductRepository $product_repository
	 */
	public function __construct(ProductRepository $product_repository) {
		$this->product_repository = $product_repository;
	}

	/**
	 * @FOS\Get("inventory/current/products")
	 *
	 * @SWG\Response(
	 *     response=200,
	 *     description="Returns update state",
	 *     @Model(type=ApiResult::class)
	 * )
	 *
	 * @return Response
	 */
	public function getProductsAction() {
		try {
			$data = $this->product_repository->getInventoryHistory();

			$api_result = new ApiResult(ApiResult::OK, '');
			$api_result->setInfos($data);
			$api_result->setSize(count($data));

			$view = $this->view($api_result, 200);
		} catch (Exception $e) {
			$view = $this->view(new ApiResult(ApiResult::KO, $e->getMessage()), 500);
		}
		return $this->handleView($view);
	}

}