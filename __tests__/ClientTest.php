<?php

namespace Trulyao\NeatHttp;

use CurlHandle;
use Exception;
use PHPUnit\Framework\TestCase;


class ClientTest extends TestCase {

	private string $url;

	public function __construct() {
		parent::__construct();
		$this->url = "https://jsonplaceholder.typicode.com/posts";
	}


	public function testConstruct() {
		$client = new Client();
		$this->assertInstanceOf(Client::class, $client);
	}

	public function testBaseURL() {
		$testURL = $this->url;
		$client = new Client([
			'baseUrl' => $testURL,
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
		$testURI = $this->url;
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
		$testURI = $this->url;
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

	/**
	 * @throws Exception
	 */
	public function testGetAsArray() {
		$testURI = $this->url;
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => false,
			]
		);
		$request = $client->get('1');
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $request['data']['title']);
	}

	/**
	 * @throws Exception
	 */
	public function testGetAsObject() {
		$testURI = $this->url;
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => true,
			]
		);
		$request = $client->get('1');
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $request->data->title);
	}

	/**
	 * @throws Exception
	 */
	public function testPostAsObject() {
		$testURI = $this->url;
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => true,
			]
		);
		$request = $client->post('', ['data' => [
			'title' => 'foo',
			'body' => 'bar',
			'userId' => 1,
		]]);
		// var_dump($request);
		$this->assertEquals('bar', $request->data->body);
	}
}
