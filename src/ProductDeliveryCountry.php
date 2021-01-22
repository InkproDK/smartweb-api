<?php
namespace inkpro\smartwebapi;

class ProductDeliveryCountry{
    public $Access;
    public $Code;
    public $Id;
	public $CompanyTax;
    public $Iso;
    public $Primary;
    public $Tax;
    
    
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