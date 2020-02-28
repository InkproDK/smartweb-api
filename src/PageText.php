<?php
namespace inkpro\smartwebapi;

class PageText{
	public $Id;
	public $CategoryId;
	public $Headline;
	public $LanguageAccess;
	public $LanguageISO;
	public $Link;
	public $ParentId;
	public $Pictures;
	public $SeoDescription;
	public $SeoKeywords;
	public $SeoTitle;
	public $ShowInMenu;
	public $Sorting;
	public $Target;
	public $Text;
	public $Text2;
	public $Text3;
	public $Title;
	public $UpdatedDate;
	public $Visible;
    
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