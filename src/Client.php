<?php
/**
 * Communication with smartwebs API. Via SOAP! VIA SOAP!
 * 
 * @see https://www.youtube.com/watch?v=RnqAXuLZlaE
 * 
 * @package inkpro\smartwebapi
 */
namespace inkpro\smartwebapi;
use DateInterval;
use DateTime;
use Exception;
use SoapClient;
use SoapFault;

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

    private const WSDL_URL = 'https://api.hostedshop.dk/service.wsdl';

    /** @var SoapClient The SOAP client. */
    private $client;
    protected $allNewsletterUsers;
    protected $allUsers;


	/**
	 * Invokes the soap client with SmartWeb login details.
	 *
	 * @param array|null  $credentials Optional credentials to service. If null, we'll use environment vars. If string, we count on it being a shopname.
	 * @param string|null $wsdl_url
	 *
	 * @throws SoapFault
	 */
    public function __construct( ?array $credentials, ?string $wsdl_url){
        $client = new SoapClient($wsdl_url ?? self::WSDL_URL);
        $client->Solution_Connect($credentials);
        $this->client = $client;
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
        $responseName = $call. 'Result';

        return $response->$responseName->item ?? $response->$responseName;
    }

    /**
     * Sets which fields to set for a certain type
     * 
     * @param string $type Which type to set fields for (i.e. "Order")
     * @param string[] $fields Array containing the field names
     * @return bool True on success, false if failure.
     */
    public function setFields($type, $fields): bool
    {
        foreach($fields as &$field){
            $field = ucfirst($field);
        }
        unset($field);

        if ($type === 'OrderLine') {
            return $this->callApi('Order_SetOrderLineFields',array('Fields' =>implode(',',$fields)));
        }

        if($type === 'ProductVariant') {

            return $this->callApi('Product_SetVariantFields',array('Fields' =>implode(',',$fields)));
        }
        return $this->callApi(ucfirst($type). '_SetFields',array('Fields' =>implode(',',$fields)));
    }

    /**
     * Retrieves a pageText.
     * 
     * @param int $id Id of the pageText to retrieve.
     * @return object|false The PageText, if found. False if there's no product with that id.
     */
    public function getPageText($id){
        $return = $this->callApi('PageText_GetById',array('PageTextId' =>$id));
        if(isset($return->Id)){
            return new PageText($return);
        }
        return false;
    }

    /**
     * Retrieves all products.
     * 
     * @return Product[] All the products.
     */
    public function getAllProducts(): array
    {
        $products = $this->callApi('Product_GetAll');
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
    public function getProduct($id){
        $return = $this->callApi('Product_GetById',array('ProductId' =>$id));
        if(isset($return->Id)){
            return new Product($return);
        }
        return false;
    }

    /**
     * Retrives a product
     *
     * @param array $ids
     * @return array|null of found Products.
     */
    public function getProductsByIds(array $ids) {
        return $this->callApi('Product_GetByIds', ['ProductIds' => $ids]);
    }

    /**
     * Retrieves products by start- and end dates.
     *
     * @param $startDate
     * @param null $endDate
     * @return mixed [] Array of Product Objects.
     */
    public function getProductsByUpdatedDate($startDate, $endDate = null)
    {
        
        return $this->callApi('Product_GetByUpdatedDate',['Start' =>$startDate, 'End' => $endDate]);
    }

    /**
     * Updates a product.
     *
     * @param object $product Product object, should include either Id or ItemNumber prop.
     * @return mixed
     */
    public function updateProduct($product){
        return $this->callApi('Product_Update',['ProductData' =>$product]);
    }

    /**
     * Deletes a product
     *
     * @param int $id Id of the product you wish to delete.
     * @return mixed
     */
    public function deleteProduct($id){
        return $this->callApi('Product_Delete',['ProductId' =>$id]);
    }

    /**
     * Retrieves all categories.
     * 
     * @return Category[] Array with the categories.
     */
    public function getAllCategories(): array
    {
        $categories = $this->callApi('Category_GetAll');
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
     * @return Category The category
     */
    public function getCategory(int $category_id): Category
    {
        $category = $this->callApi('Category_GetById', ['CategoryId' =>$category_id]);
        return new Category($category);
    }

    /**
     * Gets all users
     * 
     * @param bool $cache Do you want to get cached users, if they exist?
     * @return object[] Array with all the users.
     */
    public function getUsers($cache = true): array
    {
        if($this->allUsers && $cache) {
            return $this->allUsers;
        }
        $response = $this->callApi('User_GetAll');
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
    public function getNewsletterUsers(): array
    {
        $response = $this->callApi('User_GetAllNewsletter', array('isNotSyncedOnly' => true));
        foreach ($response as $user) {
            $this->allNewsletterUsers[$user->Id] = new User($user);
        }
        return $this->allNewsletterUsers;
    }

    /**
     * Returns all Newsletter Users created or updated in a given timespan.
     * Currently this apparently doesn't respect the start and end date.
     *
     * @param DateTime $start The start date of the query. Note only the date portion is used.
     * @param DateTime|null $end The start date of the query. Note only the date portion is used. Defaults to current date.
     * @return object[] Array with all the users.
     * @throws Exception
     */
    public function getNewsletterUsersByDate(DateTime $start, ?DateTime $end = null): array
    {
        if(!$end) {
            $end = new DateTime;
        }
        $response = $this->callApi('User_GetAllNewsletterByDate', ['Start' =>$start->format('Y-m-d'), 'End' =>$end->format('Y-m-d')]);
        foreach ($response as $user) {
            $this->allNewsletterUsers[$user->Id] = new User($user);
        }
        return $this->allNewsletterUsers;
    }

    /**
     * UnSubscribe user from newsletter.
     *
     * @param $user
     * @return mixed
     */
    public function unsubscribeNewsletterUser($user){
        $user->Newsletter = false;
        return $this->callApi('User_Update', array('UserData' => $user));
    }  
        

    /**
     * Gets a user by id.
     * 
     * @param int $id Id of the user to fetch.
     * @return object The fetched user object.
     */
    public function getUser ($id){
        return $this->callApi('User_GetById',array('UserId' =>$id));
    }

    /**
     * Gets a user by email.
     * 
     * @param string $email The email to search for.
     * @return object|false The user if found, false if user wasn't found.
     */
    public function getUserByEmail($email){
        $this->setFields('User',array('Id', 'Email', 'Firstname', 'Lastname'));
        $allUsers = $this->getUsers(false);
        foreach($allUsers as $user){
            if($user->Email === $email) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Retrieves all users associated with a certain group. Can be heavy with big groups.
     *
     * @param int $group_id Id of the group you want to retrieve users from.
     * @return mixed
     */
    public function getUsersByGroup(int $group_id)
    {
        return $this->callApi('User_GetByGroup', ['UserGroupId' =>$group_id]);
    }

    /**
     * Creates a user.
     * 
     * @param object $user User object, corresponding to smartweb UserCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/UserCreate.html
     * @return int Id of the created user.
     */
    public function createUser($user): int
    {
        return $this->callApi('User_Create',array('UserData' =>$user));
    }

    /**
     * Retrieves currency by Iso
     *
     * @param $iso
     * @return object Currency.
     */
    public function getCurrencyByIso($iso){
        return $this->callApi('Currency_GetByIso', ['Iso' => $iso]);
    }

    public function createOrder($order){
        return $this->callApi('Order_Create',['OrderData' =>$order]);
    }

    /**
     * Retrieves an order
     * 
     * @param int $orderId Id of the order to retrieve
     * @return object The order object.
     */
    public function getOrder($orderId){
        $order = $this->callApi('Order_GetById',array('OrderId' =>$orderId));
        return new Order($order);
    }

    /**
     * Retrieves a customer
     *
     * @param $CustomerId
     * @return OrderCustomer The orderCustomer object.
     */
    public function getCustomer($CustomerId): OrderCustomer
    {
        return $this->callApi('Order_GetCustomer',array('CustomerId' =>$CustomerId));
    }

    /**
     * Receives all orders from start date to end date.
     * 
     * Be careful when using, as many orders might consume a lot of memory.
     * If it consumes all memory, try raising PHP's memory limit.
     * Sometimes it might also exhaust SOAP's built in memory. If that is the case, use getOrdersFromDate().
     * 
     * @param DateTime $start The first date to get orders from.
     * @param DateTime $end The last date to get orders from.
     * @param array $status The status codes of the orders you want to get.
     * @return array Array with the orders.
     */
    public function getOrders(DateTime $start, DateTime $end, $status=['1', '2', '3', '4', '6', '7', '8']): array
    {
        $dateFormat = 'Y-m-d';
        $options = array(
            'Start' =>$start->format($dateFormat),
            'End' =>$end->format($dateFormat),
            'Status' =>implode(',', $status)
        );
        $orders = [];
        $response = $this->callApi('Order_GetByDate',$options);
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
     * @param DateTime $from The first date to get orders from.
     * @param array $status
     * @return object[] Array of orders.
     * @throws Exception
     */
    public function getOrdersFromDate(DateTime $from, $status=['1', '2', '3', '4', '6', '7', '8']): array
    {
        $month = new DateInterval('P1M');
        $day = new DateInterval('P1D');
        $now = new DateTime();
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
     * @return mixed
     */
    public function getUsersOrders ($userId){
        return $this->callApi('Order_GetByDateAndUser',array(
            'UserId' =>$userId,
            'Start' =>null,
            'End' =>null
        ));
    }

    /**
     * Gets orders by a certain status.
     * 
     * @param string|array $status Array or string containing which statuses to get, i.e. "3" to get orders that are sent.
     * @param string|DateTime|null $start The start date of the query. Null fetches from first entry.
     * @param string|DateTime|null $end The end date of the query. Null to fetch orders to current date.
     * @return object[] The orders, if any.
     */
    public function getUpdatedOrders($status, $start=null, $end=null): array
    {
        $options = array();
        if(is_array($status)){
            $options['Status']= implode(',',$status);
        }elseif(is_string($status)){
            $options['Status'] = $status;
        }

        if($start instanceof DateTime && $end instanceof DateTime){
            $format = 'Y-m-d H:i:s';
            $options['Start'] = $start->format($format);
            $options['End'] = $end->format($format);
        }else{
            $options['Start'] = $start;
            $options['End'] = $end;
        }
        return $this->callApi('Order_GetByDateUpdated',$options);
    }

    /**
     * Gets all orderlines from a certain order.
     *
     * @param int $orderId Id of the order to fetch lines from.
     * @return mixed
     */
    public function getOrderLines ($orderId){
        return $this->callApi('Order_GetLines',array('OrderId' =>$orderId));
    }

    /**
     * Updates orderline status.
     *
     * @param OrderLine $order_line Orderline to update. Must have Id property.
     * @param int $status Optional. If you haven't updated your order object, you can supply this.
     * @return mixed
     */
    public function updateOrderLineStatus($order_line, $status = null){
        if($status) {
            $order_line->Status = $status;
        }
        return $this->callApi('Order_UpdateLineStatus', ['OrderLineId' =>$order_line->Id, 'Status' =>$order_line->Status]);
    }

    /**
     * Updates the comment on an order.
     *
     * @param Order $order The order to update.
     * @param string $comment Optional. If you haven't updated your order object, you can supply this.
     * @param bool $append
     * @return mixed
     */
    public function updateOrderComment($order, $comment = null, $append = false){
        if($comment){
            if($append){
                $order->OrderComment = $order->OrderComment !== '' ? $order->OrderComment."\n".$comment : $comment;
            }else{
                $order->OrderComment = $comment;
            }
        }
        return $this->callApi('Order_UpdateComment', ['OrderId' =>$order->Id, 'Text' =>$order->OrderComment]);
    }

    /**
     * Updates the status on an order.
     *
     * @param Order $order The order to update.
     * @param int $status Optional. If you haven't updated your order object, you can supply this.
     * @return mixed
     */
    public function updateOrderStatus($order, $status = null){
        if($status !== null) {
            $order->Status = $status;
        }
        return $this->callApi('Order_UpdateStatus', ['OrderId' =>$order->Id, 'Status' =>$order->Status]);
    }

    /**
     * @param Order $order The order to update.
     * @param int $maturity_interval The amount of days from today's date to set the maturity date for the Invoice (0, 8, 14 or 30 days).
     * @return mixed
     */
    public function createInvoice($order, $maturity_interval){
        return $this->callApi('Order_CreateInvoice', ['OrderId' =>$order->Id, 'MaturityDayInterval' =>$maturity_interval]);
    }

    /**
     * Gets all images linked to a product id.
     * 
     * @param int $productId Id of the product.
     * @param int $shopId The id of the shop (i.e. 1434 for shop1434.hostedshop.dk)
     * @return object[] Array of stdObjects containing data about the image.
     */
    public function getProductImages ($productId, $shopId): array
    {
        $prefix = 'https://shop'.$shopId.'.hstatic.dk/upload_dir/shop/';
        $images = $this->callApi('Product_GetPictures',array(
            'ProductId' =>$productId
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
    public function searchProducts($search): array
    {
        return $this->callApi('Product_Search',array('SeachString' =>$search));
    }

    public function getAllDeliveries(){
        return $this->callApi('Delivery_GetAll');
    }

    public function getProductDeliveryTimes(){
        return $this->callApi('Product_GetDeliveryTimeAll');
    }

    public function getDiscounts(){
        return $this->callApi('Discount_GetAll');
    }

    public function getPaymentMethods(){
        return $this->callApi('Payment_GetAll');
    }

    public function updateDroppointId($order, $droppoint_id){
        if($return = $this->callApi('Delivery_UpdateDropPoint', ['OrderId' =>$order->Id, 'DropPointId' =>$droppoint_id])){
            $order->Delivery->DroppointId = $droppoint_id;
            return $return;
        }
        return false;
    }

    public function setTransaction($transaction_obj){
        return $this->callApi('Order_SetTransactionCode', ['TransactionData' =>$transaction_obj]);
    }

    public function getAllCustomData(){
        return $this->callApi('Product_GetCustomDataAll');
    }

    public function getCustomDataByType(int $type_id){
        return $this->callApi('Product_GetCustomDataByType', ['CustomDataTypeId' =>$type_id]);
    }

    public function getCustomDataType(int $type_id){
        return $this->callApi('Product_GetCustomDataType', ['CustomDataTypeId' =>$type_id]);
    }

    public function getAllAdditionalTypes(){
        return $this->callApi('Product_GetAdditionalTypesAll');
    }
    
    public function getDeliveryMethods(){
        return $this->callApi('Delivery_GetAll');
    }

    public function getOrderDelivery($delivery_id)
    {
        return $this->callApi('Order_GetDelivery', ['DeliveryId' =>$delivery_id]);
    }

    public function getOrderLineAddresses($orderline_id)
    {
        return $this->callApi('Order_GetLineAddresses', ['OrderLineId' =>$orderline_id]);
    }

    public function getAllCurrencies()
    {
        return $this->callApi('Currency_GetAll');
    }

    /**
     * Retrieve all product variants
     *
     * @param int $product_id
     * @return mixed
     */
    public function getProductVariants(int $product_id){
        return $this->callApi('Product_GetVariants', ['ProductId' => $product_id]);
    }

    /**
     * Retrieve all order status codes
     *
     * @return mixed
     */
    public function getOrderStatusCodes()
    {
        return $this->callApi('OrderStatusCode_GetAll');
    }

    /**
     * Retrieves a product variant by it's ItemNumber
     *
        * @param int $item_number ItemNumber of the product variant to retrieve.
     * @return object|false The product variant, if found. False if there's no product variant with that ItemNumber.
     */
    public function getProductVariantByItemNumber($item_number){
        $return = $this->callApi('Product_GetVariantsByItemNumber',array('ItemNumber' =>$item_number));
        if(isset($return->Id)){
            return $return;
        }
        return false;
    }

    /**
     * Retrieves a product by it's ItemNumber
     *
     * @param int $item_number ItemNumber of the product to retrieve.
     * @return object|false The product, if found. False if there's no product with that ItemNumber.
     */
    public function getProductByItemNumber($item_number){
        $return = $this->callApi('Product_GetByItemNumber',array('ItemNumber' =>$item_number));
        if(isset($return->Id)){
            return new Product($return);
        }
        return false;
    }

    /**
     * Updates a product variant
     *
     * @param object $product_variant Product variant object, should include either Id or ItemNumber prop.
     * @return mixed
     */
    public function updateProductVariant($product_variant){
        return $this->callApi('Product_UpdateVariant',['VariantData' =>$product_variant]);
    }

}

