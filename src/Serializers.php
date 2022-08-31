<?php

namespace Trulyao\NeatHttp;

use stdClass;

class Serializers {
	private Utils $utils;

	public function __construct(Utils $utils) {
		$this->utils = $utils;
	}

	/**
	 * @param string $response
	 * @return array|object
	 */
	public function parseResponseBody(string $response) {
		$response = json_decode($response);
		if($this->utils->getClient()->isObject()) {
			return (object)$response;
		}
		return (array)$response;
	}

	/**
	 * @param string $raw_headers
	 * @return array|stdClass
	 */
	public function parseResponseHeaders(string $raw_headers): array|stdClass {
		$headers = [];
		$raw_headers = explode("\r", $raw_headers);
		foreach($raw_headers as $header) {
			if(!strpos($header, ':')) {
				continue;
			}
			$header = explode(':', $header, 2);
			$key = strtolower(trim($header[0]));
			$value = strtolower(trim($header[1]));
			if($key) {
				$headers[$key] = $value;
			}
		}
		if($this->utils->getClient()->isObject()) {
			return (object)$headers;
		}
		return $headers;
	}
}