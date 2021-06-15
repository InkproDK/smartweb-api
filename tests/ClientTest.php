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
        $this->assertIsArray($currencies, "currencies are not an object");
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

    public function testCanGetNewsletterUsers()
    {
        $users = self::$client->getNewsletterUsers();
        $this->assertIsArray($users);
        $this->assertInstanceOf(\inkpro\smartwebapi\User::class, array_values($users)[0]);
    }

    public function testCanGetNewsletterUsersByDate()
    {
        $users = self::$client->getNewsletterUsersByDate(new \DateTime("yesterday"));
        $this->assertIsArray($users);
        $this->assertInstanceOf(\inkpro\smartwebapi\User::class, array_values($users)[0]);
    }
}