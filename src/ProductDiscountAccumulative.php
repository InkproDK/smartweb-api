<?php
namespace inkpro\smartwebapi;

class ProductDiscountAccumulative{
    public $Amount;
    public $Currency;
    public $Date;
    public $DateFrom;
    public $DateTo;
    public $Discount;
    public $Id;
	public $Language;
	public $Site;
	public $Type1;
    public $Type1Id;
    public $Type2;
	public $Type2Id;
    
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