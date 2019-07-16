<?php
namespace inkpro\smartwebapi;

class OrderLine{
	public $AdditionalTitle;
	public $Amount;
	public $BuyPrice;
	public $DeliveryId;
	public $Discount;
	public $DiscountRounded;
	public $FileDownloadId;
	public $Id;
	public $ItemNumber;
	public $ItemNumberSupplier;
	public $LineAddresses;
	public $OfflineProduct;
	public $OrderId;
	public $PacketId;
	public $PacketLines;
	public $PacketTitle;
	public $Price;
	public $PriceRounded;
	public $ProductId;
	public $ProductTitle;
	public $ServiceType;
	public $Status;
	public $StockLocationId;
	public $StockStatus;
	public $TrackingCode;
	public $Unit;
	public $VariantId;
	public $VariantTitle;
	public $Vat;
	public $VatRate;
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