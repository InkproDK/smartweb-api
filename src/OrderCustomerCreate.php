<?php
namespace inkpro\smartwebapi;

class OrderCustomerCreate{

	public $Address;
	public $Address2;
	public $B2B;
	public $City;
	public $Company;
	public $Country;
	public $CountryCode;
	public $Cvr;
	public $Ean;
	public $Email;
	public $Firstname;
	public $Lastname;
	public $Mobile;
	public $OrderId;
	public $Phone;
	public $ShippingAddress;
	public $ShippingAddress2;
	public $ShippingCity;
	public $ShippingCompany;
	public $ShippingCountry;
	public $ShippingCountryCode;
	public $ShippingEmail;
	public $ShippingFirstname;
	public $ShippingLastname;
	public $ShippingMobile;
	public $ShippingPhone;
	public $ShippingState;
	public $ShippingZip;
	public $State;
	public $Zip;
    
    function __construct($data){
        $data = (array)$data;
        foreach($data as $key=>$row){
            switch($key){
                default:
                    $this->$key = $row;
                    break;
            }
        }
    }
}