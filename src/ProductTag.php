<?php
namespace inkpro\smartwebapi;

class ProductTag{
    public $DateCreated;
	public $Id;
	public $LanguageIso;
    public $Rating;
    public $Text;
    public $Title;
    public $UserEmail;
    public $UserId;
    public $Username;
    
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