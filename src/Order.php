<?php
namespace inkpro\smartwebapi;

class Order{
    public $Currency;
    public $CurrencyId;
    public $Customer;
    public $CustomerComment;
    public $CustomerId;
    public $DateDelivered;
    public $DateDue;
    public $DateSent;
    public $DateUpdated;
    public $Delivery;
    public $DeliveryComment;
    public $DeliveryId;
    public $DeliveryTime;
    public $DiscountCodes;
    public $Id;
    public $InvoiceNumber;
    public $LanguageISO;
    public $OrderComment;
    public $OrderCommentExternal;
    public $OrderLines;
    public $Origin;
    public $Packing;
    public $PackingId;
    public $Payment;
    public $PaymentId;
    public $ReferenceNumber;
    public $Site;
    public $Status;
    public $Total;
    public $TrackingCode;
    public $Transactions;
    public $User;
    public $UserId;
    public $Vat;

    function __construct($data){
        $data = (array)$data;
        foreach($data as $key=>$row){
            switch($key){
                case "OrderLines":
                    $this->OrderLines = [];
                    if(is_array($row->item)){
                        foreach($row->item as $orderLine){
                            $this->OrderLines[] = new OrderLine($orderLine);
                        }
                    }else{
                        $this->OrderLines[] = new OrderLine($row->item);
                    }
                    break;

                case "Customer":
                    $this->Customer = new Customer($row->item);
                    break;

                
                default:
                    $this->$key = $row;
                    break;
            }
        }
    }
}