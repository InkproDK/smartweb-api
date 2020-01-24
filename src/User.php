<?php
namespace inkpro\smartwebapi;

class User
{
    public $Address;
    public $Address2;
    public $Approved;
    public $BirthDate;
    public $City;
    public $Company;
    public $Consent;
    public $ConsentDate;
    public $Country;
    public $CountryCode;
    public $Currency;
    public $CustomData;
    public $Cvr;
    public $DateCreated;
    public $DateUpdated;
    public $Description;
    public $DiscountGroupId;
    public $Ean;
    public $Email;
    public $Fax;
    public $Firstname;
    public $Id;
    public $InterestFields;
    public $LanguageAccess;
    public $LanguageISO;
    public $Lastname;
    public $Mobile;
    public $Newsletter;
    public $Number;
    public $Password;
    public $Phone;
    public $Referer;
    public $Sex;
    public $ShippingAddress;
    public $ShippingAddress2;
    public $ShippingCity;
    public $ShippingCompany;
    public $ShippingCountry;
    public $ShippingCountryCode;
    public $ShippingCvr;
    public $ShippingEan;
    public $ShippingEmail;
    public $ShippingFirstname;
    public $ShippingLastname;
    public $ShippingMobile;
    public $ShippingPhone;
    public $ShippingReferenceNumber;
    public $ShippingState;
    public $ShippingType;
    public $ShippingZip;
    public $Site;
    public $Type;
    public $Url;
    public $UserGroupId;
    public $Username;
    public $Zip;

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