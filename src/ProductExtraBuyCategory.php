<?php
namespace inkpro\smartwebapi;

class ProductExtraBuyCategory{
    public $Id;
    public $LanguageISO;
    public $ParentId;
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