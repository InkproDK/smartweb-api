<?php
namespace inkpro\smartwebapi;

class ProductVariantTypeValue{
    public $Color;
	public $Id;
	public $LanguageISO;
    public $Picture;
    public $ProductVariantTypeId;
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