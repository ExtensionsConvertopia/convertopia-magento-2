<?php

namespace Innovadeltech\Convertopia\Model\Orders;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Exception;
use Psr\Log\LoggerInterface;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\App\Action\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\Timezone\LocalizedDateToUtcConverterInterface;

class Export
{
     
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var \Magento\Framework\Controller\Result\FileFactory
     */
    protected $fileFactory;
    /**
     * @var \Magento\Framework\Controller\Result\Csv
     */
    protected $csvWriter;
    /**
     * @var \Magento\Framework\Controller\Result\DirectoryList
     */
    protected $directoryList;
    /**
     * @var \Magento\Framework\Controller\Result\Ftp
     */
    protected $ftp;
    /**
     * @var \Magento\Framework\Controller\Result\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Controller\Result\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var \Magento\Framework\Controller\Result\Escaper
     */
    protected $escaper;
    /**
     * @var \Magento\Framework\Controller\Result\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var \Magento\Framework\Controller\Result\CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var \Magento\Framework\Controller\Result\TimezoneInterface
     */
    protected $timezone;
    /**
     * @var \Magento\Framework\Controller\Result\TypeListInterface
     */
    protected $cacheTypeList;
    /**
     * @var \Magento\Framework\Controller\Result\Pool
     */
    protected $cacheFrontendPool;
    /**
     * @var \Magento\Framework\Controller\Result\File
     */
    protected $filesystemDriver;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\File\Csv $csvWriter
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\Ftp $ftp
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param LoggerInterface $logger
     */

    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvWriter,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\Ftp $ftp,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        LoggerInterface $logger
    ) {
        $this->_escaper = $escaper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory = $fileFactory;
        $this->csvWriter = $csvWriter;
        $this->directoryList = $directoryList;
        $this->ftp = $ftp;
        $this->scopeConfig = $scopeConfig;
        $this->categoryFactory = $categoryFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->timezone = $timezone;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        $this->filesystemDriver = $filesystemDriver;
        $this->configWriter = $configWriter;
        $this->logger = $logger;
    }

    /**
     * GetExport
     *
     * @param int $isDelta
     */
    public function getExport($isDelta = false)
    {
        // $delta_export = $this->scopeConfig->
        // getValue('convertopia/delta_export/delta_export',
        // // ScopeInterface::SCOPE_STORE);
        // $date = $this->timezone->date()->format('Y-m-d H:i:s');

        //  print_r $date;
        // $this->configWriter->save('convertopia/delta_export/delta_export',
        // $date, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        // $created = $this->timezone->date($date2)->format('Y-m-d H:i:s');
        //    if (strtotime($delta_export) <= strtotime($created)) {
        //    }
        // $this->configWriter->save('convertopia/delta_export/delta_export',
        // $date , $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT);

        $delta_export = "";
        $open = null;


        $htmlResponse="";

            
        try {

            if ($isDelta) {
                $collection = $this->getOrderCollectionDateFilter();
                $filePrefix = 'delta_';
            } else {
                $collection = $this->getOrderCollection();
                // $collection = $this->getOrderCollectionIncrementId();
                $filePrefix = '';
            }

            //

            $order_arrar = [];

            $check_count = 0;
            foreach ($collection as $order) {
                //  print_r $order->getIncrementId();
                //  print_r "<br>";
                $invoice_id = "NONE";

                foreach ($order->getInvoiceCollection() as $invoice) {
                    $invoice_id = $invoice->getIncrementId();
                }
                $sub_order_arrar = [];
                $sub_order_arrar['order_no'] = $order->getData('increment_id') ?? "NONE";
                $sub_order_arrar['created_by'] = $order->getData('store_name') ?? "NONE";
                $sub_order_arrar['order_date'] = $order->getData('created_at') ?? "NONE";
                $sub_order_arrar['currency'] = $order->getData('order_currency_code') ?? "NONE";
                $sub_order_arrar['locale'] = "NONE";
                $sub_order_arrar['taxation'] = "NET";
                $sub_order_arrar['invoice'] = $invoice_id;
                $sub_order_arrar['customer'] = $this->getCustomer($order);
                $sub_order_arrar['status'] = $this->getStatus($order);
                $sub_order_arrar['product_line_items'] = $this->getProductLineItems($order);
                $sub_order_arrar['order_level_discount'] = $this->getOrderLevelDiscount($order);
                $sub_order_arrar['shipping_line_items'] = $this->getShippingLineItems($order);
                $sub_order_arrar['shipment'] = $this->getShipment($order);
                $sub_order_arrar['total'] = $this->getTotal($order);
                $sub_order_arrar['customs'] = $this->getCustoms($order);
                $order_arrar[] = $sub_order_arrar;
                $check_count++;
                // if ($check_count == 10) {
                //     break;
                // }
                // break;
            }
            $fileDirectory = $this->directoryList->getPath('media') . "/convertopia/feed/";
            $ctStoreId = $this->getCtStoreId();
            $fileName = $filePrefix . 'order_' . date("Ymds") . '.json';
            if (!empty($ctStoreId)) {
                $fileName = $filePrefix . $ctStoreId . '_order_' . date("Ymds") . '.json';
            }
            $filePath = $fileDirectory .  $fileName;
            if (!$this->filesystemDriver->isDirectory($fileDirectory)) {
                $this->filesystemDriver->createDirectory($fileDirectory, 0777, true);
            }
            $orderCount = 0;
            // $json = json_encode($order_arrar);
            // file_put_contents($filePath, $json, FILE_APPEND);

            foreach ($order_arrar as $line) {
                $orderCount++;
                $json = json_encode($line) . "\n";
                $this->filesystemDriver->filePutContents($filePath, $json, FILE_APPEND);
                // file_put_contents($filePath, $json, FILE_APPEND);
            }

            // print_r "<br>";
            // print_r "<pre>";
            // print_r($order_arrar);
            // print_r "<br>";
            // print_r $orderCount;
            // print_r $filePath;
            $htmlResponse.=  "http://54.78.65.10/magento/pub/media/convertopia/feed/" . $fileName;  
            if ($isDelta) {
                $this->updateDeltaOrderDate();
            }
            $ftpConfig = $this->getFtpConfig();
            $open = $this->ftp->open(
                [
                    'host' => $ftpConfig['host'],
                    'user' => $ftpConfig['user'],
                    'password' => $ftpConfig['password'],
                    'path' => $ftpConfig['path'],
                    'ssl' => false,
                    'passive' => true
                ]
            );
            if ($orderCount > 0) {
                if ($open) {
                    $content = $this->filesystemDriver->fileGetContents($filePath);
                    $this->ftp->write($fileName, $content);
                    $this->ftp->close();
                    $htmlResponse.= "<p style='color: green'> {$orderCount} 
                    Order  exported successfully into server {$ftpConfig['host']} with file name {$fileName} </p>";  
                    if ($isDelta) {
                        $this->updateDeltaOrderDate();
                    }
                } else {
                    $htmlResponse.= "<p style='color: red'> Couldn't connect to host. Please check FTP configurations </p>";  
                }
            } else {
                $htmlResponse.= "<p style='color: green'> No more Orders are avaiable for  export </p>";  
            }
         } catch (\Throwable $th) {
            //throw $th;
            if (!empty($open)) {
                $this->ftp->close();
            }
            $htmlResponse.= "<p style='color: red'> Oops, 
            something went wrong while generating order feed and error is {$th}</p>";  
        }

        return $htmlResponse;

    }

    /**
     * SetDataConfig
     *
     * @param var $path
     * @param var $value
     */
    public function setDataConfig($path, $value)
    {
        $this->configWriter->save($path, $value, ScopeInterface::SCOPE_STORE);
    }

    /**
     * GetFtpConfig
     *
     * @param getFtpConfig
     */
    private function getFtpConfig()
    {
        $ftpAuth = [];
        $ftpAuth['host'] = $this->scopeConfig->
        getValue('convertopia_general/ftp_settings/ftp_host', ScopeInterface::SCOPE_STORE);
        $ftpAuth['user'] = $this->scopeConfig->
        getValue('convertopia_general/ftp_settings/ftp_user', ScopeInterface::SCOPE_STORE);
        $ftpAuth['password'] = $this->scopeConfig->
        getValue('convertopia_general/ftp_settings/ftp_password', ScopeInterface::SCOPE_STORE);
        $ftpAuth['port'] = $this->scopeConfig->
        getValue('convertopia_general/ftp_settings/ftp_port', ScopeInterface::SCOPE_STORE);
        $ftpAuth['path'] = $this->scopeConfig->
        getValue('convertopia_general/ftp_settings/ftp_path', ScopeInterface::SCOPE_STORE);

        return $ftpAuth;
    }

    /**
     * GetCtStoreId
     *
     * @param getCtStoreId
     */
    private function getCtStoreId()
    {
        $cpStoreId = $this->scopeConfig->
        getValue('convertopia_general/convertopia_store_settings/store_id', ScopeInterface::SCOPE_STORE);
        return $cpStoreId;
    }

    /**
     * GetCtFtpPath
     *
     * @param getCtFtpPath
     */
    private function getCtFtpPath()
    {
        $cpFtpPath = $this->scopeConfig->
        getValue('convertopia_general/ftp_settings/ftp_path', ScopeInterface::SCOPE_STORE);
        return $cpFtpPath;
    }

    /**
     * Retrieve customer collection
     *
     * @return array
     */
    public function getCustomerCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Retrieve Order  collection
     *
     * @return array
     */
    public function getOrderCollection()
    {
        return $this->orderCollectionFactory->create();
    }

    /**
     * Retrieve Order  collection
     *
     * @return array
     */
    public function getOrderCollectionDateFilter()
    {
        $date = $this->scopeConfig->
        getValue('convertopia/delta_export_orders/delta_export_orders', ScopeInterface::SCOPE_STORE);
        if ($date != null) {
            return $this->orderCollectionFactory->create()
                ->addFieldToSelect('*')->addFieldToFilter('updated_at', ['gteq' => $date]);
        } else {
            return $this->orderCollectionFactory->create();
        }
    }

    /**
     * Retrieve customer collection
     *
     * @return array
     */
    public function updateDeltaOrderDate()
    {
        date_default_timezone_set('UTC');
        $date = date("Y-m-d H:i:s", time());
        $this->configWriter->save('convertopia/delta_export_orders/delta_export_orders', $date, $scope
            = ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $types = ['config'];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        // print_r "updated";
        // print_r $date;
    }

    /**
     * GetOrderCollectionIncrementId
     *
     * @param getOrderCollectionIncrementId
     */
    public function getOrderCollectionIncrementId()
    {
        return $this->orderCollectionFactory->create()
            ->addFieldToSelect('*')->addFieldToFilter('increment_id', '000000147');
    }

    /**
     * GetCustomer
     *
     * @param var $order
     */
    public function getCustomer($order)
    {
        $customerData = [];
        $customerData['customer_no'] =  $order->getCustomerId() ?? "NONE";
        $customerData['customer_name'] = $order->getCustomerFirstname() . " " . $order->getCustomerlastname() ?? "NONE";
        $customerData['customer_email'] = $order->getCustomerEmail() ?? "NONE";
        $customerData['billing_addresss'] = $this->getBillingAddress($order);
        return $customerData;
    }

    /**
     * GetBillingAddress
     *
     * @param var $order
     */
    public function getBillingAddress($order)
    {

        $billingAddress = $order->getBillingAddress();
        $billingData = [];
        $billingData["first_name"] = $billingAddress->getData('firstname') ?? "NONE";
        $billingData["last_name"] = $billingAddress->getData('lastname') ?? "NONE";
        $billingData["address1"] = $billingAddress->getData('street') ?? "NONE";
        $billingData["city"] = $billingAddress->getData('city') ?? "NONE";
        $billingData["postal_code"] = $billingAddress->getData('postcode') ?? "NONE";
        $billingData["state_code"] = $billingAddress->getData('region') ?? "NONE";
        $billingData["country_code"] = $billingAddress->getData('country_id') ?? "NONE";
        $billingData["phone"] = $billingAddress->getData('telephone') ?? "NONE";
        return $billingData;
    }

    /**
     * GetStatus
     *
     * @param var $order
     */
    public function getStatus($order)
    {
        $status = [];
        $status['order_status'] = strtoupper($order->getStatus());
        $status['shipping_status'] = "SHIPPED";
        $status['confirmation_status'] = "CONFIRMED";
        $status['payment_status'] = "PAID";
        if ($order->hasInvoices()) {
            $status['payment_status'] = "PAID";
            $status['confirmation_status'] = "CONFIRMED";
        } else {
            $status['payment_status'] = "NOT_PAID";
            $status['confirmation_status'] = "NOT_CONFIRMED";
        }
        if ($order->hasShipments()) {

            $status['shipping_status'] = "SHIPPED";
        } else {
            $status['shipping_status'] = "NOT_SHIPPED";
        }

        // if ($order->hasShipments() && $order->hasInvoices()) {
        //     $status['order_status'] = "Complete";
        // } else {
        //     $status['order_status'] = "Not_Complete";
        // }

        return $status;
    }

    /**
     * GetProductLineItems
     *
     * @param var $order
     */
    public function getProductLineItems($order)
    {
        $ruleIds = $order->getAppliedRuleIds();
        $product_line_items = [];
        $product_line_items['line_items'] = $this->getLineItems($order);
        $product_line_items['price_adjustment'] = $this->getLineItemPriceAdjustment($ruleIds, $order);
        return $product_line_items;
    }

    /**
     * GetOrderLevelDiscount
     *
     * @param var $order
     */
    public function getOrderLevelDiscount($order)
    {
        $ruleIds = $order->getAppliedRuleIds();
        $order_level['price_adjustment'] = $this->getOrderPriceAdjustment($ruleIds, $order);
        return $order_level;
    }

    /**
     * GetLineItems
     *
     * @param var $order
     */
    public function getLineItems($order)
    {

        $line_items = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $line_items0 = [];
            $netPrice = $item->getData('price_incl_tax') - $item->getData('discount_amount');

            $tax = $item->getData('tax_amount');
            $line_items0["net_price"] = $netPrice ?? "NONE";
            $line_items0["tax"] = $tax ?? "NONE";
            $line_items0["gross_price"] = $item->getData('price_incl_tax') ?? "NONE"; //need to confirm
            $line_items0["base_price"] = $item->getData('base_price') ?? "NONE";
            $line_items0["line_item_text"] = $item->getData('name') ?? "NONE";
            $tax_basis =  $item->getData('price_incl_tax') - $tax;
            $line_items0["tax_basis"] = $tax_basis;
            $line_items0["position"] = $item->getData('position') ?? "NONE";
            $line_items0["product_id"] = $item->getData('product_id') ?? "NONE";
            $line_items0["product_name"] = $item->getData('name') ?? "NONE";
            $line_items0["quantity"] = $item->getData('qty_ordered') ?? "NONE";
            $line_items0["tax_rate"] = $item->getData('tax_percent') ?? "NONE";
            $line_items[] = $line_items0;
        }
        return $line_items;
    }

    /**
     * GetLineItemPriceAdjustment
     *
     * @param int $ruleIds
     * @param var $order
     */
    public function getLineItemPriceAdjustment($ruleIds, $order)
    {
        $price_line_adjustment = [];
        if ($ruleIds != null) {
            $ruleIds = explode(',', $ruleIds);
            foreach ($ruleIds as $ruleId) {
                $salesRuledata = $this->getRuledata($ruleId);
                if ($salesRuledata !=null) {
                    if ($salesRuledata->getSimpleAction() == 'by_percent' ||
                        $salesRuledata->getSimpleAction() == 'by_fixed'
                    ) {
                        $price_adjustment = [];
                        $price_adjustment["net_price"] =  "NONE";
                        $price_adjustment["tax"] =  "NONE";
                        $price_adjustment["gross_price"] = "NONE";
                        $price_adjustment["base_price"] =  "NONE";
                        $price_adjustment["line_item_text"] = $salesRuledata->getName() ?? "NONE";

                        $price_adjustment["promo_id"] = $order->getData('coupon_code') ?? "NONE";  // Question ALL
                        $price_line_adjustment[] =  $price_adjustment;
                    }
                }
            }
        }
        return $price_line_adjustment;
    }

    /**
     * GetOrderPriceAdjustment
     *
     * @param int $ruleIds
     * @param var $order
     */
    public function getOrderPriceAdjustment($ruleIds, $order)
    {
        $price_line_adjustment = [];
        if ($ruleIds != null) {
            $ruleIds = explode(',', $ruleIds);
            foreach ($ruleIds as $ruleId) {
                $salesRuledata = $this->getRuledata($ruleId);

                if ($salesRuledata !=null) {
                    if ($salesRuledata->getSimpleAction() != 'by_percent' &&
                    $salesRuledata->getSimpleAction() != 'by_fixed'
                    ) {
                        $price_adjustment = [];
                        $price_adjustment["net_price"] =  "NONE";
                        $price_adjustment["tax"] =        "NONE";
                        $price_adjustment["gross_price"] =  "NONE";
                        $price_adjustment["base_price"] =  "NONE";
                        $price_adjustment["line_item_text"] = $salesRuledata->getName() ?? "NONE";
                        $price_adjustment["promo_id"] = $order->getData('coupon_code') ?? "NONE";  // Question ALL
                        $price_line_adjustment[] =  $price_adjustment;
                    }
                }
            }
        }
        return $price_line_adjustment;
    }

    /**
     * GetShippingLineItems
     *
     * @param var $order
     */
    public function getShippingLineItems($order)
    {
        $shipping_line_items = [];
        $shipping_line_items['line_items'] = [$this->getLineItem($order)];
        $shipping_line_items['price_adjustment'] = [];
        // $shipping_line_items['price_adjustment'] = $this->getPriceAdjustment();
        return $shipping_line_items;
    }

    /**
     * GetLineItem
     *
     * @param var $order
     */
    public function getLineItem($order)
    {
        $line_items = [];
        $line_items["net_price"] = $order->getData('shipping_amount') ?? "NONE";
        $line_items["tax"] =  $order->getData('shipping_tax_amount') ?? "NONE";
        $line_items["gross_price"] = $order->getData('shipping_tax_amount') +
            $order->getData('shipping_amount') ?? "NONE";
        $line_items["base_price"] = $order->getData('shipping_amount') ?? "NONE";
        $line_items["line_item_text"] = "shipping charges";
        return $line_items;
    }

    /**
     * GetPriceAdjustment
     *
     * @param getPriceAdjustment
     */
    public function getPriceAdjustment()
    {
        $price_adjustment = [];
        $price_adjustment[] = $this->getPricesAdjustment();
        return $price_adjustment;
    }

    /**
     * Get Price Adjustment
     *
     * @param getPricesAdjustment
     */
    public function getPricesAdjustment()
    {
        $price_adjustment = [];
        $price_adjustment["net_price"] = "3";
        $price_adjustment["tax"] = "0";
        $price_adjustment["gross_price"] = "3";
        $price_adjustment["base_price"] = "3";
        $price_adjustment["line_item_text"] = "A 30% off promotion";
        $price_adjustment["promo_id"] = "30% off";
        return $price_adjustment;
    }
    /**
     * Get Shipment
     *
     * @param var $order
     */
    public function getShipment($order)
    {
        $shipment = [];
        if ($order->hasShipments()) {

            $shipment["shipping_status"] = "SHIPPED"; //need to confirm

        } else {
            $shipment["shipping_status"] = "NOT_SHIPPED"; //need to confirm

        }
        // $shipment["shipping_method"] = $order->getData('shipping_method');
        $shipment["shipping_method"] = $order->getShippingDescription();
        $tracksCollection = $order->getTracksCollection();
        $shipment["tracking_number"] = "";
        foreach ($tracksCollection->getItems() as $track) {
            $shipment["tracking_number"]  = $track->getTrackNumber();
        }

        $shipment["shipping_address"] = $this->getShippingAddress($order);
        return $shipment;
    }

    /**
     * Get Shipping Address
     *
     * @param var $order
     */
    public function getShippingAddress($order)
    {
        $shippingAddress = $order->getShippingAddress();
        $shipping_address = [];
        $shipping_address["first_name"] = $shippingAddress->getData('firstname') ?? "NONE";
        $shipping_address["last_name"] = $shippingAddress->getData('lastname') ?? "NONE";
        $shipping_address["address1"] = $shippingAddress->getData('street') ?? "NONE";
        $shipping_address["city"] = $shippingAddress->getData('city') ?? "NONE";
        $shipping_address["postal_code"] = $shippingAddress->getData('postcode') ?? "NONE";
        $shipping_address["state_code"] = $shippingAddress->getData('region') ?? "NONE";
        $shipping_address["country_code"] = $shippingAddress->getData('country_id') ?? "NONE";
        $shipping_address["phone"] = $shippingAddress->getData('telephone') ?? "NONE";
        return $shipping_address;
    }

    /**
     * Get Total
     *
     * @param var $order
     */
    public function getTotal($order)
    {
        $total = [];
        $total["sub_total"] = $order->getData('subtotal') ?? "NONE";
        $total["shipping_total"] = $order->getData('shipping_amount') ?? "NONE";
        $total["tax_total"] = $order->getData('tax_amount') ?? "NONE";
        $total["orders_total"] = $order->getData('grand_total') ?? "NONE";
        return $total;
    }

    /**
     * Get Customs
     *
     * @param var $order
     */
    public function getCustoms($order)
    {
        $customsData = [];
        $customs["custom_attribute_name"] = "updated_at";
        $customs["custom_attribute_value"] = $order->getData('updated_at') ?? "NONE";
        $customsData[] = $customs;
        $customs["custom_attribute_name"] = "weight";
        $customs["custom_attribute_value"] = $order->getData('weight') ?? "NONE";
        $customsData[] = $customs;
        $customs["custom_attribute_name"] = "remote_ip";
        $customs["custom_attribute_value"] = $order->getData('remote_ip') ?? "NONE";
        $customsData[] = $customs;
        return $customsData;
    }
    /**
     * Getting Country Name
     *
     * @param string $countryCode
     *
     * @return null|string
     * */
    public function getCountryName($countryCode)
    {
        $countryName = null;
        return $countryName;
    }
    /**
     * Get Rules Data
     *
     * @param  var $ruleId
     * @return RuleInterface|null
     */
    public function getRuledata($ruleId): ?RuleInterface
    {
        $salesRule = null;
        try {
            $salesRule = $this->ruleRepository->getById($ruleId);
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return $salesRule;
    }
}
