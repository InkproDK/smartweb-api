<?php
namespace inkpro\tests\smartwebapi;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;
use inkpro\smartwebapi\Client;

class SmartwebTestCase extends TestCase
{
    protected static $client;

    public static function setUpBeforeClass() : void
    {
        $dotenv = Dotenv::create(__DIR__);
        $dotenv->load();
        $credentials = [
            "Username"=>$_ENV["WANNAFIND_USER"],
            "Password"=>$_ENV["WANNAFIND_PASS"]
        ];
        self::$client = new Client($credentials);
    }
}