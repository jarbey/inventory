<?php

namespace App\Model;

/**
 * Class ApiResult
 * @package App\Model
 */
class ApiResult {
	const OK = 'ok';
	const KO = 'ko';

	/**
	 * @var integer
	 */
	private $code;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $details;

	/**
	 * @var string[]
	 */
	private $infos;

	/**
	 * @var integer
	 */
	private $size;

	/**
	 * ApiResult constructor.
	 * @param int $code
	 * @param string $details
	 */
	public function __construct($code, $details) {
		$this->code = $code;
		$this->status = ($code == 0) ? self::OK : self::KO;
		$this->details = $details;
		$this->infos = [];
	}

	/**
	 * @return int
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @param int $code
	 * @return ApiResult
	 */
	public function setCode($code) {
		$this->code = $code;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 * @return ApiResult
	 */
	public function setStatus($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDetails() {
		return $this->details;
	}

	/**
	 * @param string $details
	 * @return ApiResult
	 */
	public function setDetails($details) {
		$this->details = $details;

		return $this;
	}

	/**
	 * Add Info
	 *
	 * @param $key
	 * @param $value
	 */
	public function addInfo($key, $value) {
		$this->infos[$key] = $value;
	}

	/**
	 * @return object
	 */
	public function getInfos() {
		return (object)$this->infos;
	}

	/**
	 * @param array $infos
	 * @return ApiResult
	 */
	public function setInfos(array $infos) {
		$this->infos = $infos;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @param int $size
	 * @return ApiResult
	 */
	public function setSize($size) {
		$this->size = $size;

		return $this;
	}

}
