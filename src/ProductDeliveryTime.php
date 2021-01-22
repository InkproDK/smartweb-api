<?php
namespace inkpro\smartwebapi;

class ProductDeliveryTime{
    public $Id;
	public $LanguageIso;
    public $Sorting;
    public $TitleInStock;
    public $TitleNoStock;
    
    
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