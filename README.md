# Wrapper class for communication with the smartweb API.

Documentation for the API can be found here: https://api.hostedshop.dk/doc/  
Smartweb uses a soap client to pool from their servers.

The easiest way to initiate: 
```php
$client = new \inkpro\smartwebapi\Client(["Username"=>"myuser", "Password"=>"mypassword"]);
```

If you need to specify a wsdl url, you can do like this:
```php
$client = new \inkpro\smartwebapi\Client(
    ["Username"=>"myuser", "Password"=>"mypassword"],
    "https://customwsdl.url"
);
```

For available methods, take a look in `/src/Client.php`.