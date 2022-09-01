<?php

namespace Trulyao\NeatHttp;

use CurlHandle;
use Trulyao\NeatHttp\HTTPException as HTTPException;
use stdClass;

class Utils {
	private Client $client;
	private Serializers $serializers;

	public function __construct(Client $client) {
		$this->client = $client;
		$this->serializers = new Serializers($this);
	}



	/**
	 * @param string $response
	 * @return array|object
	 */
	public function parseResponseBody(string $response) {
		return $this->serializers->parseResponseBody($response);
	}

	/**
	 * @param string $raw_headers
	 * @return array|stdClass
	 */
	public function parseResponseHeaders(string $raw_headers): array|stdClass {
		return $this->serializers->parseResponseHeaders($raw_headers);
	}

	/**
	 * @param array $response
	 * @return array|stdClass
	 */
	public function makeResponse(array $response = []): array|stdClass {
		if($this->client->isObject()) {
			return (object)$response;
		}
		return $response;
	}

	/**
	 * @param array $headers
	 * @return array
	 */
	public final function makeHeaders(array $headers = []): array {
		$finalHeaders = [];
		if(count($this->client->getHeaders()) > 0) {
			$headers = array_merge($this->client->getHeaders(), $headers);
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
	 * @param array|null $options
	 * @param string $endpoint
	 * @return array
	 */
	public function prepareRequestParams(?array $options, string $endpoint): array {
		$method = strtoupper($options['method'] ?? 'GET');
		$endpoint = trim($endpoint, '/');
		$headers = $this->makeHeaders($options['headers'] ?? []);
		$data = $options['data'] ?? [];
		return array($method, $endpoint, $headers, $data);
	}

    /**
     * @param CurlHandle $curl
     * @param string $endpoint
     * @param string $method
     * @param array $headers
     * @param array $data
     * @return void
     */
    public function setCurlOptions(CurlHandle $curl, string $endpoint, string $method, array $headers, array $data): void {
        $uri = $this->client->baseUrl ? $this->client->baseUrl.'/'.$endpoint : $endpoint;
        $default_options = [
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 20,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_FAILONERROR => true,
            CURLOPT_HEADER => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ];
        curl_setopt_array($curl, $default_options);
    }

    /**
     * @param CurlHandle|bool $curl
     * @return bool|string|array
     * @throws HttpException
     */
	public function executeCurlAndRetryOnSSLError(CurlHandle|bool $curl): bool|string|array
    {
		if(!$response = curl_exec($curl)) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            if(curl_errno($curl)){
                $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                $message = curl_error($curl);
                throw new HttpException($message, $status_code);
            }
		}
		return $response;
	}


	/**
	 * @param CurlHandle|bool $curl
	 * @param bool|string $response
	 * @return array
	 */
	public function extractHeadersAndBody(CurlHandle|bool $curl, bool|string $response): array {
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		return [
			'headers' => $headers,
			'body' => $body,
		];
	}

	/**
	 * @return Client
	 */
	public function getClient(): Client {
		return $this->client;
	}
}