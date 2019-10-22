<?php
/**
 * Communication with smartwebs API. Via SOAP! VIA SOAP!
 * 
 * @see https://www.youtube.com/watch?v=RnqAXuLZlaE
 * 
 * @package inkpro\smartwebapi
 */
namespace inkpro\smartwebapi;
/**
 * Wrapper class for communication with the smartweb API.
 * 
 * Documentation for the API can be found here: https://api.smart-web.dk/doc/
 * Smartweb uses a soap client to pool from their servers.
 * 
 * To initiate, use `$client = new \inkpro\smartwebapi\Client(["Username"=>"myuser", "Password"=>"mypassword"]);`.
 * 
 * @author Esben Tind <esben@inkpro.dk>
 * @see https://api.smart-web.dk/doc/
 */
class Client{

    private const WSDL_URL = "https://api.hostedshop.dk/service.wsdl";

    /** @var \SoapClient The SOAP client. */
    private $client = null;
    

    /**
     * Envokes the soap client with smartweb login details.
     * 
     * @param array|null $credentials Optional credentials to service. If null, we'll use environment vars. If string, we count on it being a shopname.
     * @return bool True if soap client successfully connected. Throws exception on error.
     */
    function __construct($credentials){
        $client = new \SoapClient(self::WSDL_URL);
        $client->Solution_Connect($credentials);
        $this->client = $client;
        return true;
    }

    /**
     * Internal method for handling API calls. All API calls should use this.
     * 
     * @param string $call Which API call to execute, i.e. "User_GetAll".
     * @param array $settings Settings to pass to the API.
     * @return mixed The response from the API.
     */
    private function callApi($call, $settings=array()){
        $response = $this->client->$call($settings);
        $responseName = $call."Result";
        switch(true){
            case is_bool($response->$responseName):
                return $response->$responseName;
                break;
            case isset($response->$responseName->item):
                return $response->$responseName->item;
                break;
            case is_object($response->$responseName):
                return $response->$responseName;
                break;
            default:
                return $response->$responseName;
                break;
        }
    }

    /**
     * Sets which fields to set for a certain type
     * 
     * @param string $type Which type to set fields for (i.e. "Order")
     * @param string[] $fields Array containing the field names
     * @return bool True on success, false if failure.
     */
    function setFields($type, $fields){
        foreach($fields as &$field){
            $field = ucfirst($field);
        }
        if($type == "OrderLine"){
            return $this->callApi("Order_SetOrderLineFields",array("Fields"=>implode(",",$fields)));
        }
        return $this->callApi(ucfirst($type)."_SetFields",array("Fields"=>implode(",",$fields)));
    }

    /**
     * Retrieves all products.
     * 
     * @return \inkpro\smartwebapi\Product[] All the products.
     */
    function getAllProducts(){
        $products = $this->callApi("Product_GetAll");
        $return = [];
        foreach($products as $product){
            $return[] = new Product($product);
        }
        return $return;
    }

    /**
     * Retrieves a product.
     * 
     * @param int $id Id of the product to retrieve.
     * @return object|false The product, if found. False if there's no product with that id.
     */
    function getProduct($id){
        $return = $this->callApi("Product_GetById",array("ProductId"=>$id));
        if(isset($return->Id)){
            $product = new Product($return);
            return $product;
        }
        return false;
    }

    /**
     * Retrives a product
     * 
     * @param int $id Id of the product you want to retrieve.
     * @return object of found Products.
     */
    function getProductsByIds(array $ids) : ?array {
        return $this->callApi("Product_GetByIds", ["ProductIds"=>implode(",", $ids)]);
    }

    /**
     * Retrieves products by start- and end dates.
     * 
     * @param string|\DateTime $start The start date of the query.
     * @param string|\DateTime|null $end The end date of the query. Null to fetch products to current date.
     * @return [] Array of Product Objects.
     */
    function getProductsByUpdatedDate($startDate, $endDate = null) {
    	
    	return $this->callApi("Product_GetByUpdatedDate",["Start"=>$startDate, "End" => $endDate]);
    }

    /**
     * Updates a product.
     * 
     * @param object $product Product object, should include either Id or ItemNumber prop.
     */
    function updateProduct($product){
        return $this->callApi("Product_Update",["ProductData"=>$product]);
    }

    /**
     * Deletes a product
     * 
     * @param int $id Id of the product you wish to delete.
     */
    function deleteProduct($id){
        return $this->callApi("Product_Delete",["ProductId"=>$id]);
    }

    /**
     * Retrieves all categories.
     * 
     * @return \inkpro\smartwebapi\Category[] Array with the categories.
     */
    function getAllCategories(){
        $categories = $this->callApi("Category_GetAll");
        $return = [];
        foreach($categories as $category){
            $return[] = new Category($category);
        }
        return $return;
    }

    /**
     * Retrives a category
     * 
     * @param int $category_id Id of the category you want to get.
     * @return \inkpro\smartwebapi\Category The category
     */
    function getCategory(int $category_id)
    {
        $category = $this->callApi("Category_GetById", ["CategoryId"=>$category_id]);
        return new Category($category);
    }

    /**
     * Gets all users
     * 
     * @param bool $cache Do you want to get cached users, if they exist?
     * @return object[] Array with all the users.
     */
    function getUsers($cache = true){
        if($this->allUsers && $cache) return $this->allUsers;
        $response = $this->callApi("User_GetAll");
        foreach($response as $user){
            $this->allUsers[$user->Id] = $user;
        }
        return $this->allUsers;
    }

    /**
     * Gets all Newsletter users
     * 
     * @return object[] Array with all the users.
     */
    function getNewsletterUsers(){
    	$response = $this->callApi("User_GetAllNewsletter", array('isNotSyncedOnly' => true));
        foreach ($response as $user) {
        	$this->allNewsletterUsers[$user->Id] = $user;
        }
        return $this->allNewsletterUsers;
    }

    /**
     * UnSubscribe user
     * 
     * @param int $id Id of the user you wish to unsubscribe.
     */
    function unsubscribeNewsletterUser($user){
        $user->Newsletter = false;
        return $this->callApi("User_Update", array("UserData" => $user));
    }  
    	

    /**
     * Gets a user by id.
     * 
     * @param int $id Id of the user to fetch.
     * @return object The fetched user object.
     */
    function getUser ($id){
        return $this->callApi("User_GetById",array("UserId"=>$id));
    }

    /**
     * Gets a user by email.
     * 
     * @param string $email The email to search for.
     * @return object|false The user if found, false if user wasn't found.
     */
    function getUserByEmail($email){
        $this->setFields("User",array("Id","Email","Firstname","Lastname"));
        $allUsers = $this->getUsers(false);
        foreach($allUsers as $user){
            if($user->Email == $email) return $user;
        }
        return false;
    }

    /**
     * Retrieves all users associated with a certain group. Can be heavy with big groups.
     * 
     * @param int $group_id Id of the group you want to retrieve users from.
     */
    function getUsersByGroup(int $group_id)
    {
        return $this->callApi("User_GetByGroup", ["UserGroupId"=>$group_id]);
    }

    /**
     * Creates a user.
     * 
     * @param object $user User object, corresponding to smartweb UserCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/UserCreate.html
     * @return int Id of the created user.
     */
    function createUser($user){
        return $this->callApi("User_Create",array("UserData"=>$user));
    }

    /**
     * Retrieves currency by Iso
     * 
     * @return object Currency.
     */
    function getCurrencyByIso($iso){
        return $this->callApi("Currency_GetByIso", ["Iso" => $iso]);
    }

    function createOrder($order){
        return $this->callApi("Order_Create",["OrderData"=>$order]);
    }

    /**
     * Retrieves an order
     * 
     * @param int $orderId Id of the order to retrieve
     * @return object The order object.
     */
    function getOrder($orderId){
        $order = $this->callApi("Order_GetById",array("OrderId"=>$orderId));
        $return = new Order($order);
        return $return;
    }

    /**
     * Receives all orders from start date to end date.
     * 
     * Be careful when using, as many orders might consume a lot of memory.
     * If it consumes all memory, try raising PHP's memory limit.
     * Sometimes it might also exhaust SOAP's built in memory. If that is the case, use getOrdersFromDate().
     * 
     * @param \DateTime $start The first date to get orders from.
     * @param \DateTime $end The last date to get orders from.
     * @param array $status The status codes of the orders you want to get.
     * @return object[] Array with the orders.
     */
    function getOrders(\DateTime $start, \DateTime $end, $status=["1","2","3","4","6","7","8"]){
        $dateFormat = "Y-m-d";
        $options = array(
            "Start"=>$start->format($dateFormat),
            "End"=>$end->format($dateFormat),
            "Status"=>implode(",", $status)
        );
        $orders = [];
        $response = $this->callApi("Order_GetByDate",$options);
        if(is_array($response) && count($response) > 0){
            foreach($response as $order){
                $orders[] = new Order($order);
            }
        }elseif(is_object($response) && count((array)$response) > 0){
            $orders[] = new Order($response);
        }
        return $orders;
    }

    /**
     * Receives all orders from a certain date.
     * 
     * Fetches one month at the time to avoid consuming all SOAP's built in ressources.
     * Use this to get many orders at once. Might take a while to run. Remember to raise memory limit.
     * 
     * @param \DateTime $from The first date to get orders from.
     * @return object[] Array of orders.
     */
    function getOrdersFromDate(\DateTime $from, $status=["1","2","3","4","6","7","8"]){
        $month = new \DateInterval("P1M");
        $day = new \DateInterval("P1D");
        $now = new \DateTime();
        $orders = [];
        while($from < $now){
            $end = clone $from;
            $end->add($month);
            $orders = array_merge($orders, $this->getOrders($from, $end, $status));
            $from->add($month)->add($day);
        }
        return $orders;
    }
    
    /**
     * Gets all orders made by a certain user.
     * 
     * @param int $userId Id of the user to fetch orders from.
     */
    function getUsersOrders ($userId){
        return $this->callApi("Order_GetByDateAndUser",array(
            "UserId"=>$userId,
            "Start"=>null,
            "End"=>null
        ));
    }

    /**
     * Gets orders by a certain status.
     * 
     * @param string|array $status Array or string containing which statuses to get, i.e. "3" to get orders that are sent.
     * @param string|\DateTime|null $start The start date of the query. Null fetches from first entry.
     * @param string|\DateTime|null $end The end date of the query. Null to fetch orders to current date.
     * @return object[] The orders, if any.
     */
    function getUpdatedOrders($status, $start=null, $end=null){
        $options = array();
        if(is_array($status)){
            $options["Status"]= implode(",",$status);
        }elseif(is_string($status)){
            $options["Status"] = $status;
        }

        if($start instanceof DateTime && $end instanceof DateTime){
            $format = "Y-m-d H:i:s";
            $options["Start"] = $start->format($format);
            $options["End"] = $end->format($format);
        }else{
            $options["Start"] = $start;
            $options["End"] = $end;
        }
        return $this->callApi("Order_GetByDateUpdated",$options);
    }

    /**
     * Gets all orderlines from a certain order.
     * 
     * @param int $orderId Id of the order to fetch lines from.
     */
    function getOrderLines ($orderId){
        return $this->callApi("Order_GetLines",array("OrderId"=>$order_id));
    }

    /**
     * Updates orderline status.
     * 
     * @param \inkpro\smartwebapi\OrderLine $order_line Orderline to update. Must have Id property.
     * @param int $status Optional. If you haven't updated your order object, you can supply this.
     */
    function updateOrderLineStatus($order_line, $status = null){
        if($status) $order_line->Status = $status;
        return $this->callApi("Order_UpdateLineStatus", ["OrderLineId"=>$order_line->Id, "Status"=>$order_line->Status]);
    }

    /**
     * Updates the comment on an order.
     * 
     * @param \inkpro\smartwebapi\Order $order The order to update.
     * @param string $comment Optional. If you haven't updated your order object, you can supply this.
     */
    function updateOrderComment($order, $comment = null, $append = false){
        if($comment){
            if($append){
                $order->OrderComment = strlen($order->OrderComment) > 0 ? $order->OrderComment."\n".$comment : $comment;
            }else{
                $order->OrderComment = $comment;
            }
        }
        return $this->callApi("Order_UpdateComment", ["OrderId"=>$order->Id, "Text"=>$order->OrderComment]);
    }

    /**
     * Updates the status on an order.
     * 
     * @param \inkpro\smartwebapi\Order $order The order to update.
     * @param int $status Optional. If you haven't updated your order object, you can supply this.
     */
    function updateOrderStatus($order, $status = null){
        if($status !== null) $order->Status = $status;
        return $this->callApi("Order_UpdateStatus", ["OrderId"=>$order->Id, "Status"=>$order->Status]);
    }

    /**
     * @param \inkpro\smartwebapi\Order $order The order to update.
     * @param int $maturity_interval The amount of days from today's date to set the maturity date for the Invoice (0, 8, 14 or 30 days).
     */
    function createInvoice($order, $maturity_interval){
        return $this->callApi("Order_CreateInvoice", ["OrderId"=>$order->Id, "MaturityDayInterval"=>$maturity_interval]);
    }

    /**
     * Gets all images linked to a product id.
     * 
     * @param int $productId Id of the product.
     * @param int $shopId The id of the shop (i.e. 1434 for shop1434.hostedshop.dk)
     * @return object[] Array of stdObjects containing data about the image.
     */
    function getProductImages ($productId, $shopId){
        $prefix = 'https://shop'.$shopId.'.hstatic.dk/upload_dir/shop/';
        $images = $this->callApi("Product_GetPictures",array(
            "ProductId"=>$productId
        ));
        if(!is_array($images)){
            $images = array($images);
        }
        foreach($images as &$image){
            $image->FilePath = $prefix.$image->FileName;
        }
        return $images;
    }

    /**
     * Searches for products containing a certain string.
     * 
     * @param string The string to search for.
     * @return object[] The search results if any.
     */
    function searchProducts($search){
        return $this->callApi("Product_Search",array("SeachString"=>$search));
    }

    function getAllDeliveries(){
        return $this->callApi("Delivery_GetAll");
    }

    function getProductDeliveryTimes(){
        return $this->callApi("Product_GetDeliveryTimeAll");
    }

    function getDiscounts(){
        return $this->callApi("Discount_GetAll");
    }

    function getPaymentMethods(){
        return $this->callApi("Payment_GetAll");
    }

    function updateDroppointId($order, $droppoint_id){
        if($return = $this->callApi("Delivery_UpdateDropPoint", ["OrderId"=>$order->Id, "DropPointId"=>$droppoint_id])){
            $order->Delivery->DroppointId = $droppoint_id;
            return $return;
        }
        return false;
    }

    function setTransaction($order, $transaction_obj){
        return $this->callApi("Order_SetTransactionCode", ["TransactionData"=>$transaction_obj]);
    }

    function getAllCustomData(){
        return $this->callApi("Product_GetCustomDataAll");
    }

    function getCustomDataByType(int $type_id){
        return $this->callApi("Product_GetCustomDataByType", ["CustomDataTypeId"=>$type_id]);
    }

    function getAllAdditionalTypes(){
        return $this->callApi("Product_GetAdditionalTypesAll");
    }
    
    function getDeliveryMethods(){
        return $this->callApi("Delivery_GetAll");
    }

    function getOrderDelivery($delivery_id)
    {
        return $this->callApi("Order_GetDelivery", ["DeliveryId"=>$delivery_id]);
    }

    function getOrderLineAddresses($orderline_id)
    {
        return $this->callApi("Order_GetLineAddresses", ["OrderLineId"=>$orderline_id]);
    }

    function getAllCurrencies()
    {
        return $this->callApi("Currency_GetAll");
    }

}
?>
