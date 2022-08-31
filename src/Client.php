<?php

namespace Trulyao\NeatHttp;

use \stdClass;
use \Exception;
use CurlHandle;

class Client {
	private false|CurlHandle $curl;
	public string $baseUrl;
	protected bool $object = true;
	protected array $headers = [];
	private Utils $utils;


	/**
	 * @param array $options
	 */
	public final function __construct(array $options = []) {
		$this->curl = curl_init();
		$this->baseUrl = $options['baseUrl'] ?? '';
		$this->object = $options['object'] ?? true;
		$this->headers = $options['headers'] ?? [
			'Content-Type' => 'application/json',
			'Accept' => '*/*',
		];
		$this->utils = new Utils($this);
	}

	/**
	 * @throws Exception
	 */
	final public function get(string $endpoint, ?array $options = []): array|stdClass {
		return $this->request($endpoint, array_merge($options, [
			'method' => 'GET',
		]));
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function post(string $endpoint, ?array $options = []): array|stdClass {
		return $this->request($endpoint, array_merge($options, [
			'method' => 'POST',
			'headers' => [
				'Content-Type' => 'application/json',
			],
		]));
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function put(string $endpoint, ?array $options = []): array|stdClass {
		return $this->request($endpoint, array_merge($options, [
			'method' => 'PUT',
			'headers' => [
				'Content-Type' => 'application/json',
			],
		]));
	}

	/**
	 * @throws Exception
	 */
	final public function patch(string $endpoint, ?array $options = []): array|stdClass {
		return $this->request($endpoint, array_merge($options, [
			'method' => 'PATCH',
			'headers' => [
				'Content-Type' => 'application/json',
			],
		]));
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function delete(string $endpoint, ?array $options = []): array|stdClass {
		return $this->request($endpoint, array_merge($options, [
			'method' => 'DELETE',
		]));
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 * @throws Exception
	 */
	final public function request(string $endpoint, ?array $options = []): array|stdClass {
		try {
			$curl = $this->curl;
			list($method, $endpoint, $headers, $data) = $this->prepareRequestParams($options, $endpoint);
			$this->setCurlOptions($curl, $endpoint, $method, $headers, $data);
			$full_response = $this->extractHeadersAndBody($curl, $this->executeCurlAndRetryOnSSLError($curl));
			$body = $this->parseResponse($full_response['body'] ?? '');
			$headers = $this->parseResponseHeaders($full_response['headers'] ?? []);
			return $this->makeResponse([
				'headers' => $headers,
				'data' => $body,
				'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE) ?? 200,
			]);
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * @return bool
	 */
	public function isObject(): bool {
		return $this->object;
	}

	/**
	 * @return array
	 */
	public function getHeaders(): array {
		return $this->headers;
	}

	/**
	 * @param array $headers
	 * @return array
	 */
	final public function makeHeaders(array $headers = []): array {
		return $this->utils->makeHeaders($headers);
	}

	/**
	 * @param array $response
	 * @return array|stdClass
	 */
	private function makeResponse(array $response = []): array|stdClass {
		return $this->utils->makeResponse($response);
	}

	/**
	 * @param string $raw_headers
	 * @return array|stdClass
	 */
	private function parseResponseHeaders(string $raw_headers): array|stdClass {
		return $this->utils->parseResponseHeaders($raw_headers);
	}


	/**
	 * @param CurlHandle $curl
	 * @param string $endpoint
	 * @param string $method
	 * @param array $headers
	 * @return void
	 */
	private function setCurlOptions(CurlHandle $curl, string $endpoint, string $method, array $headers, array $data): void {
		$this->utils->setCurlOptions($curl, $endpoint, $method, $headers, $data);
	}

	/**
	 * @param string $response
	 * @return array|object
	 */
	private function parseResponse(string $response) {
		return $this->utils->parseResponseBody($response);
	}

	/**
	 * @param CurlHandle|bool $curl
	 * @param bool|string $response
	 * @return array
	 */
	private function extractHeadersAndBody(CurlHandle|bool $curl, bool|string $response): array {
		return $this->utils->extractHeadersAndBody($curl, $response);
	}

	/**
	 * @param array|null $options
	 * @param string $endpoint
	 * @return array
	 */
	private function prepareRequestParams(?array $options, string $endpoint): array {
		return $this->utils->prepareRequestParams($options, $endpoint);
	}


	/**
	 * @param CurlHandle|bool $curl
	 * @return bool|string
	 */
	private function executeCurlAndRetryOnSSLError(CurlHandle|bool $curl): string|bool {
		return $this->utils->executeCurlAndRetryOnSSLError($curl);
	}


	/**
	 *
	 */
	final public function __destruct() {
		if($this->curl) {
			curl_close($this->curl);
		}
	}
}