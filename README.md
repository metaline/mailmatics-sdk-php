# PHP Mailmatics SDK

A PHP client for the Mailmatics API.

> This library is in development. Use it at your risk.

## Requirements

* PHP >= 5.4
* [Composer](https://getcomposer.org/)
* (optional) [cURL](http://php.net/manual/en/book.curl.php) extension
* (optional) [Guzzle](http://guzzlephp.org) >= 6

## Installation

The recommended way to install PHP Mailmatics SDK is through [Composer](https://getcomposer.org/).

	$ curl -sS https://getcomposer.org/installer | php

Next, run the Composer command to install the latest stable version:

	$ php composer.phar require mailmatics/php-sdk

After installing, you need to require the Composer’s autoloader:

```php
require 'vendor/autoload.php';
```

## Authentication

PHP Mailmatics SDK supports two authentication modes:

**1. API key:**

```php
use Mailmatics\Client;

$client = new Client(['key' => '...']);
```

**2. Simple login:**

```php
use Mailmatics\Client;

$client = new Client(['username' => 'admin', 'password' => '12345']);
```

## Options

TODO

## HTTP Client

Internally, PHP Mailmatics SDK uses an implementation of `Mailmatics\HttpClientInterface`. The default is `Mailmatics\HttpClient\StreamClient`. You can change it by passing it to the client constructor:

```php
use Mailmatics\Client;
use Mailmatics\HttpClient\CurlClient;

$client = new Client($credentials, $options, new CurlClient());
```

The more powerful implementation is the `Mailmatics\HttpClient\GuzzleHttpClient`, that require the [Guzzle](http://guzzlephp.org) library.

```php
use Mailmatics\HttpClient\GuzzleHttpClient;

$httpClient = new GuzzleHttpClient();
```

You can pass your instance of Guzzle client in the constructor:

```php
use GuzzleHttp\Client as GuzzleClient;
use Mailmatics\HttpClient\GuzzleHttpClient;

$guzzleClient = new GuzzleClient();
$httpClient = new GuzzleHttpClient($guzzleClient);
```

## Transactional Emails

List all transactional emails:

```php
$emails = $client->getTransactional()->all();
```

Get a single transactional email:

```php
$email = $client->getTransactional()->get(123);
```

## License

This library is licensed under the MIT License - see the LICENSE file for details.
