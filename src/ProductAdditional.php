<?php
namespace inkpro\smartwebapi;

class ProductAdditional{
    public $Id;
	public $LanguageISO;
	public $Price;
	public $ProductAdditionalTypeId;
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