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
Usage of available methods:

Retrieves all products
```php
$products = $client->getAllProducts();
```

Retrieves a product
```php
$product = $client->getProduct();
```

Updates a product
```php
@param object $product Product object, should include either Id or ItemNumber prop.
$status = $client->updateProduct($product);
```

Deletes a product
```php
$status = $client->deleteProduct($id);
```

Retrieves all categories
```php
$categories = $client->getAllCategories();
```

Retrieves a category
```php
$category = $client->getCategory($categoryId);
```

Retrieves all users.
```php
$users = $client->getUsers();
```

Retrieves all newsletter users.
```php
$users = $client->getNewsletterUsers();
```

UnSubscribe user from newsletter.
```php
$status = $client->unsubscribeNewsletterUser($user);
```

Gets a user by id.
```php
$user = $client->getUser($id);
```

Gets a user by email.
```php
$user = $client->getUserByEmail($email);
```

Creates a user
```php
@param object $user User object, corresponding to smartweb UserCreate schema.
* @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/UserCreate.html
$status = $client->createUser($user);
```

Create order
```php
@param object $order OrderData object, corresponding to smartweb Order_Create schema.
$status = $client->createOrder($order);
```

Get Order
```php
$order = $client->getOrder($orderId);
```

Retrieves all Orders
```php
* @param DateTime $start The first date to get orders from.
* @param DateTime $end The last date to get orders from.
* @param array $status The status codes of the orders you want to get.
$orders = $client->getOrders($start, $end, $status);
```

Gets all orders made by a certain user.
```php
$orders = $client->getUsersOrders($userId);
```

Updates the comment on an order.
```php
* @param $order Order to update.
$status = $client->updateOrderComment($order, $comment);
```

Updates the status on an order.
```php
* @param $order Order to update.
$status = $client->updateOrderStatus($order, $status);
```

Creates invoice from an order.
```php
* @param $order Order
* @param int $maturity_interval The amount of days from today's date to set the maturity date for the Invoice (0, 8, 14 or 30 days).
$status = $client->createInvoice($order, $maturity_interval);
```

Gets all images linked to a product id.
```php
$images = $client->getProductImages($productId, $shopId);
```

Gets all orderlines from a certain order.
```php
$orderLines = $client->getOrderLines($orderId);
```

Gets all orderlines from a certain order.
```php
$orderLines = $client->getOrderLines($orderId);
```

Updates orderline status
```php
* @param OrderLine $order_line Orderline to update. Must have Id property.
$status = $client->updateOrderLineStatus($orderline, $status);
```



Retrieves a customer
```php
$order = $client->getCustomer($orderId);
```


