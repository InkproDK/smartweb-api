<?php
namespace inkpro\smartwebapi;

class ProductCustomData{
    public $Id;
	public $LanguageISO;
	public $ProductCustomId;
	public $ProductCustomIds;
    public $ProductCustomTypeId;
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