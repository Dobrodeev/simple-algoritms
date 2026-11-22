<?php
/**
 * $ composer require serpstat/sdk
 * */
// This example works on serpstat-sdk only
// https://github.com/SerpstatGlobal/serpstat-sdk

require_once __DIR__ . '/../vendor/autoload.php';
// configure your application
$config = [
	'token' => '19666fc1ae1724da1d5ea2f3a99d5f5a',
];
$domain = 'example.com';
// init client with your serpstat api token
$apiClient = new \Serpstat\Sdk\Core\ApiGuzzleHttpClient( $config['token'] );
// create instance of any api method class
// list of methods classes in folder src\Methods
$apiMethod = new \Serpstat\Sdk\Methods\CompetitorsMethod(
	$domain,
	\Serpstat\Sdk\Interfaces\IApiClient::SE_GOOGLE_RU
);
try {
	// try call api method
	$response = $apiClient->call( $apiMethod );
} catch ( \Exception $e ) {
	// catch api error
	$response = $e->getMessage();
}
