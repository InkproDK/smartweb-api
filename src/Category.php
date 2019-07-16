<?php
namespace inkpro\smartwebapi;

class Category{
    public $Description;
    public $DescriptionBottom;
    public $Id;
    public $LanguageAccess;
    public $LanguageISO;
    public $ParentId;
    public $SeoDescription;
    public $SeoKeywords;
    public $SeoTitle;
    public $ShowInMenu;
    public $Sorting;
    public $Status;
    public $Title;
    public $UserGroupAccessIds;

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