# Neat HTTP Client
`EXPERIMENTAL`

A curL-based HTTP client. This is an experimental side-project, if you need something more robust and stable, you can check out [GuzzleHTTP](https://docs.guzzlephp.org/en/stable/). Feel free to fork or make a PR too :)

## Requirements
- PHP 8.0+
- CurL extension (ENABLE IN `PHP.INI`)

## Installation

```bash
composer require trulyao/neat-http
```

## Usage

```php
<?php

use Trulyao\NeatHttp\Client;

$client = new Client(
    [
        'baseUrl' => 'http://example.com',
        'object' => true, // return object instead of array
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ] // optional base config
);

$variable1 = $client->get('1');

$variable2 = $client->post('', [
    'data' => [
         'title' => 'foo',
         'body' => 'bar',
         'userId' => 1,
    ],
]); // automatically serialized to JSON before sending
       
```



> Check `__tests__` for more usage example.
