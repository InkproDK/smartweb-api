<?php
namespace inkpro\tests\smartwebapi;

class ClientTest extends SmartwebTestCase
{
    public function testCanConnect()
    {
        $this->assertInstanceOf(\inkpro\smartwebapi\Client::class, self::$client);
    }

    public function testCanGetAllCurrencies()
    {
        $currencies = self::$client->getAllCurrencies();
        $this->assertIsObject($currencies, "currencies are not an object");
    }

    public function testCanGetCurrencyByISO()
    {
        $iso = "DKK";
        $currency = self::$client->getCurrencyByISO($iso);
        $this->assertInstanceOf(\stdClass::class, $currency);
    }

    public function testCatGetAllDeliveries()
    {
        $deliveries = self::$client->getAllDeliveries();
        $this->assertIsArray($deliveries);
    }
}