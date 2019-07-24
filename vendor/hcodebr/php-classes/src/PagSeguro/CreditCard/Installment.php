<?php

namespace Hcode\PagSeguro\CreditCard;

class Installment{
	private $quantity;
	private $value;

	public function __construct(int $quantity, float $value){
		if($quantity < 1 || $quantity > Config::MAX_INSTALLMENT){
			throw new Exception("Número de parcelas inválidas"); 
		}

		if($value <= 0){
			throw new Exception("Valor totatl inválido"); 
		}

		$this->quantity = $quantity;
		$this->value = $value;
	}

	public function getDOMElement():DOMElement{
		$dom = new DOMDocument();

		$installment = $dom->createElement("installment");
		$installment = $dom->appendChild($installment);
 
		$value = $dom->createElement("value", number_format($this->value,2,".",""));
		$value = $installment->appendChild($value);
		
		$quantity = $dom->createElement("quantity");
		$quantity = $installment->appendChild($quantity);

		$noInterestInstallmentQuantity = $dom->createElement("noInterestInstallmentQuantity", Config::MAX_INSTALLMENT_NO_INTEREST);
		$noInterestInstallmentQuantity = $installment->appendChild($noInterestInstallmentQuantity);
  
		return $installment;
	}
}