<?php
namespace inkpro\smartwebapi;

class OrderCurrency{
    public $Currency;
    public $Decimal;
    public $Id;
    public $Iso;
    public $OrderId;
    public $Point;
    public $Round;
    public $Symbol;
    public $SymbolPlace;

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