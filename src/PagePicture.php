<?php
namespace inkpro\smartwebapi;

class PagePicture{
	public $Id;
	public $LanguageAccess;
	public $LanguageISO;
	public $Name;
	public $Sorting;
	public $Thumbnail;
    
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