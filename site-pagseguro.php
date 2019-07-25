<?php 

use \Hcode\Page; 
use \Hcode\Model\User; 
use \Hcode\PagSeguro\Config;
use \Hcode\PagSeguro\Transporter;
use \Hcode\PagSeguro\Document;
use \Hcode\PagSeguro\Phone;
use \Hcode\PagSeguro\Address;
use \Hcode\PagSeguro\Sender;
use \Hcode\PagSeguro\Shipping;
use \Hcode\PagSeguro\CreditCard\Installment;
use \Hcode\PagSeguro\CreditCard\Holder;
use \Hcode\Model\Order;

$app->post('/payment/credit',function(){
	
	User::verifyLogin(false);
	
	$order = new Order();
	
	$order->getFromSession();

	$order->get((int)$order->getidorder());

	$address = $order->getAddress();

	$cart = $order->getCart();

	$cpf = new Document(Document::CPF, $_POST['cpf']);
 
	$phone = new Phone($_POST['ddd'],$_POST['phone']);

	/*
	$address = new Address(
		$address->getdesaddress(),
		$address->getdesnumber(), 
		$address->getdescomplement(),
		$address->getdesdistrict(),
		$address->getdeszipcode(),
		$address->getdescity(),
		$address->getdesstate(),
		$address->getdescountry()
	);*/

	$address = new Address(
		"Rua Teste",
		"140", 
		"",
		"Bairro",
		"18078666",
		"Sorocaba",
		"SP",
		"Brasil"
	); 

	$birthDate = new DateTime($_POST['birth']);

	$sender = new Sender('teste',$cpf,$birthDate,$phone, 'teste@gmail.com', $_POST['hash']);
 	
	$holder = new Holder('teste',$cpf,$birthDate,$phone);
 
	$shipping = new Shipping($address, (float)$cart->getvlfreight(), Shipping::PAC);

	$installment = new Installment($address, (float)$cart->getvlfreight(), Shipping::PAC);

	$dom = new DOMDocument();

	$test = $installment->getDOMElement();

	$testNode = $dom->importNode($test, true);

	$dom->appendChild($testNode);
 
	echo $dom->saveXml();



}); 

$app->get('/payment', function() {

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$years = [];

	for($y = date('Y'); $y<date('Y')+14 ; $y++ ){
		array_push($years, $y);
	}

	$page = new Page();

	$page->setTpl("payment",[
		"order"=>$order->getValues(),
		"msgError"=>Order::getError(),
		"years"=>$years,
		"pagseguro"=>[
			"urlJS"=>Config::getUrlJS(),
			"id"=>Transporter::createSession(),
			"maxInstallmentNoInterest"=>Config::MAX_INSTALLMENT_NO_INTEREST,
			"maxInstallment"=>Config::MAX_INSTALLMENT,
			]
	]);
}); 