<?php
namespace inkpro\smartwebapi;

class Product{
	public $Additionals;
	public $AutoStock;
	public $BuyingPrice;
	public $CallForPrice;
	public $Category;
	public $CategoryId;
	public $CustomData;
	public $CategorySortings;
	public $DateCreated;
	public $DateUpdated;
	public $Delivery;
	public $DeliveryId;
	public $DeliveryTime;
	public $DeliveryTimeId;
	public $Description;
	public $DescriptionLong;
	public $DescriptionShort;
	public $DisableOnEmpty;
	public $Discount;
	public $DiscountGroup;
	public $DiscountGroupId;
	public $Discounts;
	public $DiscountType;
	public $Ean;
	public $ExtraBuyRelations;
	public $FocusCart;
	public $FocusFrontpage;
	public $GuidelinePrice;
	public $Id;
	public $ItemNumber;
	public $ItemNumberSupplier;
	public $LanguageAccess;
	public $LanguageISO;
	public $MinAmount;
	public $Online;
	public $PacketProducts;
	public $Pictures;
	public $Price;
	public $Producer;
	public $ProducerId;
	public $ProductUrl;
	public $RelatedProductIds;
	public $RelationCode;
	public $SecondaryCategories;
	public $SecondaryCategoryIds;
	public $SeoCanonical;
	public $SeoDescription;
	public $SeoKeywords;
	public $SeoLink;
	public $SeoTitle;
	public $Sorting;
	public $Status;
	public $Stock;
	public $StockLocationId;
	public $Tags;
	public $Title;
	public $Type;
	public $Unit;
	public $UnitId;
	public $Url;
	public $UserAccess;
	public $UserAccessIds;
	public $UserGroupAccess;
	public $UserGroupAccessIds;
	public $Variants;
	public $VariantTypes;
	public $VatGroup;
	public $VatGroupId;
    public $Weight;
    
    function __construct($data){
        $data = (array)$data;
        foreach($data as $key=>$row){
            switch($key){
                case 'SecondaryCategoryIds':
                    if(isset($row->item) && is_array($row->item)){
                        $this->SecondaryCategoryIds = $row->item;
                    }else{
                        $this->SecondaryCategoryIds = $row;
                    }
                    break;
                default:
                    $this->$key = $row;
                    break;
            }
        }
    }
}