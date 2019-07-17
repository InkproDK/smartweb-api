# Wrapper class for communication with the smartweb API.

Documentation for the API can be found here: https://api.hostedshop.dk/doc/  
Smartweb uses a soap client to pool from their servers.

The easiest way to initiate: `$client = new \inkpro\smartwebapi\Client(["Username"=>"myuser", "Password"=>"mypassword"]);`.