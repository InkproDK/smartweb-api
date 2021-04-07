<?php
namespace inkpro\smartwebapi;

class OrderCustomer{
    public $Address;
    public $Amount;
    public $City;
    public $Comment;
    public $Company;
    public $CountryIso;
    public $DeliveryDate;
    public $DeliveryTime;
    public $Firstname;
    public $Id;
    public $Lastname;
    public $LineId;
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