<?php

namespace Trulyao\NeatHttp;

use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase {

	public function testConstruct() {
		$client = new Client();
		$this->assertInstanceOf(Client::class, $client);
	}

	public function testInit() {
		$client = new Client([
			'baseUri' => 'http://localhost',
			'json' => false,
		]);
		$this->assertEquals('http://localhost', $client->baseUri);
	}
}
