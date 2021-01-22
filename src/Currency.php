<?php
namespace inkpro\smartwebapi;

class Currency{
    public $Currency;
    public $Decimal;
    public $DecimalCount;
    public $Id;
    public $Iso;
    public $Point;
    public $Round;
    public $RoundOn;
    public $Symbol;
    public $SymbolPlace;
    public $Title;

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