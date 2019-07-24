<?php

namespace Hcode\PagSeguro;

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

	//Quanto a é aceita de parcelamento
	const MAX_INSTALLMENT_NO_INTEREST = 10;
	//Quanto a loja aceita de parcelamento
	const MAX_INSTALLMENT = 10;

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

}