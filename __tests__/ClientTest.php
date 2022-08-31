<?php

namespace Trulyao\NeatHttp;

use CurlHandle;
use Exception;
use PHPUnit\Framework\TestCase;


class ClientTest extends TestCase {


	public function testConstruct() {
		$client = new Client();
		$this->assertInstanceOf(Client::class, $client);
	}

	public function testBaseURL() {
		$testURI = "https://jsonplaceholder.typicode.com/posts";
		$client = new Client([
			'baseUrl' => $testURI,
			'object' => false,
		]);
		$this->assertEquals('https://jsonplaceholder.typicode.com/posts', $client->baseUrl);
		$this->assertEquals('string', gettype($client->baseUrl));
	}

	public function testMakeHeaders() {
		$client = new Client();
		$headers = $client->makeHeaders([
			'Content-Type' => 'text/html',
			'Accept' => 'application/json',
		]);
		$this->assertEquals(
			array('Content-Type: text/html', 'Accept: application/json')
		, $headers);
	}

	/**
	 * @throws Exception
	 */
	public function testRequestAsArray() {
		$testURI = "https://jsonplaceholder.typicode.com/posts";
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => false,
				'headers' => [
					'Accept' => 'application/json',
				],
			]
		);
		$request = $client->request('/1', ['method' => 'GET']);
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $request['data']['title']);
	}

	/**
	 * @throws Exception
	 */
	public function testRequestAsObject() {
		$testURI = "https://jsonplaceholder.typicode.com/posts";
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => true,
				'headers' => [
					'Accept' => 'application/json',
				],
			]
		);
		$request = $client->request('/1', ['method' => 'GET']);
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $request->data->title);
	}

	/*public function testGetAsArray() {
		$testURI = "https://jsonplaceholder.typicode.com/posts";
		$client = new Client(
			[
				'baseUri' => $testURI,
				'json' => false,
			]
		);
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $client->get('1')['data']['title']);
	}*/
}
