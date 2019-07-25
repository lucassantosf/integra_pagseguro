<?php

namespace Hcode\PagSeguro;

use Exception;
use DOMDocument;
use DOMElement;

class CreditCard{

	private $token;
	private $installment;
	private $holder;
	private $billingAddress;

	public function __construct(string $token, Tnstallment $installment, Holder $holder, Address $billingAddress){

		if(!$token){
			throw new Exception("Informe token do cartãod e crédtio!");			
		}

		$this->token = $token;
		$this->installment = $installment;
		$this->holder = $holder;
		$this->billingAddress = $billingAddress;

	}

	public function getDOMElement():DOMElement{
		$dom = new DOMDocument();

		$creditCard = $dom->createElement("creditCard");
		$creditCard = $dom->appendChild($creditCard);
 
		$token = $dom->createElement("token", $this->token);
		$token = $creditCard->appendChild($token);
		
		$installment = $this->installment->getDOMElement();
		$installment = $dom->importNode($installment, true); 
		$installment = $creditCard->appendChild($installment);

		$holder = $this->holder->getDOMElement();
		$holder = $dom->importNode($holder, true); 
		$holder = $creditCard->appendChild($holder);

		$billingAddress = $this->billingAddress->getDOMElement("billingAddress");
		$billingAddress = $dom->importNode($billingAddress, true); 
		$billingAddress = $creditCard->appendChild($billingAddress); 

		return $creditCard; 
	}
}