<?php

namespace App\Http\Controllers\PagSeguro; 

use \GuzzleHttp\Client; 

class Transporter{

	public static function createSession(){ 
		$client = new Client(); 
		$res = $client->request('POST', Config::getUrlSessions()."?".http_build_query(Config::getAuthentication()),['verify'=>false]); 
		$xml = simplexml_load_string($res->getBody()->getContents()); 
		return ((string)$xml->id);
	} 

	public static function sendTransaction(Payment $payment){
		$client = new Client();  
		$res = $client->request('POST', Config::getUrlTransation()."?".http_build_query(Config::getAuthentication()),[
			'verify'=>false,
			'headers'=>[
				'Content-Type'=>'application/xml' 
			],
			'body'=>$payment->getDOMDocument()->saveXml()
		]); 
		
		$xml = simplexml_load_string($res->getBody()->getContents());

		var_dump($xml);

		$order = new Order(); 
		/*
		$order->get((int)$xml->reference);

		$order->setPagSeguroTransactionResponse(
			(string)$xml->code,
			(float)$xml->grossAmount,
			(float)$xml->disccountAmount,
			(float)$xml->feeAmount,
			(float)$xml->netAmount,
			(float)$xml->extraAmount, 
			(string)$xml->paymentLink
		);*/

		return $xml; 
	}
} 