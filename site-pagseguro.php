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
use \Hcode\PagSeguro\CreditCard;
use \Hcode\PagSeguro\Item;
use \Hcode\PagSeguro\Payment;
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
		"complemento",
		"Bairro exemplo",
		"18078-666",
		"Sorocaba",
		"SP",
		"Brasil"
	);  

	$birthDate = new DateTime($_POST['birth']);

	$sender = new Sender('Fulano da Silva',$cpf,$birthDate,$phone, 'teste@sandbox.pagseguro.com.br', $_POST['hash']);
 	
	$holder = new Holder('fulano da silva',$cpf,$birthDate,$phone);
 
	$shipping = new Shipping($address, (float)$cart->getvlfreight(), Shipping::PAC);
	
	$installment = new Installment((int)$_POST['installments_qtd'],(float)$_POST["installments_value"]); 

 	//Endereço de fatura
	$billingAddress = new Address(
		"Rua Teste",
		"140", 
		"complemento",
		"Bairro",
		"18078666",
		"Sorocaba",
		"SP",
		"Brasil" 
	);

	$creditCard = new CreditCard(
		$_POST["token"],
		$installment,
		$holder,
		$billingAddress
	); 

	$payment = new Payment(
		//$order->getidorder(),
		1,
		$sender,
		$shipping
	);

	foreach ($cart->getProducts() as $product) {
		$item = new Item(
			(int)$product['idproduct'],
			$product['desproduct'],
			(float)$product['vlprice'],
			(int)$product['nrqtd']
		); 	
		$payment->addItem($item);
	} 

	//Carregar um item
	$item = new Item(
		1,
		'celular motorola',
		120.00,
		1
	); 	
	$payment->addItem($item);

	$payment->setCreditCard($creditCard);

	/*$dom = $payment->getDOMDocument();  
	echo $dom->saveXml();
	exit();*/

	Transporter::sendTransaction($payment);

	echo json_encode([
		'success'=>true
	]);


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