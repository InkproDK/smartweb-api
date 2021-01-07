<?php
namespace inkpro\smartwebapi;

class OrderStatusCode{
    public $Id;
    public $LanguageISO;
    public $Sorting;
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