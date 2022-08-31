<?php

namespace Trulyao\NeatHttp;

use \stdClass;
use \Exception;
use CurlHandle;

class Client
{
	protected false|CurlHandle $curl;
	public string $baseUri;
	protected bool $json = true;
	protected array $headers = [];


	public final function __construct(array $options = [])
	{
		$this->curl = curl_init();
		$this->baseUri = $options['baseUri'] ?? '';
		$this->json = $options['json'] ?? true;
	}

	final public function get(string $endpoint, array $data): array | stdClass
	{
		return [];
	}

	final public function post(string $endpoint, array $data): array | stdClass
	{
		return [];
	}

	final public function delete(string $endpoint, array $data): array | stdClass
	{
		return [];
	}

	final public function put(string $endpoint, array $data): array | stdClass
	{
		return [];
	}

	final public function __destruct()
	{
		curl_close($this->curl);
	}
}