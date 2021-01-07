<?php
namespace inkpro\smartwebapi;

class OrderPacking{
    public $From;
    public $Id;
    public $OrderId;
    public $Text;

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