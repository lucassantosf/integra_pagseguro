<?php

namespace Hcode\PagSeguro;

class Address{ 

	private $street;
	private $number;
	private $complement;
	private $district;
	private $postalCode;
	private $city;
	private $state;

	public function __construct(string $street, string $number, string $complement, string $district, string $number, string $postalCode, string $city, string $state){
		if(!$street){
			throw new Exception("Informe o logradouro do endereço"); 
		}

		if(!$number){
			throw new Exception("Informe o número do endereço"); 
		}
   
		if(!$district){
			throw new Exception("Informe o bairro do endereço"); 
		}

		if(!$postalCode){
			throw new Exception("Informe o CEP do endereço"); 
		}

		if(!$city){
			throw new Exception("Informe a cidade do endereço"); 
		}

		if(!$state){
			throw new Exception("Informe o pais do endereço"); 
		}

		$this->street = $street;
		$this->number = $number;
		$this->complement = $complement;
		$this->district = $district;
		$this->postalCode = $postalCode;
		$this->city = $city; 
		$this->state = $state; 
	}

	public function getDOMElement($node = "address"):DOMElement{
		
		$dom = new DOMDocument();

		$address = $dom->createElement($node);
		$address = $dom->appendChild($address);

		$street = $dom->createElement("street", $this->street);
		$street = $address->appendChild($street);

		$number = $dom->createElement("number", $this->number);
		$number = $address->appendChild($number);

		$complement = $dom->createElement("complement", $this->complement);
		$complement = $address->appendChild($complement);

		$district = $dom->createElement("district", $this->district);
		$district = $address->appendChild($district);

		$postalCode = $dom->createElement("postalCode", $this->postalCode);
		$postalCode = $address->appendChild($postalCode);

		$city = $dom->createElement("city", $this->city);
		$city = $address->appendChild($city);

		$state = $dom->createElement("state", $this->state);
		$state = $address->appendChild($state);
	
		return $phone;
	}
}