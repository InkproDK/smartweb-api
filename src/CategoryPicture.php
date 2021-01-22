<?php
namespace inkpro\smartwebapi;

class CategoryPicture{
    public $CategoryId;
    public $Id;
    public $Sorting;
    public $Name;

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