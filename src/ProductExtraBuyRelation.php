<?php
namespace inkpro\smartwebapi;

class ProductExtraBuyRelation{
    public $ExtraBuyCategoryId;
    public $Id;
    public $ProductId;
    public $RelationProductId;
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