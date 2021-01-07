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
    public function __construct( ?array $credentials, ?string $wsdl_url = null){
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
     * Creates category.
     * 
     * @param object $category Category object, corresponding to smartweb CategoryCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/CategoryCreate.html
     * @return int Id of the created category.
     */
    public function createCategory($category)
    {
        return $this->callApi('Category_Create',['CategoryData' =>$category]);
    }

    /**
     * Creates or updates a Category. Assumes that only unique Category titles exist for a given level in the shop. If the Title supplied is not found on the level a new Category is created
     * 
     * $param object $category The input in CategoryCreateUpate object format.
     * $return int Id of the created or udpated Category
     */
    public function createOrUpdateCategory($category)
    {
        return $this->callApi('Category_CreateOrUpdate',['CategoryData' =>$category]);
    }

    /**
     * Creates a new CategoryPicture
     * 
     * @param object $categoryPicture, The input in CategoryPictureCreate object format
     * @return int Id of the newly created CategoryPicture
     */
    public function createCategoryPicture($categoryPicture)
    {
        return $this->callApi("Category_CreatePicture", ["CategoryPictureData" => $categoryPicture]);
    }

    /**
     * Deletes category
     *
     * @param int $id Id of the Category you wish to delete.
     * @return mixed
     */
    public function deleteCategory($id){
        return $this->callApi('Category_Delete',['CategoryId' =>$id]);
    }

    /**
     * Deletes CategoryPicture
     *
     * @param int $id Id of the CategoryPicture you wish to delete.
     * @return mixed
     */
    public function deleteCategoryPicture($id)
    {
        return $this->callApi("Category_DeletePicture", ["CategoryPictureId" => $id]);
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
     * Retrives categoryPictures of the indicated category
     * 
     * @param int $category_id Id of the category of the wanted CategoryPictures
     * @return Category CategoryPicture[] Array with the CategoryPicture.
     */
    public function getCategoryPictures(): array
    {
        $pictures = $this->callApi("Category_GetPictures");
        $return = [];
        foreach($pictures as $picture){
            $return[] = new CategoryPicture($picture);
        }

        return $return;
    }

    /**
     * Updates a new Category
     * 
     * @param object $categoryPicture, The input in CategoryPictureCreate object format
     * @return int Id of the newly created CategoryPicture
     */
    public function updateCategory($category): int
    {
        return $this->callApi('Category_Update',array('CategoryData' =>$category));
    }

    /**
     * Updates a CategoryPicture
     * 
     * @param object $categoryPicture, The input in CategoryPictureUpdate object format
     * @return int Id of the updated CategoryPicture
     */
    public function updateCategoryPicture($categoryPicture)
    {
        return $this->callApi("Category_UpdatePicture", ["CategoryPictureData" => $categoryPicture]);
    }

    /**
     * Updates Category Title
     * 
     * @param int $CategoryId, Id of the Category to update
     * @param string $Title The new title
     * @param string $LanguageISO, The LanguageISO the new title should be set in
     * @return boolean true if the Title was updated, false at failure
     */
    public function updateCategoryTitle($categoryId, $title, $languageISO)
    {
        return $this->callApi('Category_UpdateTitle',array('CategoryId' =>$categoryId, "Title" => $title, "LanguageISO" => $languageISO));
    }

    /**
     * Creates currency.
     * 
     * @param object $currency Currency object, corresponding to smartweb CurrencyCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/CurrencyCreate.html
     * @return int Id of the created currency.
     */
    public function createCurrency($currency){
        return $this->callApi('Currency_Create',['CurrencyData' =>$currency]);
    }

    /**
     * Deletes currency
     *
     * @param int $id Id of the Currency you wish to delete.
     * @return mixed
     */
    public function deleteCurrency($id){
        return $this->callApi('Currency_Delete',['CurrencyId' =>$id]);
    }

    /**
     * Retrives all currencies
     * 
     * @return Currency Currency[] Array with the Currency.
     */
    public function getAllCurrencies()
    {
        $currencies = $this->callApi('Currency_GetAll');
        $return = [];
        foreach($currencies as $currency){
            $return[] = new Currency($currency);
        }
        return $return;
    }

    /**
     * Retrieves currency by Iso
     *
     * @param string $iso The isocode of the wanted Currency
     * @return object Currency.
     */
    public function getCurrencyByIso($iso){
        return $this->callApi('Currency_GetByIso', ['Iso' => $iso]);
    }

    /**
     * Updates Currency
     * 
     * @param object CurrencyData The input in CurrencyUpdate Object format
     * @return int Id of the currency
     */
    public function updateCurrency($currency): int
    {
        return $this->callApi('CurrencyData',array('CurrencyData' =>$currency));
    }

    /**
     * Retrives all delivery methods
     * 
     * @return Delivery Delivery[] Array with the Delivery.
     */
    public function getAllDeliveries(){
        $deliveries = $this->callApi('Delivery_GetAll');
        $return = [];
        foreach($deliveries as $delivery){
            $return[] = new Delivery($delivery);
        }
        return $return;
    }

    /**
     * Retrives all delivery methods available by region
     * 
     * @param int Zip code of the region
     * @param string CountryCode of the region
     * @param double Weight of the order
     * @return Delivery Delivery[] Array of Delivery objects.
     */
    public function getDeliveryByLocation($zip, $countryCode, $weight): array
    {
        $deliveries = $this->callApi('Delivery_GetByLocation', ["Zip" => $zip, "CountryCode" => $countryCode, "Weight" => $weight]);
        $return = [];
        foreach($deliveries as $delivery){
            $return[] = new Delivery($delivery);
        }
        return $return;
    }

    /**
     * Updates a DropPointId of an OrderDelivery
     * 
     * @param int OrderId of the OrderDelivery
     * @param string DropPointId of the OrderDelivery
     * @return mixed
     */
    public function updateDroppointId($order, $droppoint_id){
        if($return = $this->callApi('Delivery_UpdateDropPoint', ['OrderId' =>$order->Id, 'DropPointId' =>$droppoint_id])){
            $order->Delivery->DroppointId = $droppoint_id;
            return $return;
        }
        return false;
    }

    /**
     * Creates a new DiscountGroupProduct
     * 
     * @param object DiscountGroupProductData input in DiscountGroupProductCreate Object format
     * @return Id of the newly created DiscountGroupProduct
     */
    public function createDiscountGroupProduct($discountGroupProduct)
    {
        return $this->callApi("DiscountGroupProduct_Create", ["DiscountGroupProductData" => $discountGroupProduct]);
    }

    /**
     * Retrives all delivery methods
     * 
     * @return Delivery Delivery[] Array with the Delivery.
     */
    public function getDeliveryMethods(){
        return $this->callApi('Delivery_GetAll');
    }

    /**
     * Deletes a DiscountGroupProduct
     * 
     * @param string $title The title of the DiscountGroup to delete
     * @return mixed
     */
    public function deleteDiscountGroupProduct($title)
    {
        return $this->callApi("DiscountGroupProduct_Delete", ["DiscounProducttGroupTitle" => $title]);
    }

    /**
     * Retrieves all DiscountGroupProducts
     * 
     * @return array of DiscountGroupProduct Objects
     */
    public function getDiscountGroupProducts()
    {
        return $this->callApi("DiscountGroupProduct_GetAll");
    }

    /**
     * Get indicted DiscountGroupProduct
     * 
     * @param int $id Id of DiscountGroupProduct
     * @return object DiscountGroupProduct Object
     */
    public function getDiscountGroupProduct($id)
    {
        return $this->callApi("DiscountGroupProduct_GetById", ["DiscountGroupProductId" => $id]);
    }

    /**
     * Get indicated DiscountGroupProduct
     * 
     * @param string $title The title of the DiscountGroupProduct
     * @param DiscountGroupProductTitle
     * @return object of DiscountGroupProduct
     */
    public function getDiscountGroupProductByTitle($discountGroupProductTitle, $discountGroupProductId)
    {
        return $this->callApi("DiscountGroupProduct_GetByTitle", ["DiscountGroupProductTitle" => $discountGroupProductTitle, "DiscountGroupProductId" => $discountGroupProductId]);
    }

    /**
     * Updates a DiscountGroupProduct
     * 
     * @param object $discountGroupProductData, input in DiscountGroupProductUpdate Object format
     * @return int Id of the DiscountGroupProduct
     */
    public function updateDiscountGroupProduct($discountGroupProduct)
    {
        return $this->callApi("DiscountGroupProduct_Update", ["DiscountGroupProductData" => $discountGroupProduct]);
    }

    /**
     * Creates a new DiscountGroup
     * 
     * @param object $discountGroup input in DiscountGroupCreate Object format
     * @return int Id of the newly created DiscountGroup
     */
    public function createDiscountGroup($discountGroup)
    {
        return $this->callApi("DiscountGroup_Create", ["DiscountGroupData" => $discountGroup]);
    }

    /**
     * Deletes a DiscountGroup
     * 
     * @param int $id Id of the DiscountGroup to delete
     * @return mixed
     */
    public function deleteDiscountGroup($id)
    {
        return $this->callApi("DiscountGroup_Delete", ["DiscountGroupId" => $id]);
    }

    /**
     * Retrieves all DiscountGroupProducts
     * 
     * @return array of DiscountGroupProduct Objects
     */
    public function getDiscountGroups()
    {
        $discountGroups = $this->callApi("DiscountGroup_GetAll");
        $return = [];
        foreach($discountGroups as $discount){
            $return[] = new DiscountGroupProduct($discount);
        }
        return $return;
    }

    /**
     * Returns the indicated DiscountGroupProduct
     * 
     * @param int $id Id of DiscountGroupProduct
     * @return object DiscountGroupProduct
     */
    public function getDiscountGroup($id)
    {
        return $this->callApi("DiscountGroup_GetById", ["DiscountGroupId" => $id]);
    }

    /**
     * Retrieves a DiscountGroup by Title
     * 
     * @param string $title The title of the DiscountGroup
     * @return object DiscountGroup
     */
    public function getDiscountGroupByTitle($title, $id)
    {
        return $this->callApi("DiscountGroup_GetByTitle", ["DiscountGroupTitle" => $title, "DiscountGroupId" => $id]);
    }

    /**
     * Updates a DiscountGroupProduct
     * 
     * @param object input in DiscountGroupUpdate Object format
     * @return int Id of the discountGroup
     */
    public function updateDiscountGroup($discountGroup)
    {
        return $this->callApi("DiscountGroup_Update", ["DiscountGroupData" => $discountGroup]);
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
     * Retrieves all discounts.
     * 
     * @return array Discount Objects
     */
    public function getDiscounts(){
        $discounts = [];
        $response = $this->callApi('Discount_GetAll');
        foreach($response as $discount){
            $discounts[] = new Discount($discount);
        }
        return $discounts;
    }

    /**
     * Returns the indicated Discount.
     * 
     * @param int Id of Discount
     * @return object Discount Object
     */
    public function getDiscount($id)
    {
        $discount = $this->callApi("Discount_GetById", ["DiscountId" => $id]);
        return new Discount($discount);
    }

    /**
     * Updates a Discount.
     * 
     * @param object nput in DiscountUpdate Object format
     * @return int Id of the Discount
     */
    public function updateDiscount($discount)
    { 
        return $this->callApi("Discount_Update", ["DiscountData" => $discount]);
    }

    /**
     * Creates discount.
     * 
     * @param object $discount Discount object, corresponding to smartweb Discount_Create schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/DiscountCreate.html
     * @return int Id of the created discount.
     */
    public function createDiscount($discount)
    {
        return $this->callApi('Discount_Create',['DiscountData' =>$discount]);
    }

    /**
     * Deletes a Discount
     *
     * @param int $id Id of the Discount you wish to delete.
     * @return mixed
     */
    public function deleteDiscount($id){
        return $this->callApi('Discount_Delete',['DiscountId' =>$id]);
    }

    /**
     * Creates a new NewsletterCustomField
     *
     * @param object The input in NewsletterCustomFieldCreate Object format
     * @return int The id of the newly created NewsletterCustomField
     */
    public function createNewsletterCustomField($customField)
    {
        return $this->callApi("Newsletter_CreateCustomField", ["CustomFieldData" => $customField]);
    }

    /**
     * Deletes a new NewsletterCustomField
     *
     * @param int The Id of the NewsletterCustomField to delete
     */
    public function deleteNewsletterCustomField($id)
    {
        return $this->callApi("Newsletter_DeleteCustomField", ["CustomFieldId" => $id]);
    }

    /**
     * Retrieve NewsletterCustomField by Group and Type (1: Mailmarketing)
     *
     * @param int $userGroupId The id of the UserGroup
     * @param int $serviceId The id of the mail service
     * @return array An array of NewsletterCustomField Objects
     */
    public function getNewsletterCustomFieldByGroup($userGroupId, $serviceId)
    {
        $customFields = $this->callApi("Newsletter_GetCustomFieldByGroup", ["UserGroupId" => $userGroupId, "ServiceId" => $serviceId]);
        $return = [];
        foreach($customFields as $customField){
            $return[] = new NewsletterCustomField($customField);
        }
        return $return;
    }

    /**
     * Returns the indicated NewsletterCustomField
     * 
     * @param int $id The id of the NewsletterCustomField
     * @param int $id The id of the mail service
     * @return object NewsletterCustomField object
     */
    public function getNewsletterCustomField($id, $serviceId)
    {
        $customField = $this->callApi("Newsletter_GetCustomFieldById", ["CustomFieldId" => $id, "ServiceId" => $id]);
        return new NewsletterCustomField($customField);
    }

    /**
     * Updates NewsletterCustomField
     * 
     * @param NewsletterCustomFieldUpdate
     * @return int $id The id of the NewsletterCustomField
     */
    public function updateNewsletterCustomField($customField)
    {
        return $this->callApi("Newsletter_UpdateCustomField", ["CustomFieldData" => $customField]);
    }

    /**
     * Cancels a transaction in the paymentgateway
     * 
     * @param string $transactionCode The transactioncode of the transaction to cancel
     * @param int Id of the Order of the transaction 
     * @return boolean the status from the payment gateway, ok if success
     */
    public function cancelOrderTransaction($transactionCode, $orderId)
    {
        return $this->callApi("Order_CancelTransaction", ["TransactionCode" => $transactionCode, "OrderId" => $orderId]);
    }

    /**
     * Completes a transaction in the paymentgateway
     * 
     * @param string $transactionCode Transactioncode of the transaction to complete
     * @param int $orderId Id of the Order of the transaction
     * @return boolean The status from the payment gateway, ok if success
     */
    public function completeOrderTransaction($transactionCode, $orderId)
    {
        return $this->callApi("Order_CompleteTransaction", ["TransactionCode" => $transactionCode, "OrderId" => $orderId]);
    }

    /**
     * Creates a new Order
     * @param OrderCreate $order Object format
     * @return int Id of the newly create Order
     */
    public function createOrder($order){
        return $this->callApi('Order_Create',['OrderData' =>$order]);
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
     * Returns information about orders. The output format can be set with Order_SetFields
     * 
     * @param int $orderId Id of the order to retrieve
     * @return object The order object.
     */
    public function getOrderByNumber($start, $end){
        $orders = $this->callApi('Order_GetByNumber',array('Start' =>$start, "End" => $end));
        return new Order($order);
    }

    /**
     * Returns information about orders. The output format can be set with Order_SetFields
     * 
     * @param int The site of the order
     * @return array [] of Order objects
     */
    public function getOrderBySite($site)
    {
        $orders = [];
        $response = $this->callApi("Order_GetBySite", ["Site" => $site]);
        foreach($response as $order){
            $orders[] = new Order($order);
        }
        return $orders;
    }

    /**
     * Returns information about orders. The output format can be set with Order_SetFields
     * 
     * @param string A comma separated list of Status ids (0: Not Received, 1: Order Received, 2: Processing, 3: Order Sent, 4: Reopen, 5: Cancelled, 6: Ready for pickup, 7: Partially sent, 8: Picked up, 99: Draft, 100: Credit note)
     * @return array [] of Order objects
     */
    public function getOrderByStatus($status)
    {
        $orders = [];
        $response = $this->callApi("Order_GetByStatus", ["Status" => $status]);
        foreach($response as $order){
            $orders[] = new Order($order);
        }
        return $orders;
    }

    /**
     * Returns the indicated OrderCurrency
     *
     * @param int The id of the wanted Currency
     * @return OrderCurrency Object
     */
    public function getOrderCurrency($currencyId)
    {
        $response = $this->callApi("Order_GetCurrency", ["CurrencyId" => $currencyId]);
        return new OrderCurrency($response);
    }

    /**
     * Retrieves a customer
     *
     * @param int $CustomerId Id of the Customer
     * @return OrderCustomer The orderCustomer object.
     */
    public function getCustomer($CustomerId): OrderCustomer
    {
        $customer = $this->callApi('Order_GetCustomer',array('CustomerId' =>$CustomerId));
        return new OrderCustomer($customer);
    }

    /**
     * Retrieves OrderDelivery
     *
     * @param int $delivery_id The id of the wanted Delivery
     * @return OrderDelivery The OrderDelivery object.
     */
    public function getOrderDelivery($delivery_id)
    {
        return $this->callApi('Order_GetDelivery', ['DeliveryId' =>$delivery_id]);
    }

    /**
     * Returns the OrderDiscountCodes for the indicated Order
     *
     * @param int $orderId The id of the Order of the DiscountCodes
     * @return array of OrderDiscountCode Objects
     */
    public function getOrderDiscountCodes($orderId)
    {
        return $this->callApi('Order_GetDiscountCodes', ['OrderId' =>$orderId]);
    }

    /**
     * Returns the indicated OrderFileDownload
     *
     * @param int The id of the wanted OrderFileDownload
     * @return OrderFileDownload
     */
    public function getOrderFileDownload($id)
    {
        $response = $this->callApi('Order_GetFileDownload', ['FileDownloadId' =>$id]);
        return new OrderFileDownload($response);
    }
    
    /**
     * Returns the OrderLineAddresses of the indicated OrderLine.
     * @param int The  id of the OrderLine of the Order
     * @return array of OrderLineAddress Objects
     */
    public function getOrderLineAddresses($orderline_id)
    {
        $addresses = [];
        $response = $this->callApi('Order_GetLineAddresses', ['OrderLineId' =>$orderline_id]);
        foreach($response as $address){
            $addresses[] = new OrderLineAddress($address);
        }
        return $addresses;
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
     * Creates a new Order Transaction
     *
     * @param OrderSetTransactionCode
     * @return int The id of the newly create Order Transaction
     */
    public function setTransaction($transaction_obj){
        return $this->callApi('Order_SetTransactionCode', ['TransactionData' =>$transaction_obj]);
    }
    
    /**
     * Returns the indicated OrderPacking
     *
     * @param int The id of the OrderPacking
     * @return OrderPacking
     */
    public function getOrderPacking($id){
        $packing = $this->callApi('Order_GetPacking', ["PackingId" => $id]);
        return new OrderPacking($packing);
    }

    /**
     * Returns the indicated OrderPayment
     *
     * @param int The id of the OrderPayment
     * @return OrderPayment
     */
    public function getOrderPayment($id){
        $payment = $this->callApi('Order_GetPayment', ["PaymentId" => $id]);
        return new OrderPayment($packing);
    }

    /**
     * Returns the indicated OrderTransactions by Order
     *
     * @param int The id of the Order of the OrderTransactions
     * @return array of OrderTransaction objects
     */
    public function getOrderTransactions($id){
        $transactions = [];
        $response = $this->callApi('Order_GetTransactions', ["PaymentId" => $id]);
        foreach($response as $transaction){
            $transactions[] = new OrderTransaction($transaction);
        }
        return $transactions;
    }

    /**
     * Lowers a transaction in the payment gateway
     *
     * @param string $transactionCode the transactioncode of the transaction to cancel
     * @param int $amount The new amount of the translation in 1/100 of the currency. That is, and Amount of 150 equals 1.50
     * @param int $orderId the id of the Order of the transaction
     * @return mixed
     */
    public function lowerOrderTransaction($transactionCode, $amount, $orderId){
        return $this->callApi('Order_LowerTransaction', ['TransactionCode' => $transactionCode, "Amount" => $amount, "OrderId" => $orderId]);
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
     * Updates the service type of an order line
     * 
     * @param int $orderlineId The id of the orderline to update
     * @param string $serviceType The id of the new ServiceType of the orderline
     */

    public function updateOrderLineServiceType($orderlineId, $serviceType)
    {
        return $this->callApi('Order_UpdateLineServiceType', ['OrderLineId' => $orderlineId, 'ServiceType' => $serviceType]);
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
     * Updates the status of an order line
     *
     * @param int $orderlineId The id of the orderline to update
     * @param int $status The new status of the orderline
     * @return mixed
     */
    public function updateOrderLineTrackingCode($orderlineId, $trackingCode){
        return $this->callApi('Order_UpdateLineTrackingCode', ['OrderLineId' =>$orderlineId, 'TrackingCode' =>$trackingCode]);
    }

    /**
     * Updates the trackingcode of an order line
     * 
     * @param int The id of the orderline to update
     * @param string The id of the new ServiceType of the Order
     */
    public function updateOrderServiceType($orderId, $serviceType){
        return $this->callApi('Order_UpdateServiceType', ['OrderId' => $orderId, 'ServiceType' => $serviceType]);
    }

    /**
     * Updates the trackingcode of an order line
     * 
     * @param int The id of the Order to update
     * @param int $site The new site of the Order
     */
    public function updateOrderSite($orderId, $site){
        return $this->callApi('Order_UpdateSite', ['OrderId' => $orderId, 'Site' => $site]);
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
     * Updates the order tracking code
     *
     * @param int $orderId The id of the Order to update
     * @param int $status The new status of the Order
     * @return mixed
     */
    public function updateOrderTrackingCode($orderId, $trackingCode){
        return $this->callApi('Order_UpdateTrackingCode', ['OrderId' =>$orderId, 'TrackingCode' =>$trackingCode]);
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
     * Creates PageText.
     * 
     * @param object $pageText PageText object, corresponding to smartweb PageTextCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/PageTextCreate.html
     * @return int Id of the created pageText.
     */
    public function createPageText($pageText)
    {
        return $this->callApi('PageText_Create',['PageTextData' =>$pageText]);
    }
        
    /**
     * Deletes a PageText
     *
     * @param int $id Id of the PageText you wish to delete.
     * @return mixed
     */
    public function deletePageText($id){
        return $this->callApi('PageText_Delete',['PageTextId' =>$id]);
    }

    public function getPageTextByFolder($folderId){
        $pagetexts = [];
        $response = $this->callApi('PageText_GetByFolder', ["FolderId" => $folderId]);
        foreach($response as $pagetext){
            $pagetexts[] = new PageText($pagetext);
        }
        return $pagetexts;
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
     * Returns the indicated PageTexts. The output format can be set with PageText_SetFields
     * 
     * @param string $ids A comma separated list of ids.
     * @return array of PageText Objects
     */
    public function getPageTextByIds($ids){
        $pagetexts = [];
        $response = $this->callApi('PageText_GetByIds', ["PageTextIds" => $ids]);
        foreach($response as $pagetext){
            $pagetexts[] = new PageText($pagetext);
        }
        return $pagetexts;
    }

    /**
     * Returns the indicated PageTexts. The output format can be set with PageText_SetFields
     * 
     * @param string $pageTextLink
     * @return PageText
     */
    public function getPageTextByLink($pageTextLink){
        $pagetext = $this->callApi('PageText_GetByLink', ["PageTextLink" => $pageTextLink]);
        return new PageText($pagetext);
    }

    /**
     * Sets the format for the thumbnails returned in PageText objects
     * 
     * @param int $width the width of the thumbnail
     * @param int $height the height of the thumbnail
     * @param int $crop wether or not to crop
     * @param int $greyScale wether or not to greyscale
     * @param int $watermark wether or not to watermark the thumbnail
     * @return mixed
     */
    public function setPageTextThumbOptions($width, $height, $crop, $greyScale, $watermark){
        return $this->callApi('PageText_SetThumbOptions', ['ThumbWidth' => $width, "ThumbHeight" => $height, "Crop" => $crop, "Greyscale" => $greyScale, "Watermark" => $watermark]);
    }

    /**
     * Updates a PageText
     * 
     * @param PageText
     * @return int The id of the updated PageText
     */
    public function updatePageText($pageText)
    {
        return $this->callApi("PageText_Update", ["PageTextData" => $pageText]);
    }

    /**
     * Updates a PageText
     * 
     * @param int $id The id of the PagePicture to delete
     * @return mixed
     */
    public function deletePagePicture($id)
    {
        return $this->callApi("Page_DeletePicture", ["PagePictureId" => $id]);
    }

    /**
     * Returns the PagePictures of the indicated Page.
     * 
     * @param int $id The id of the PagePicture to delete
     * @return [] PagePicture Objects
     */
    public function getPagePictures($id){
        $pictures = [];
        $response = $this->callApi('Page_GetPictures', ["PageId" => $id]);
        foreach($response as $picture){
            $pictures[] = new PagePicture($picture);
        }
        return $pictures;
    }

    /**
     * Returns the PagePictures of the indicated Page.
     * 
     * @param int $pageId the id of the Page of the PagePictures
     * @param int $width the width of the thumbnail
     * @param int $height the height of the thumbnail
     * @param int $crop wether or not to crop
     * @param int $greyScale wether or not to greyscale
     * @param int $watermark wether or not to watermark the thumbnail
     * @return [] PagePicture Objects
     */
    public function getPagePictureThumbnails($pageId, $width, $height, $crop, $greyScale, $watermark){
        $pictures = [];
        $response = $this->callApi('Page_GetPictureThumbnails', ["PageId" => $pageId, "ThumbWidth" => $width, "ThumbHeight" => $height, "Crop" => $crop, "Greyscale" => $greyScale, "Watermark" => $watermark]);
        foreach($response as $picture){
            $pictures[] = new PagePicture($picture);
        }
        return $pictures;
    }

    /**
     * Returns all available PaymentMethods.
     * 
     * @return [] PaymentMethod Objects
     */
    public function getPaymentMethods(){
        $paymentMethods = $this->callApi('Payment_GetAll');
        $return = [];
        foreach($paymentMethods as $method){
            $return[] = new PaymentMethod($method);
        }
        return $return;
    }

    /**
     * Returns the available PaymentMethods by CountryCode
     * 
     * @param string $countryCode
     * @return [] PaymentMethod Objects
     */
    public function getPaymentMethodsByCountryCode($countryCode){
        $payments = [];
        $response = $this->callApi('Payment_GetByCountry', ["CountryCode" => $countryCode]);
        foreach($response as $payment){
            $payments[] = new PaymentMethod($payment);
        }
        return $payments;
    }

    /**
     * Adds an existing ProductAdditionalType to the indicated Product
     * 
     * @param int $productId The id of the Product to add the ProductAdditionalType to
     * @param int $typeId The id of the ProductAdditionalType
     * @return mixed
     */
    public function addProductAdditionalType($productId, $typeId){
        return $this->callApi('Product_AddAdditionalType', ["ProductId" => $productId, "AdditionalTypeId" => $typeId]);
    }

    /**
     * Adds an existing ProductCostumData to the indicated Product
     * 
     * @param int $productId The id of the Product to add the ProductCustomData to
     * @param int $customDataId The id of the ProductCustomData
     * @return mixed
     */
    public function addProductCustomData($productId, $customDataId){
        return $this->callApi('Product_AddCustomData', ["ProductId" => $productId, "CustomDataId" => $customDataId]);
    }

    /**
     * Creates product.
     * 
     * @param object $product Product object, corresponding to smartweb ProductCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/ProductCreate.html
     * @return int Id of the created product.
     */
    public function createProduct($product)
    {
        return $this->callApi('Product_Create',['ProductData' =>$product]);
    }

    /**
     * Creates a new ProductCustomData
     * 
     * @param ProductCustomData
     * @return int The id of the newly created ProductCustomData
     */
    public function createProductCustomData($customData){
        return $this->callApi('Product_CreateCustomData', ["ProductCustomData" => $customData]);
    }

    /**
     * Creates a new ProductCustomDataType
     * 
     * @param ProductCustomDataTypeCreate
     * @param ProductCustomDataType
     * @return int The id of the newly created ProductCustomDataType
     */
    public function createProductCustomDataType($customDataType, $customData){
        return $this->callApi('Product_CreateCustomDataType', ["ProductCustomData" => $customDataType, "ProductCustomDataType" => $customDataType]);
    }

    /**
     * Creates a new ProductDeliveryCountry
     * 
     * @param ProductDeliveryCountry
     * @return int The id of the newly created ProductDeliveryCountry
     */
    public function createProductDeliveryCountry($deliveryCountry){
        return $this->callApi('Product_CreateDeliveryCountry', ["DeliveryCountryData" => $deliveryCountry]);
    }

    /**
     * Creates a new ProductDeliveryTime
     * 
     * @param ProductDeliveryTime
     * @return int The id of the newly created ProductDeliveryTime
     */
    public function createProductDeliveryTime($deliveryTime){
        return $this->callApi('Product_CreateDeliveryTime', ["DeliveryTimeData" => $deliveryTime]);
    }

    /**
     * Creates a new ProductDiscountAccumulative
     * 
     * @param ProductDiscountAccumulative
     * @return int The id of the newly created ProductDiscountAccumulative
     */
    public function createProductDiscountAccumulative($discount){
        return $this->callApi('Product_CreateDiscountAccumulative', ["ProductDiscountAccumulativeData" => $discount]);
    }

    /**
     * Creates a new ProductDiscount
     * 
     * @param ProductDiscount
     * @return int The id of the newly created ProductDiscount
     */
    public function createProductDiscount($discount){
        return $this->callApi('Product_CreateDiscount', ["ProductDiscountData" => $discount]);
    }

    /**
     * Creates a new ProductExtraBuyCategory
     * 
     * @param ProductExtraBuyCategory
     * @return int The id of the newly created ProductExtraBuyCategory
     */
    public function createProductExtraBuyCategory($category){
        return $this->callApi('Product_CreateExtraBuyCategory', ["ExtraBuyCategoryData" => $category]);
    }

    /**
     * Creates a new ProductExtraBuyRelation
     * 
     * @param ProductExtraBuyRelation
     * @return int The id of the newly created ProductExtraBuyRelation
     */
    public function createProductExtraBuyRelation($relation){
        return $this->callApi('Product_CreateExtraBuyRelation', ["ExtraBuyRelationData" => $relation]);
    }

    /**
     * Creates a new ProductFile
     * 
     * @param ProductFile
     * @return int The id of the newly created ProductFile
     */
    public function createProductFile($file){
        return $this->callApi('Product_CreateFile', ["FileData" => $file]);
    }

    /**
     * Creates product.
     * 
     * @param object $product Product object, corresponding to smartweb ProductCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/ProductCreate.html
     * @return int Id of the created product.
     */
    public function createOrUpdateProduct($product)
    {
        return $this->callApi('Product_CreateOrUpdate',['ProductData' =>$product]);
    }

    /**
     * Creates or Updates a ProductVariant using its ItemNumber as key. Assumes that only unique ItemNumbers exist in the shop. If the Itemnumber supplied is not found a new ProductVariant is created
     * 
     * @param ProductVariant
     * @return int The id of the newly created or updated ProductVariant
     */
    public function createOrUpdateProductVariant($variant)
    {
        return $this->callApi('Product_CreateOrUpdateVariant',['VariantData' =>$variant]);
    }

    /**
     * Creates or Updates a ProductVariantType using Title as key. Assumes that only unique ProductVariantType Titles in a given language exist in the shop. If the Title supplied is not found a new ProductVariantType is created
     * 
     * @param ProductVariantType
     * @return int The id of the created or updated ProductVariantType
     */
    public function createOrUpdateProductVariantType($variantType)
    {
        return $this->callApi('Product_CreateOrUpdateVariantType',['VariantTypeData' =>$variantType]);
    }

    /**
     * Creates or Updates a ProductVariantTypeValue using Title and ProductVariantType id as keys. If the Title supplied is not found a new ProductVariantTypeValue is created
     * 
     * @param ProductVariantTypeValue
     * @return int The id of the created or updated ProductVariantTypeValue
     */
    public function createOrUpdateProductVariantTypeValue($variantTypeValue)
    {
        return $this->callApi('Product_CreateOrUpdateVariantTypeValue',['VariantTypeValueData' =>$variantTypeValue]);
    }

    /**
     * Creates a new ProductPicture
     * 
     * @param ProductPicture
     * @return int The id of the created ProductPicture
     */
    public function createProductPicture($picture)
    {
        return $this->callApi('Product_CreatePicture',['PictureData' =>$picture]);
    }

    /**
     * Creates a new ProductUnit
     * 
     * @param ProductUnit
     * @return int The id of the created ProductUnit
     */
    public function createProductUnit($unit)
    {
        return $this->callApi('Product_CreateUnit',['ProductUnitData' =>$unit]);
    }

     /**
     * Creates a new ProductVariant
     * 
     * @param ProductVariant
     * @return int The id of the created ProductVariant
     */
    public function createProductVariant($variant)
    {
        return $this->callApi('Product_CreateVariant',['VariantData' =>$variant]);
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
     * Deletes a ProductAdditional
     *
     * @param int $id Id of the ProductAdditional you wish to delete.
     * @return mixed
     */
    public function deleteProductAdditional($id){
        return $this->callApi('Product_DeleteAdditional',['ProductAdditionalId' =>$id]);
    }

    /**
     * Deletes all ProductDiscountAccumulatives of a DiscountGroupProduct
     *
     * @param string $discountGroupProductTitle The Title of the DiscountGroupProduct
     * @return mixed
     */
    public function deleteAllProductDiscountAccumulativeByProductGroup($discountGroupProductTitle){
        return $this->callApi('Product_DeleteAllDiscountAccumulativeByProductGroup',['DiscountGroupProductTitle' =>$discountGroupProductTitle]);
    }

    /**
     * Deletes all ProductDiscountAccumulatives of a Product
     *
     * @param string $productItemNumber The Itemnumber of the Product
     * @return mixed
     */
    public function deleteAllProductDiscountAccumulatives($productItemNumber){
        return $this->callApi('Product_DeleteAllDiscountAccumulatives',['ProductItemNumber' => $productItemNumber]);
    }

    /**
     * Deletes all ProductDiscounts of a Product
     *
     * @param string $productItemNumber The Itemnumber of the Product
     * @return mixed
     */
    public function deleteAllProductDiscounts($productItemNumber){
        return $this->callApi('Product_DeleteAllDiscounts',['ProductItemNumber' => $productItemNumber]);
    }

    /**
     * Deletes a ProductCustomData
     *
     * @param int $id The id of the ProductCustomData to delete
     * @return mixed
     */
    public function deleteProductCustomData($id){
        return $this->callApi('Product_DeleteCustomData',['ProductCustomDataId' => $id]);
    }

    /**
     * Deletes a ProductCustomDataType
     *
     * @param int $id The id of the ProductCustomDataType to delete
     * @return mixed
     */
    public function deleteProductCustomDataType($id){
        return $this->callApi('Product_DeleteCustomDataType',['ProductCustomDataTypeId' => $id]);
    }

    /**
     * Deletes a ProductDeliveryCountry
     *
     * @param int $id The id of the ProductDeliveryCountry to delete
     * @return mixed
     */
    public function deleteProductDeliveryCountry($id){
        return $this->callApi('Product_DeleteDeliveryCountry',['ProductDeliveryCountryId' => $id]);
    }

    /**
     * Deletes a ProductDeliveryTime
     *
     * @param int $id The id of the ProductDeliveryTime to delete
     * @return mixed
     */
    public function deleteProductDeliveryTime($id){
        return $this->callApi('Product_DeleteDeliveryTime',['ProductDeliveryTimeId' => $id]);
    }

    /**
     * Deletes a ProductDiscount
     *
     * @param int $id The id of the ProductDiscount to delete
     * @return mixed
     */
    public function deleteProductDiscount($id){
        return $this->callApi('Product_DeleteDiscount',['ProductDiscountId' => $id]);
    }

    /**
     * Deletes a ProductDiscountAccumulative
     *
     * @param int $id The id of the ProductDiscountAccumulative to delete
     * @return mixed
     */
    public function deleteProductDiscountAccumulative($id){
        return $this->callApi("Product_DeleteDiscountAccumulative",["ProductDiscountAccumulativeId" => $id]);
    }

    /**
     * Deletes a ProductExtraBuyCategory
     *
     * @param int $id The id of the ProductExtraBuyCategory to delete
     * @return mixed
     */
    public function deleteProductExtraBuyCategory($id){
        return $this->callApi("Product_DeleteExtraBuyCategory",["ProductExtraBuyCategoryId" => $id]);
    }

    /**
     * Deletes a ProductExtraBuyRelation
     *
     * @param int $id The id of the ProductExtraBuyRelation to delete
     * @return mixed
     */
    public function deleteProductExtraBuyRelation($id){
        return $this->callApi("Product_DeleteExtraBuyRelation",["ProductExtraBuyRelationId" => $id]);
    }

    /**
     * Deletes a ProductPicture
     *
     * @param int $id The id of the ProductPicture to delete
     * @return mixed
     */
    public function deleteProductFile($fileId, $pictureId){
        return $this->callApi("Product_DeleteFile",["FileId" => $fileId, "PictureId" => $pictureId]);
    }

    /**
     * Deletes a ProductPicture
     *
     * @param int $id The id of the ProductPicture to delete
     * @return mixed
     */
    public function deleteProductPicture($pictureId){
        return $this->callApi("Product_DeletePicture",["PictureId" => $pictureId]);
    }

    /**
     * Deletes a ProductTag
     *
     * @param int $id The id of the ProductTag to delete
     * @return mixed
     */
    public function deleteProductTag($id){
        return $this->callApi("Product_DeleteTag",["ProductTagId" => $id]);
    }

    /**
     * Deletes a ProductUnit
     *
     * @param int $id The id of the ProductUnit to delete
     * @return mixed
     */
    public function deleteProductUnit($id){
        return $this->callApi("Product_DeleteUnit",["ProductUnitId" => $id]);
    }

    /**
     * Deletes a ProductVariant
     *
     * @param int $id The id of the ProductVariant to delete
     * @return mixed
     */
    public function deleteProductVariant($id){
        return $this->callApi("Product_DeleteVariant",["VariantId" => $id]);
    }

    /**
     * Returns the ProductAdditionals of the indicated ProductAdditionalType
     * 
     * @param int $id The id of the ProductAdditionalType of the wanted ProductAdditionals
     * @return [] ProductAdditional Objects
     */
    public function getProductAdditionals($id){

        $additionals = [];
        $response = $this->callApi("Product_GetAdditionals",["AdditionalTypeId" => $id]);
        foreach($response as $each){
            $additionals[] = new ProductAdditional($each);
        }
        return $additionals;
    }
    
    /**
     * Returns the ProductAdditionalTypes of the indicated Product
     * 
     * @param int $id The id of the Product of the wanted ProductAdditionalTypes
     * @return [] ProductAdditionalType Objects
     */
    public function getProductAdditionalTypes($productId){
        $types = [];
        $response = $this->callApi('Product_GetAdditionalTypes');
        foreach ($response as $each) {
            $types[] = new ProductAdditionalType($each);
        }
        return $types;
    }

    /**
     * Returns the ProductAdditionalTypes of the indicated Product
     * 
     * @param int $id The id of the Product of the wanted ProductAdditionalTypes
     * @return [] ProductAdditionalType Objects
     */
    public function getAllAdditionalTypes(){
        $types = [];
        $response = $this->callApi('Product_GetAdditionalTypesAll');
        foreach ($response as $type) {
            $types[] = new ProductAdditionalType($type);
        }
        return $types;
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
     * Retrieves all ProductExtraBuyCategories.
     * 
     * @return ProductExtraBuyCategory[] All the ProductExtraBuyCategory.
     */
    public function getAllProductsExtraBuyCategory(): array
    {
        $response = $this->callApi('Product_GetAllExtraBuyCategory');
        $return = [];
        foreach($response as $category){
            $return[] = new ProductExtraBuyCategory($category);
        }
        return $return;
    }

    /**
     * Equal to Product_GetAll, however it returns only $Length amount of products starting as index $Start. The output format can be set with Product_SetFields
     * 
     * @param $start
     * @param $length
     * @return [] of Product Object
     */
    public function getAllProductsWithLimit($start, $length)
    {
        return $this->callApi('Product_GetAllWithLimit', ["Start" => $start, "Length" => $length]);
    }

    /**
     * Returns the products with this brand. The output format can be set with Product_SetFields
     * 
     * @param int $brandId The id of the Brand of the wanted Products
     * @return [] of Product Object
     */
    public function getAllProductsByBrand($brandId)
    {
        $return = [];
        $products = $this->callApi('Product_GetByBrand', ["BrandId" => $brandId]);
        foreach($products as $product){
            $return[] = new Product($product);
        }
        return $return;
    }


    /**
     * Retrieves products that has a certain category as main category
     * 
     * @param int $category_id The id of the Category of the wanted Products
     * @return Product[] Returns the products with this category as their main category. The output format can be set with setFields.
     */
    public function getProductsByCategory(int $category_id): array
    {
        $products = $this->callApi('Product_getbycategory', ['CategoryId' =>$category_id]);
        $result = [];
        foreach ($products as $product) {
            $result[] = new Product($product);
        }
        return $result;
    }

    /**
     * Retrieves products that has a certain category as main or second category
     * 
     * @param int $category_id The id of the Category of the wanted Products
     * @return Product[] Returns the products with this category as their main or secondary category. The output format can be set with setFields.
     */
    public function getProductsByCategoryAndSecoundary(int $category_id): array
    {
        $products = $this->callApi('Product_GetByCategoryAndSecondary', ['CategoryId' =>$category_id]);
        $result = [];
        foreach ($products as $product) {
            $result[] = new Product($product);
        }
        return $result;
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
     * Returns the ProductCustomData of the indicated Product
     * 
     * @param int $productId The id of the Product of the wanted ProductCostumData's
     * @return [] of ProductCustomData Object
     */
    public function getAllCustomDataByProduct($productId){
        $customData = $this->callApi('Product_GetCustomData', ["ProductId" => $productId]);
        $return = [];
        foreach($customData as $custom){
            $return[] = new ProductCustomData($custom);
        }
        return $return;
    }

    /**
     * Returns all ProductCustomData of the solution
     * 
     * @return [] of ProductCustomData Object
     */
    public function getAllCustomData(){
        $customDatas = $this->callApi('Product_GetCustomDataAll');
        $return = [];
        foreach($customDatas as $custom){
            $return[] = new ProductCustomData($custom);
        }
        return $return;
    }

    /**
     * Returns the ProductCustomData of the indicated Product
     * 
     * @param int $id The id of the Product of the wanted ProductCostumData's
     * @return [] of ProductCustomData Object
     */
    public function getCustomData($id){
        $custom = $this->callApi('Product_GetCustomDataById', ["CustomDataId" => $id]);
        return new ProductCustomData($custom);
    }

    /**
     * Returns the ProductCustomDatas of the indicated ProductCustomDataType
     * 
     * @param int $type_id The id of ProductCostumDataType
     * @return [] of ProductCustomData Object
     */
    public function getCustomDataByType(int $type_id){
        return $this->callApi('Product_GetCustomDataByType', ['CustomDataTypeId' =>$type_id]);
    }

    /**
     * Returns the indicated ProductCustomDataType
     * 
     * @param int $type_id The id of the wanted ProductCustomDataType
     * @return ProductCustomDataType
     */
    public function getCustomDataType(int $type_id){
        return $this->callApi('Product_GetCustomDataType', ['CustomDataTypeId' =>$type_id]);
    }

    /**
     * Returns all ProductCustomDataTypes
     * 
     * @return [] of ProductCustomDataType Objects
     */
    public function getAllProductCustomDataType(){
        $return = [];
        $customDataTypes = $this->callApi('Product_GetCustomDataTypeAll');
        foreach($customDataTypes as $dataType){
            $return[] = new ProductCustomDataType($dataType);
        }
        return $return;
    }

    /**
     * Returns ProductDeliveryCountry
     * 
     * @param int $deliveryCountryId The id of the wanted ProductDeliveryCountry
     * @return ProductDeliveryCountry
     */
    public function getProductDeliveryCountry($deliveryCountryId){
        $deliveryCountry = $this->callApi('Product_GetDeliveryCountry', ["DeliveryCountryId" => $deliveryCountryId]);
        return new ProductDeliveryCountry($deliveryCountry);
    }

    /**
     * Returns all ProductDeliveryCountries
     * 
     * @return [] of ProductDeliveryCountry
     */ 
    public function getAllProductDeliveryCountry(){
        $return = [];
        $deliveryCountries = $this->callApi('Product_GetDeliveryCountryAll');
        foreach($deliveryCountries as $deliveryCountry){
            $return[] = new ProductDeliveryCountry($deliveryCountry);
        }
        return $return;
    }

    /**
     * Returns ProductDeliveryCountry by Code
     * 
     * @param int $deliveryCountryId The code of the wanted ProductDeliveryCountry
     * @param string $countryCode
     * @return ProductDeliveryCountry
     */ 
    public function getProductDeliveryCountryByCode($countryCode, $deliveryCountryId){
        $deliveryCountry = $this->callApi('Product_GetDeliveryCountryByCode', ["DeliveryCountryCode" => $countryCode, "DeliveryCountryId" => $deliveryCountryId]);
        return new ProductDeliveryCountry($deliveryCountry);
    }

    /**
     * Returns ProductDeliveryCountry by Iso
     * 
     * @param int $deliveryCountryId The iso of the wanted ProductDeliveryCountry
     * @param string $countryCode
     * @return ProductDeliveryCountry
     */ 
    public function getProductDeliveryCountryByIso($deliveryCountryId, $deliveryCountryIso){
        $deliveryCountry = $this->callApi('Product_GetDeliveryCountryByIso', ["DeliveryCountryId" => $deliveryCountryId, "DeliveryCountryIso" => $deliveryCountryIso]);
        return new ProductDeliveryCountry($deliveryCountry);
    }

    /**
     * Returns ProductDeliveryCountry by Iso
     * 
     * @param int $deliveryTimeId The id of the wanted ProductDeliveryTime
     * @return ProductDeliveryTime
     */ 
    public function getProductDeliveryTime($deliveryTimeId){
        $deliveryTime = $this->callApi('Product_GetDeliveryTime', ["DeliveryTimeId" => $deliveryTimeId]);
        return new ProductDeliveryTime($deliveryTime);
    }

    /**
     * Returns all ProductDeliveryTimes
     * 
     * @return [] of ProductDeliveryTime
     */ 
    public function getProductDeliveryTimes(){
        return $this->callApi('Product_GetDeliveryTimeAll');
    }

    /**
     * Returns ProductDiscount
     * 
     * @param int DiscountId the id of the ProductDiscount
     * @return ProductDiscount
     */ 
    public function getProductDiscount($discountId){
        return $this->callApi('Product_GetDiscount', ["DiscountId" => $discountId]);
    }

    /**
     * Returns ProductDiscountAccumulative
     * 
     * @param int DiscountId the id of the ProductDiscountAccumulative
     * @return ProductDiscountAccumulative
     */ 
    public function getProductDiscountAccumulative($discountId){
        return $this->callApi("Product_GetDiscountAccumulative", ["DiscountId" => $discountId]);
    }

    /**
     * Returns DiscountGroup
     * 
     * @param int id of the DiscountGroup
     * @return DiscountGroup
     */ 
    public function getProductDiscountGroup($discountGroupId){
        return $this->callApi("Product_GetDiscountGroup", ["DiscountGroupId" => $discountGroupId]);
    }

    /**
     * Returns ProductDiscounts of a product
     * 
     * @param int ProductId the id of the Product
     * @return [] of ProductDiscount
     */ 
    public function getProductDiscounts($productId)
    {
        $discounts = $this->callApi("Product_GetDiscounts", ["ProductId" => $productId]);
        $return = [];
        foreach($discounts as $discount){
            $return[] = new ProductDiscount($discount);
        }
        return $return;
    }

    /**
     * Returns the ProductDiscountAccumulatives of a Product
     * 
     * @param int ProductId the id of the Product
     * @return [] of ProductDiscountAccumulative
     */ 
    public function getProductDiscountsAccumulative($productId){
        $discounts = $this->callApi("Product_GetDiscountsAccumulative", ["ProductId" => $productId]);
        $return = [];
        foreach($discounts as $discount){
            $return[] = new ProductDiscountAccumulative($discount);
        }
        return $return;
    }

    /**
     * Returns all ProductDiscountAccumulatives
     * 
     * @return [] of ProductDiscountAccumulative
     */ 
    public function getAllProductDiscountsAccumulative(){
        $discounts = $this->callApi("Product_GetDiscountsAccumulativeAll");
        $return = [];
        foreach($discounts as $discount){
            $return[] = new ProductDiscountAccumulative($discount);
        }
        return $return;
    }

    /**
     * Returns the ProductDiscountAccumulatives of a DiscountGroupProduct
     * 
     * @param int DiscountGroupProductId the id of the DiscountGroupProduct
     * @return [] of ProductDiscountAccumulative
     */ 
    public function getAllProductDiscountsAccumulativeByProductGroup($discountGroupProductId){
        $discounts = $this->callApi("Product_GetDiscountsAccumulativeByProductGroup", ["DiscountGroupProductId" => $discountGroupProductId]);
        $return = [];
        foreach($discounts as $discount){
            $return[] = new ProductDiscountAccumulative($discount);
        }
        return $return;
    }

    /**
     * Returns the ProductDiscountAccumulatives of a User
     * 
     * @param int UserId the id of the User
     * @return [] of ProductDiscountAccumulative
     */ 
    public function getAllProductDiscountsAccumulativeByUser($userId){
        $discounts = $this->callApi("Product_GetDiscountsAccumulativeByUser", ["UserId" => $userId]);
        $return = [];
        foreach($discounts as $discount){
            $return[] = new ProductDiscountAccumulative($discount);
        }
        return $return;
    }

    /**
     * Returns the ProductDiscountAccumulatives of a UserDiscountGroup
     * 
     * @param int UserDiscountGroupId the id of the User
     * @return [] of ProductDiscountAccumulative
     */ 
    public function getAllProductDiscountsAccumulativeByUserGroup($userDiscountGroupId){
        $discounts = $this->callApi("Product_GetDiscountsAccumulativeByUserGroup", ["UserDiscountGroupId" => $userDiscountGroupId]);
        $return = [];
        foreach($discounts as $discount){
            $return[] = new ProductDiscountAccumulative($discount);
        }
        return $return;
    }

    /**
     * Returns the indicated ProductExtraBuyCategory
     * 
     * @param int The id of the wanted ExtraBuyCategory
     * @return ProductExtraBuyCategory
     */ 
    public function getProductExtraBuyCategory($extraBuyCategoryId){
        $category = $this->callApi("Product_GetExtraBuyCategory", ["ExtraBuyCategoryId" => $extraBuyCategoryId]);
        return new ProductExtraBuyCategory($category);
    }

    /**
     * Returns the ProductExtraBuyRelations of the indicated Product
     * 
     * @param int The id of the Product of the wanted ProductExtraBuyRelations
     * @return [] of ProductExtraBuyRelation
     */ 
    public function getProductExtraBuyRelations($productId){
        $relations = $this->callApi("Product_GetExtraBuyRelations", ["ProductId" => $productId]);
        $return = [];
        foreach($relations as $relation){
            $return[] = new ProductExtraBuyRelation($relation);
        }
        return $return;
    }

    /**
     * Returns the ProductFiles of the indicated Product
     * 
     * @param int The id of the Product of the wanted ProductFiles
     * @return [] of ProductFile
     */ 
    public function getProductFiles($productId){
        $files = $this->callApi("Product_GetFiles", ["ProductId" => $productId]);
        $return = [];
        foreach($files as $file){
            $return[] = new ProductFile($file);
        }
        return $return;
    }

    /**
     * Returns the ProductPictures of the indicated Product
     * 
     * @param int The id of the Product of the wanted ProductPictures
     * @return [] of ProductPicture
     */ 
    public function getProductPictures($productId){
        $pictures = $this->callApi("Product_GetPictures", ["ProductId" => $productId]);
        $return = [];
        foreach($pictures as $picture){
            $return[] = new ProductPicture($picture);
        }
        return $return;
    }

    /**
     * Returns the secondary Categories of the indicated Product
     * 
     * @param int $productId The id of the Product of the wanted Categories
     * @return [] of Category
     */ 
    public function getProductSecondaryCategories($productId){
        $categories = $this->callApi("Product_GetSecondaryCategories", ["ProductId" => $productId]);
        $return = [];
        foreach($categories as $category){
            $return[] = new Category($category);
        }
        return $return;
    }

    /**
     * Returns the ProductTags of the indicated Product
     * 
     * @param int $productId The id of the Product of the wanted ProductTag
     * @return [] of ProductTag
     */ 
    public function getProductTags($productId){
        $tags = $this->callApi("Product_GetTags", ["ProductId" => $productId]);
        $return = [];
        foreach($tags as $tag){
            $return[] = new ProductTag($tag);
        }
        return $return;
    }

    /**
     * Returns all ProductUnits
     * 
     * @param int $productId The id of the Product of the wanted ProductTag
     * @return [] of ProductUnit
     */ 
    public function getProductUnits($productId){
        $units = $this->callApi("Product_GetUnitAll");
        $return = [];
        foreach($units as $unit){
            $return[] = new ProductUnit($unit);
        }
        return $return;
    }

    /**
     * Returns ProductUnit by Id
     * 
     * @param int $productUnitId The id of the ProductUnit
     * @return ProductUnit
     */ 
    public function getProductUnit($productUnitId){
        $unit = $this->callApi("Product_GetUnitById",["ProductUnitId" => $productUnitId]);
        return new ProductUnit($unit);
    }

    /**
     * Returns ProductVariant by Id
     * 
     * @param int $variantId The id of the ProductVariant
     * @return ProductVariant
     */ 
    public function getProductVariant($variantId){
        $variant = $this->callApi("Product_GetVariantById",["VariantId" => $variantId]);
        return new ProductVariant($variant);
    }

    /**
     * Retrieve all product variants
     *
     * @param int $product_id
     * @return [] of ProductVariant Object
     */
    public function getProductVariants(int $product_id){
        return $this->callApi('Product_GetVariants', ['ProductId' => $product_id]);
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
     * Returns the ProductVariants of the indicated Product in Sorted order
     *
     * @param int $productId Id of the product to retrieve.
     * @return [] of ProductVariant Objects
     */
    public function getProductVariantSorted($productId){
        $variants = $this->callApi('Product_GetVariantsSorted',array('ProductId' =>$productId));
        $return = [];
        foreach($variants as $variant){
            $return[] = new ProductVariant($variant);
        }
        return $return;
    }

    /**
     * Returns ProductVariantType
     *
     * @param int $variantTypeId The id of the wanted ProductVariantType
     * @return ProductVariantType
     */
    public function getProductVariantType($variantTypeId){
        $variantType = $this->callApi('Product_GetVariantType',array('VariantTypeId' =>$variantTypeId));
        return new ProductVariantType($variantType);
    }

    /**
     * Returns all ProductVariantTypes of the solution
     *
     * @return [] of ProductVariantType Objects
     */
    public function getAllProductVariantTypes(){
        $variantTypes = $this->callApi('Product_GetVariantTypeAll');
        $return = [];
        foreach($variantTypes as $variant){
            $return[] = new ProductVariantType($variant);
        }
        return $return;
    }

    /**
     * Returns ProductVariantTypeValue
     *
     * @param int $typeValueId The id of the ProductVariantTypeValue
     * @return ProductVariantTypeValue
     */
    public function getProductVariantTypeValue($typeValueId){
        $variantType = $this->callApi('Product_GetVariantTypeValue',array('TypeValueId' =>$typeValueId));
        return new ProductVariantTypeValue($variantType);
    }

    /**
     * Returns the ProductVariantTypeValues of the indicated Variant
     *
     * @param int $variantId The id of the ProductVariant of the wanted ProductVariantTypeValues
     * @return [] of ProductVariantTypeValue Objects
     */
    public function getAllProductVariantTypeValues($variantId){
        $variants = $this->callApi('Product_GetVariantTypeValues',array('VariantId' =>$variantId));
        $return = [];
        foreach($variants as $variant){
            $return[] = new ProductVariantTypeValue($variant);
        }
        return $return;
    }

    /**
     * Returns the ProductVariantTypeValues of the indicated ProductVariantType
     *
     * @param int $variantTypeId The id of the ProductVariantType
     * @return [] of ProductVariantTypeValue Objects
     */
    public function getAllProductVariantTypeValuesByType($variantTypeId){
        $variants = $this->callApi('Product_GetVariantTypeValuesByType',array('VariantTypeId' =>$variantTypeId));
        $return = [];
        foreach($variants as $variant){
            $return[] = new ProductVariantTypeValue($variant);
        }
        return $return;
    }

    /**
     * Removes a ProductAdditionalType from the indicated Product
     *
     * @param int $productId The id of the Product to remove the ProductAdditionalType from
     * @param int $additionalTypeId The id of the ProductAdditionalType
     */
    public function removeProductAdditionalType($productId, $additionalTypeId){
        return $this->callApi('Product_RemoveAdditionalType',array('ProductId' =>$productId, "AdditionalTypeId" => $additionalTypeId));
    }

    /**
     * Removes a ProductCostumData from the indicated Product
     *
     * @param int $productId The id of the Product to remove the ProductAdditionalType from
     * @param int $customDataId The id of the ProductCustomData
     */
    public function removeProductCustomData($productId, $customDataId){
        return $this->callApi('Product_RemoveCustomData',array('ProductId' =>$productId, "CustomDataId" => $customDataId));
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
     * Updates a product custom data.
     *
     * @param object $product Product object, should include either Id or ItemNumber prop.
     * @return int The id of the updated ProductCustomData
     */
    public function updateProductCustomData($productCustomData){
        return $this->callApi('Product_UpdateCustomData',['ProductCustomData' =>$productCustomData]);
    }

    /**
     * Updates a product custom data type.
     *
     * @param object $product Product object, should include either Id or ItemNumber prop.
     * @return int The id  of the updated ProductCustomDataType
     */
    public function updateProductCustomDataType($productCustomDataType, $productCustomData){
        return $this->callApi('Product_UpdateCustomDataType',['ProductCustomDataType' =>$productCustomDataType, 'ProductCustomData' =>$productCustomData]);
    }

    /**
     * Updates the text of a 'textype' ProductCostumData for the indicated Product
     *
     * @param int $productId The id of the Product for which to update the ProductCustomData
     * @param int $customDataTypeId The id of the ProductCustomDateType of the ProductCustomData Object
     * @param string $text The new text of the ProductCustomData
     * @return int The id  of the updated ProductCustomDataType
     */
    public function updateProductCustomTextData($productId, $customDataTypeId, $text){
        return $this->callApi('Product_UpdateCustomTextData',['ProductId' =>$productId, 'CustomDataTypeId' =>$customDataTypeId, "Text" => $text]);
    }

    /**
     * Updates a ProductDeliveryCountry
     *
     * @param ProductDeliveryCountry
     * @return int The id of the ProductDeliveryCountry
     */
    public function updateProductDeliveryCountry($deliveryCountry){
        return $this->callApi('Product_UpdateDeliveryCountry',['DeliveryCountryData' =>$deliveryCountry]);
    }

    /**
     * Updates a ProductDeliveryTime
     *
     * @param ProductDeliveryTime
     * @return int The id of the ProductDeliveryTime
     */
    public function updateProductDeliveryTime($deliveryTime){
        return $this->callApi('Product_UpdateDeliveryTime',['DeliveryTimeData' =>$deliveryTime]);
    }

    /**
     * Updates a ProductDiscount
     *
     * @param ProductDiscount
     * @return int The id of the ProductDiscount
     */
    public function updateProductDiscount($discount){
        return $this->callApi('Product_UpdateDiscount',['ProductDiscountData' =>$discount]);
    }

    /**
     * Updates a ProductDiscountAccumulative
     *
     * @param ProductDiscountAccumulative
     * @return int The id of the ProductDiscountAccumulative
     */
    public function updateProductDiscountAccumulative($discountAccumulative){
        return $this->callApi('Product_UpdateDiscountAccumulative',['ProductDiscountAccumulativeData' =>$discountAccumulative]);
    }

    /**
     * Updates a ProductExtraBuyCategory
     *
     * @param ProductExtraBuyCategory
     * @return int The id of the ProductExtraBuyCategory
     */
    public function updateProductExtraBuyCategory($extraBuyCategory){
        return $this->callApi('Product_UpdateExtraBuyCategory',['ExtraBuyCategoryData' =>$extraBuyCategory]);
    }

    /**
     * Updates a ProductExtraBuyRelation
     *
     * @param ProductExtraBuyRelation
     * @return int The id of the ProductExtraBuyRelation
     */
    public function updateProductExtraBuyRelation($relation){
        return $this->callApi('Product_UpdateExtraBuyRelation',['ExtraBuyRelationData' =>$relation]);
    }

    /**
     * Updates a ProductFile
     *
     * @param ProductFile
     * @return int The id of the ProductFile
     */
    public function updateProductFile($file){
        return $this->callApi('Product_UpdateFile',['FileData' =>$file]);
    }

    /**
     * Updates a ProductPicture
     *
     * @param ProductPicture
     * @return int The id of the ProductPicture
     */
    public function updateProductPicture($picture){
        return $this->callApi('Product_UpdatePicture',['PictureData' =>$picture]);
    }

    /**
     * Updates a ProductUnit
     *
     * @param ProductUnit
     * @return int The id of the ProductUnit
     */
    public function updateProductUnit($unit){
        return $this->callApi('Product_UpdateUnit',['ProductUnitData' =>$unit]);
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

    /**
     * Updates a product variant type
     *
     * @param object $variantType Product variant type object.
     * @return int The id  of the updated ProductVariantType
     */
    public function updateProductVariantType($variantType){
        return $this->callApi('Product_UpdateVariantType',['VariantTypeData' =>$variantType]);
    }

    /**
     * Updates a product variant type
     *
     * @param object $variantTypeValue ProductVariantTypeValueUpdate variant type object.
     * @param object $variantType VariantTypeValueData variant type object.
     * @return int The id  of the updated ProductVariantType
     */
    public function updateProduct_UpdateVariantTypeValue($variantTypeValue, $variantType){
        return $this->callApi('Product_UpdateVariantTypeValue',['VariantTypeValueData' => $variantTypeValue,'VariantTypeData' =>$variantType]);
    }

    /**
     * Deletes a SEORedirect
     * 
     * 
     * @param int SEORedirectId. The id of the SEORedirect to delete
     */
    public function deleteSeoRedirect($seoRedirectId){
        return $this->callApi('SEORedirect_Delete',['SEORedirectId' =>$seoRedirectId]);
    }

    /**
     * Returns all SEORedirects
     *
     * @param ProductUnit
     * @return [] of SEORedirect Objects
     */
    public function getAllSeoRedirects(){
        $seoRedirects = $this->callApi('SEORedirect_GetAll');
        $return = [];
        foreach($seoRedirects as $seo){
            $return[] = new SEORedirect($seo);
        }
        return $return;
    }

    /**
     * Returns the indicated SEORedirect by Id
     *
     * @param int $seoRedirectId
     * @return SEORedirect
     */
    public function getSeoRedirect($seoRedirectId){
        return $this->callApi('SEORedirect_GetById',['SEORedirectId' =>$seoRedirectId]);
    }

    /**
     * Returns the Sites of the solution
     *
     * @param int $seoRedirectId
     * @return [] of Site Objects
     */
    public function getAllSites(){
        $sites = $this->callApi('Sites_GetAll');
        $return = [];
        foreach($sites as $site){
            $return[] = new Site($site);
        }
        return $return;
    }
    
    /**
     * Returns the Sites of the solution By Id
     *
     * @param int $siteId The if of the Site
     * @return Site Object
     */
    public function getSite($siteId){
        return $this->callApi('Sites_GetById', ["SiteId" => $siteId]);
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
     * Creates a userGroup.
     * 
     * @param object $userGroup UserGroup object, corresponding to smartweb UserGroupCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/UserGroupCreate.html
     * @return int Id of the created userGroup.
     */
    public function createUserGroup($userGroup): int
    {
        return $this->callApi('User_CreateGroup',array('UserGroupData' =>$userGroup));
    }

    /**
     * Creates or Updates a User using Username as key. Assumes that only unique Usernames exist in the shop. If the Username supplied is not found a new User is created
     * 
     * @param object $userGroup UserGroup object, corresponding to smartweb UserCreateUpdate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/UserCreateUpdate.html
     * @return int Id of the created userGroup.
     */
    public function updateUser($user): int
    {
        return $this->callApi('User_CreateOrUpdate',array('UserData' =>$user));
    }

    /**
     * Deletes a user
     *
     * @param int $id Id of the user you wish to delete.
     * @return mixed
     */
    public function deleteUser($id){
        return $this->callApi('User_Delete',['UserId' =>$id]);
    }

    /**
     * Deletes a user group
     *
     * @param int $id Id of the user you wish to delete.
     * @return mixed
     */
    public function deleteUserGroup($groupId){
        return $this->callApi('User_DeleteGroup',['UserGroupId' =>$groupId]);
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
     * Returns all Users created or updated in a given timespan. The output format can be set with User_SetFields
     * 
     * @param strting $start the start date of the query in 'yyyy-mm-dd' or 'yyyy-mm-dd hh:mm:ss' format. Null to fetch users from current date and on
     * @param string $end the end date of the query in 'yyyy-mm-dd' or 'yyyy-mm-dd hh:mm:ss' format. Null to fetch users to current date and before
     * @return [] of User objects
     */
    public function getUsersByDate($start, $end)
    {
        $users = $this->callApi('User_GetAllByDate', ["Start" => $start, "End" => $end]);
        $return = [];
        foreach($users as $user){
            $return[] = new User($user);
        }
        return $return;
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
     * Gets a user by id.
     * 
     * @param int $id Id of the user to fetch.
     * @return object The fetched user object.
     */
    public function getUser ($id){
        return $this->callApi('User_GetById',array('UserId' =>$id));
    }

    /**
     * Gets a user by name
     * 
     * @param string $name Name of the user to match against.
     * @return object The fetched user object.
     */
    public function getUserByName ($name){
        return $this->callApi('User_GetByName',array('UserName' =>$name));
    }

    /**
     * Retrieves all UserGroups of the solution
     * 
     * @param withInterests wether or not interests should be returned with interest fields included
     * @return [] of UserGroup Objects
     */
    public function getAllUserGroup ($withInterests){
        $users = $this->callApi('User_GetGroupAll',array('withInterests' =>$withInterests));
        $return = [];
        foreach($users as $user){
            $return[] = new UserGroup($user);
        }
        return $return;
    }

    /**
     * Returns the indicated UserGroup by Id
     * 
     * @param $id The id of the UserGroup
     * @return UserGroup
     */
    public function getUserGroupById ($id){
        return $this->callApi('User_GetGroupById',array('UserGroupId' =>$id));
    }

    /**
     * UnSubscribe user from newsletter.
     *
     * @param User
     * @return id of the updated user
     */
    public function unsubscribeNewsletterUser($user){
        $user->Newsletter = false;
        return $this->callApi('User_Update', array('UserData' => $user));
    }  
        

    /**
     * Updates a user group
     *
     * @param UserGroup
     * @return id of the userGroup
     */
    public function updateUserGroup ($userGroup){
        return $this->callApi('User_UpdateGroup',array('UserGroupData' =>$userGroup));
    }


    /**
     * Creates VatGroup.
     * 
     * @param object $vatGroup VatGroup object, corresponding to smartweb VatGroupCreate schema.
     * @see https://api.smart-web.dk/doc/Hosted%20Solution%20API/VatGroupCreate.html
     * @return int Id of the created VatGroup.
     */
    public function createVatGroup($vatGroup)
    {
        return $this->callApi('VatGroup_Create',['VatGroupData' =>$vatGroup]);
    }

    /**
     * Deletes a VatGroup
     * 
     * @param $id The id of the VatGroup to delete
     * 
     */
    public function deleteVatGroup($id)
    {
        return $this->callApi('VatGroup_Delete',['VatGroupId' =>$id]);
    }

    /**
     * Returns the VatGroups of the solution
     * 
     * @return [] of VatGroup Objects
     */
    public function getAllVatGroup()
    {
        $vatGroups = $this->callApi('VatGroup_GetAll');
        $return = [];
        foreach($vatGroups as $vatGroup){
            $return[] = new VatGroup($vatGroup);
        }
        return $return;
    }

    /**
     * Retrieve a VatGroup by Id
     * 
     * @param int $int The id of the wanted VatGroup
     * @return VatGroup
     */
    public function getVatGroup($id)
    {
        return $this->callApi('VatGroup_Delete',['VatGroupId' =>$id]);
    }

    /**
     * Updates a VatGroup
     * 
     * @param VatGroup
     * @return int The if of the newly created VatGroup
     */
    public function updateVatGroup($vatGroup)
    {
        return $this->callApi('VatGroup_Update',['VatGroupData' =>$vatGroup]);
    }

    
}

