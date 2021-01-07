<?php
namespace inkpro\smartwebapi;

class Site{
	public $Id;
	public $LanguageISO;
	public $Sorting;
	public $Title;
	public $Type;
    
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