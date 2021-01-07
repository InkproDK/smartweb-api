<?php
namespace inkpro\smartwebapi;

class VatGroup{
	public $Id;
	public $Name;
	public $Sorting;
	public $VatPercentage;
    
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