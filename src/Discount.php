<?php
namespace inkpro\smartwebapi;

class Discount{
    public $AmountSpent;
    public $Code;
    public $DateCreated;
    public $DateExpire;
    public $Id;
    public $Limit;
    public $ProductIds;
    public $Title;
    public $Type;
    public $UseCount;
    public $Value;
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