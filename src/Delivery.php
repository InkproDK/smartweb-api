<?php
namespace inkpro\smartwebapi;

class Delivery{
    public $DeliveryEstimate;
    public $FixedDelivery;
    public $FreeDeliveryActive;
    public $FreeDeliveryPrice;
    public $Id;
    public $LanguageISO;
    public $MultipleAddresses;
    public $OverLimitFeeActive;
    public $OverLimitFixedFee;
    public $OverLimitPercentageFee;
    public $Price;
    public $Primary;
    public $RegionMode;
    public $ServiceType;
    public $Sorting;
    public $Text;
    public $Title;
    public $Type;
    public $UserGroups;
    public $Vat;
    public $ZipFrom;
    public $ZipGroups;
    public $ZipTo;

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