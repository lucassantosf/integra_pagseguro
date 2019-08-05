<?php

namespace App\Http\Controllers\PagSeguro; 

class Config{

	const SANDBOX = true;

	const SANDBOX_EMAIL = "vendas@finger.com.vc";
	const PRODUCTION_EMAIL = "vendas@finger.com.vc";

	const SANDBOX_TOKEN = "79393F95AC494AF1B43B5035EFF8D8E0";
	const PRODUCTION_TOKEN = "";

	const SANDBOX_SESSIONS = "https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";
	const PRODUCTION_SESSIONS = "POST https://ws.sandbox.pagseguro.uol.com.br/v2/sessions";

	const SANDBOX_URL_JS = "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";
	const PRODUCTION_URL_JS = "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js";

	const SANDBOX_URL_TRANSACTION = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions";
	const PRODUCTION_URL_TRANSACTION = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions";

	const SANDBOX_URL_NOTIFICATION = "https://ws.sandbox.pagseguro.uol.com.br/v2/transactions/notifications/";
	const PRODUCTION_URL_NOTIFICATION = "https://ws.pagseguro.uol.com.br/transactions/notifications/";

	//Quanto é aceita de parcelamento sem juros na loja
	const MAX_INSTALLMENT_NO_INTEREST = 2;
	//Quanto a loja aceita de parcelamento
	const MAX_INSTALLMENT = 12;

	const NOTIFICATION_URL = "http://www.html5dev.com.br/payment/notification";

	public static function getAuthentication():array{
		if(Config::SANDBOX === true){
			return [
				"email"=>Config::SANDBOX_EMAIL,
				"token"=>Config::SANDBOX_TOKEN,
			];
		}else{
			return [
				"email"=>Config::PRODUCTION_EMAIL,
				"token"=>Config::PRODUCTION_TOKEN,
			];
		}
	} 

	public static function getUrlSessions():string{
		return (Config::SANDBOX == true) ? Config::SANDBOX_SESSIONS : Config::PRODUCTION_SESSIONS;
	}

	public static function getUrlJS(){
		return (Config::SANDBOX == true) ? Config::SANDBOX_URL_JS : Config::PRODUCTION_URL_JS;
	}

	public static function getUrlTransation(){
		return (Config::SANDBOX == true) ? Config::SANDBOX_URL_TRANSACTION : Config::PRODUCTION_URL_TRANSACTION;
	}

	public static function getNotificationTransactionURL(){
		return (Config::SANDBOX == true) ? Config::SANDBOX_URL_NOTIFICATION : Config::PRODUCTION_URL_NOTIFICATION;
	}

}