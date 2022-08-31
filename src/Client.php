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


	/**
	 * @param array $options
	 */
	public final function __construct(array $options = []) {
		$this->curl = curl_init();
		$this->baseUrl = $options['baseUrl'] ?? '';
		$this->object = $options['object'] ?? true;
		$this->headers = $options['headers'] ?? [
			'Content-Type' => 'application/json',
		];
	}

	/**
	 * @throws Exception
	 */
	final public function get(string $endpoint, ?array $options = []): array|stdClass {
		return $this->request($endpoint, $options);
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 */
	final public function post(string $endpoint, ?array $options = []): array|stdClass {
		return [];
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 */
	final public function delete(string $endpoint, ?array $options = []): array|stdClass {
		return [];
	}

	/**
	 * @param string $endpoint
	 * @param array|null $options
	 * @return array|stdClass
	 */
	final public function put(string $endpoint, ?array $options = []): array|stdClass {
		return [];
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
			list($method, $endpoint, $headers) = $this->prepareRequestParams($options, $endpoint);
			$this->setCurlOptions($curl, $endpoint, $method, $headers);
			$full_response = $this->extractHeadersAndBody($curl, $this->executeCurlAndRetryOnSSLError($curl));
			$body = $this->parseResponse($full_response['body'] ?? '');
			$headers = $this->parseResponseHeaders($full_response['headers'] ?? []);
			return $this->makeResponse([
				'headers' => $headers,
				'data' => $body,
				'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
			]);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * @param array $headers
	 * @return array
	 */
	final public function makeHeaders(array $headers = []): array {
		$finalHeaders = [];
		if(count($this->headers) > 0) {
			$headers = array_merge($this->headers, $headers);
		}
		if(count($headers) > 0) {
			foreach($headers as $key => $value) {
				$key = ucwords(trim($key));
				$finalHeaders[] = "$key: $value";
			}
			return $finalHeaders;
		}
		return [];
	}

	/**
	 * @param array $response
	 * @return array|stdClass
	 */
	private function makeResponse(array $response = []): array|stdClass {
		if($this->object) {
			return (object) $response;
		}
		return $response;
	}

	/**
	 * @param string $raw_headers
	 * @return array|stdClass
	 */
	private function parseResponseHeaders(string $raw_headers): array|stdClass {
		$headers = [];
		$raw_headers = explode("\r", $raw_headers);
		foreach($raw_headers as $header) {
			if(!strpos($header, ':')) {
				continue;
			}
			$header = explode(':', $header,2);
			$key = strtolower(trim($header[0]));
			$value = strtolower(trim($header[1]));
			if($key) {
				$headers[$key] = $value;
			}
		}
		if($this->object) {
			return (object) $headers;
		}
		return $headers;
	}


	/**
	 * @param CurlHandle $curl
	 * @param string $endpoint
	 * @param string $method
	 * @param array $headers
	 * @return void
	 */
	public function setCurlOptions(CurlHandle $curl, string $endpoint, string $method, array $headers): void {
		$uri = $this->baseUrl.'/'.$endpoint;
		$default_options = [
			CURLOPT_URL => $uri,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 20,
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_FAILONERROR => true,
			CURLOPT_HEADER => true,
		];
		curl_setopt_array($curl, $default_options);
	}

	/**
	 * @param string $response
	 * @return array|object
	 */
	private function parseResponse(string $response) {
		$response = json_decode($response);
		if($this->object) {
			return (object) $response;
		}
		return (array) $response;
	}

	/**
	 * @param CurlHandle|bool $curl
	 * @param bool|string $response
	 * @return array
	 */
	private function extractHeadersAndBody(CurlHandle|bool $curl, bool|string $response): array {
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		return [
			'headers' => $headers,
			'body' => $body,
		];
	}

	/**
	 * @param array|null $options
	 * @param string $endpoint
	 * @return array
	 */
	private function prepareRequestParams(?array $options, string $endpoint): array {
		$method = strtoupper($options['method'] ?? 'GET');
		$endpoint = trim($endpoint, '/');
		$headers = $this->makeHeaders($options['headers'] ?? []);
		return array($method, $endpoint, $headers);
	}



	/**
	 * @param CurlHandle|bool $curl
	 * @return bool|string
	 */
	private function executeCurlAndRetryOnSSLError(CurlHandle|bool $curl): string|bool {
		if(!$response = curl_exec($curl)) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			if(!$response = curl_exec($curl)) {
				trigger_error(curl_error($curl));
			}
		}
		return $response;
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