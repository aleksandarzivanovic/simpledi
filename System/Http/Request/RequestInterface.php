<?php

namespace System\Http\Request;

use System\Http\Method\MethodInterface;

interface RequestInterface {

	/**
	 * @return array
	 */
	public function getHeaders();

	/**
	 * @param string $header
	 * @return string|null
	 */
	public function getHeader($header);

	/**
	 * @return MethodInterface
	 */
	public function getMethod();

	/**
	 * @param string $data
	 * @param string $method
	 * @return string|array|object|null
	 */
	public function getRequestData($data, $method);

	/**
	 * 
	 * @param string $method
	 * @return array
	 */
	public function getRequestDataArray($method);
}
