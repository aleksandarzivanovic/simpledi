<?php

namespace System\Http\Request;

use System\Http\Request\Method\MethodInterface;
use System\Http\Header\HeaderInterface;

class Request extends RequestFactory implements RequestInterface {

	/** @var MethodInterface */
	protected $method;

	/** @var HeaderInterface */
	protected $header;

	/**
	 * @param MethodInterface $method
	 */
	public function __construct(MethodInterface $method, HeaderInterface $header) {
		parent::__construct();

		$this->method = $method;
		$this->header = $header;
	}

	/**
	 * @param string $header
	 * @return string
	 */
	public function getHeader($header) {
		return $this->header->getHeader($header);
	}

	/**
	 * @return array
	 */
	public function getHeaders() {
		return $this->header->getHeaders();
	}

	/**
	 * @return MethodInterface
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * 
	 * @param string $data
	 * @param MethodInterface $method
	 * @return string|null
	 */
	public function getRequestData($data, MethodInterface $method = null) {
		$values = $this->getRequestDataArray($method);

		return (isset($values[$data]) ? $values[$data] : null);
	}

	/**
	 * 
	 * @param MethodInterface $method
	 * @return array
	 * @throws \RuntimeException
	 */
	public function getRequestDataArray(MethodInterface $method = null) {
		if (is_null($method)) {
			$method = $this->getMethod();
		}

		switch ($method->getMethod()) {
			case MethodInterface::METHOD_GET:
				return $this->get;
			case MethodInterface::METHOD_POST:
			case MethodInterface::METHOD_PUT:
			case MethodInterface::METHOD_DELETE:
				return $this->post;
		}
	}

}
