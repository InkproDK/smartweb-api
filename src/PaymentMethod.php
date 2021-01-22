<?php
namespace inkpro\smartwebapi;

class PaymentMethod{
    public $Description;
    public $FixedFee;
    public $GatewayPassword;
    public $GatewayUserId;
    public $GatewayUserName;
    public $Id;
	public $LanguageAccess;
	public $LanguageISO;
	public $OnlineMethods;
	public $OrderStatus;
	public $PaymentAcceptPath;
	public $PercentageFee;
	public $Sorting;
	public $Title;
	public $Type;
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