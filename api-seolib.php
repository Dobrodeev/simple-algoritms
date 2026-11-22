<?php
//$url = 'https://api.seolib.ru/account/balance';
//$url = 'https://api.seolib.ru/globals/engines';
//$url = 'https://api.seolib.ru/globals/regions';
$url = 'https://api.seolib.ru/projects/list';
//$params = [ 'key' => 'YOUR_API_KEY' ];
$params = [ 'token' => '1e178770-7a58-11ea-98ce-56847afe9799' ];

//$url = 'url_to_post';
//$data = array(
//	"first_name" => "First name",
//	"last_name" => "last name",
//	"email"=>"email@gmail.com",
//	"addresses" => array (
//		"address1" => "some address",
//		"city" => "city",
//		"country" => "CA",
//		"first_name" =>  "Mother",
//		"last_name" =>  "Lastnameson",
//		"phone" => "555-1212",
//		"province" => "ON",
//		"zip" => "123 ABC"
//	)
//);
$data_string = json_encode( $params );
$ch          = curl_init( $url );
curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string );
// лучше выводит
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
//curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt( $ch, CURLOPT_HTTPHEADER,
	array(
		'Content-Type:application/json',
		'Content-Length: ' . strlen( $data_string )
	)
);

$result = curl_exec( $ch );
//print_r($result);
curl_close( $ch );
$decodeResult = json_decode( $result );
echo '<pre>';
print_r( $decodeResult );
echo '</pre>';


