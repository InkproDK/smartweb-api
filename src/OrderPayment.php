<?php
namespace inkpro\smartwebapi;

class OrderPayment{
    public $Id;
    public $OrderId;
    public $PaymentMethodId;
    public $Price;
    public $Title;
    public $Vat;

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