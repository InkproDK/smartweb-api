<?php
namespace inkpro\smartwebapi;

class OrderTransaction{
    public $ActionCode;
    public $Amount;
    public $AmountFull;
    public $Cardtype;
    public $Currency;
    public $Date;
    public $ErrorCode;
    public $Id;
    public $OrderId;
    public $PaymentId;
    public $Status;
    public $SubscriptionId;
    public $TransactionNumber;
    public $TransactionNumberLong;

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