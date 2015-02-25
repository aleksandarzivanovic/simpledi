<?php

namespace System\Http\Header;

class Header implements HeaderInterface {

	/** @var array */
	private $headers;

	public function __construct() {
		$this->headers = array_change_key_case(getallheaders());
	}

	/**
	 * 
	 * @param string $header
	 * @return string|array|null
	 */
	public function getHeader($header) {
		if (isset($this->headers[$header])) {
			return $this->headers[$header];
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * 
	 * @param string $header
	 * @param string|array $value
	 * @return HeaderInterface|$this
	 */
	public function setHeader($header, $value) {
		$lowCaseHeader = strtolower($header);
		$this->headers[$lowCaseHeader] = $value;

		return $this;
	}

}
