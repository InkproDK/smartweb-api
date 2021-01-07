<?php
namespace inkpro\smartwebapi;

class ProductFile{
    public $FileName;
	public $Id;
	public $ProductId;
    public $Sorting;
    
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