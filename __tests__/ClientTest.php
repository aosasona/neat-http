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
		$response = $client->request('/1', ['method' => 'GET']);
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $response['data']['title']);
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
		$response = $client->request('/1', ['method' => 'GET']);
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $response->data->title);
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
		$response = $client->get('1');
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $response['data']['title']);
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
		$response = $client->get('1');
		$this->assertEquals('sunt aut facere repellat provident occaecati excepturi optio reprehenderit', $response->data->title);
	}

	/**
	 * @throws Exception
	 */
	public function testPostAsArray() {
		$testURI = $this->url;
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => false,
			]
		);
		$response = $client->post('', ['data' => [
			'title' => 'foo',
			'body' => 'bar',
			'userId' => 1,
		]]);
		$this->assertEquals('bar', $response['data']['body']);
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
		$response = $client->post('', ['data' => [
			'title' => 'foo',
			'body' => 'bar',
			'userId' => 1,
		]]);
		$this->assertEquals('bar', $response->data->body);
	}

	/**
	 * @throws Exception
	 */
	public function testDeleteShouldNotReturnBody() {
		$testURI = $this->url;
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => false,
			]
		);
		$response = $client->delete('1');
		$this->assertEquals([], $response['data']);
	}

	/**
	 * @throws Exception
	 */
	public function testPutShouldReplaceAllData() {
		$testURI = $this->url;
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => true,
			]
		);
		$response = $client->put('1', ['data' => [
			'id' => 1,
			'title' => 'hello',
			'body' => 'world',
			'userId' => 20,
		]]);
		$this->assertEquals('hello', $response->data->title);
		$this->assertEquals('world', $response->data->body);
		$this->assertEquals('20', $response->data->userId);
	}

	/**
	 * @throws Exception
	 */
	public function testPatchShouldReplacePartially() {
		$testURI = $this->url;
		$client = new Client(
			[
				'baseUrl' => $testURI,
				'object' => true,
			]
		);
		$response = $client->patch('1', ['data' => [
			'body' => 'updated',
		]]);
		$this->assertEquals('1', $response->data->userId);
		$this->assertEquals('updated', $response->data->body);
	}
}
