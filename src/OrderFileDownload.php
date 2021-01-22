<?php
namespace inkpro\smartwebapi;

class OrderFileDownload{
    public $Count;
    public $DateFrom;
    public $DateTo;
    public $Id;
    public $OrderId;
    public $ProductId;

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