<?php
namespace inkpro\smartwebapi;

class Transaction{

	
	public $Actioncode;
	public $Amount;
	public $Cardtype;
	public $Currency;
	public $Date;
	public $Errorcode;
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