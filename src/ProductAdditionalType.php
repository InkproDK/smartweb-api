<?php
namespace inkpro\smartwebapi;

class ProductAdditionalType{
    public $CategoryId;
    public $Id;
	public $LanguageISO;
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