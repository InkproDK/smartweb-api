<?php
namespace inkpro\smartwebapi;

class NewsletterCustomField{
    public $Default;
    public $Id;
    public $Name;
    public $ServiceId;
    public $Type;
    public $UserGroupId;
    

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