<?php

namespace System\Http\Request;

use System\Http\Request\Method\MethodInterface;

class Request extends RequestFactory implements RequestInterface {

	/** @var MethodInterface */
	protected $method;

	public function __construct(MethodInterface $method) {
		parent::__construct();
		$this->method = $method;
	}

	public function getHeader($header) {
		
	}

	public function getHeaders() {
		
	}

	public function getMethod() {
		return $this->method;
	}

	public function getRequestData($data, $method) {
		$values = $this->getRequestDataArray($method);

		return (isset($values[$data]) ? $values[$data] : null);
	}

	public function getRequestDataArray($method) {
		switch ($method) {
			case MethodInterface::METHOD_GET:
				return $this->get;
			case MethodInterface::METHOD_POST:
			case MethodInterface::METHOD_PUT:
			case MethodInterface::METHOD_DELETE:
				return $this->post;
			default:
				throw new \RuntimeException("Undefined method {$method}");
		}
	}

}
