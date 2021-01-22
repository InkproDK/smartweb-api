<?php
namespace inkpro\smartwebapi;

class SEORedirect{
	public $Id;
	public $LanguageISO;
    public $Source;
    public $Target;
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