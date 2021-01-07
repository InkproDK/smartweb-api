<?php
namespace inkpro\smartwebapi;

class ProductVariant{
	public $BuyingPrice;
	public $DeliveryTime;
    public $DeliveryTimeId;
    public $Description;
    public $DescriptionLong;
    public $DisableOnEmpty;
    public $Discount;
    public $DiscountType;
    public $Ean;
    public $Id;
    public $ItemNumber;
    public $ItemNumberSupplier;
    public $MinAmount;
    public $PictureId;
    public $PictureIds;
    public $Price;
    public $ProductId;
    public $Sorting;
    public $Status;
    public $Stock;
    public $StockLow;
    public $Title;
    public $Unit;
    public $VariantTypeValues;
    public $Weight;
    
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