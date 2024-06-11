<?php

namespace Innovadeltech\Convertopia\Model\Customers;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\App\Action\Context;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\Driver\File;

class Export
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvWriter;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Framework\Filesystem\Io\Ftp
     */
    protected $ftp;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Logger
     */
    protected $customerLogger;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     */
    protected $filesystemDriver;

    /**
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\File\Csv $csvWriter
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\Ftp $ftp
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Logger $customerLogger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param  \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     */

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvWriter,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Io\Ftp $ftp,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Escaper $escaper,
        CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Logger $customerLogger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory = $fileFactory;
        $this->csvWriter = $csvWriter;
        $this->directoryList = $directoryList;
        $this->ftp = $ftp;
        $this->scopeConfig = $scopeConfig;
        $this->_categoryFactory = $categoryFactory;
        $this->_escaper = $escaper;
        $this->collectionFactory = $collectionFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->customerLogger = $customerLogger;
        $this->timezone = $timezone;
        $this->configWriter = $configWriter;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->filesystemDriver = $filesystemDriver;
    }

    /**
     * Get getExport
     *
     * @param int $isDelta
     * @return object
     */
    public function getExport($isDelta = false)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/my_customcustom.log');
        $logger = new \Zend_Log();
        $htmlResponse="";
        $logger->addWriter($writer);
        $logger->info("customr model execute");

        if ($isDelta) {
            $logger->info("Yes Delta ");
        } else {
            $logger->info("No  Delta ");
        }

        // return "";
        $open = null;
        try {
            if ($isDelta) {
                $collection = $this->getDeltaCustomerCollection();
                $filePrefix = 'delta_';
            } else {
                $collection = $this->getCustomerCollection();
                $filePrefix = '';
            }

            $customerDataCollection = [];
            foreach ($collection as $customer) {
                $orderCollection = $this->getOrderCollection($customer->getData('entity_id'));
                $customerData = [];

                // echo "<br>";
                // echo $customer->getData('email');
                // echo "<br>";
                // echo $customer->getData('updated_at');
                $customerData['customer_no'] = $customer->getData('entity_id') ?? "NONE";
                $customerData['created'] = $customer->getData('created_at') ?? "NONE";
                $customerData['last_visited_day_time'] = $this->getCustomerLog($customer->
                getData('entity_id'))->getLastVisitAt();
                $customerData['login'] = $customer->getData('email') ?? "NONE";
                $customerData['profile'] = $this->getCustomerProfileData($customer);
                $customerData['phone'] = $this->getCustomerPhoneData($customer);
                $customerData['addresses'] = $this->getCustomerAddressData($customer);
                $customerData["placed_orders_count"] = $this->getCustomerOrderDataCount($orderCollection);
                $customerData["orders"] = $this->getCustomerOrderData($orderCollection);
                $customerData["customs"] = $this->getCustomerCustomAttributeData($customer);
                $customerDataCollection[] = $customerData;
            }
            $fileDirectory = $this->directoryList->getPath('media') . "/convertopia/feed/";
            $ctStoreId = $this->getCtStoreId();
            $fileName =  $filePrefix . 'customer_' . date("Ymds") . '.json';
            if (!empty($ctStoreId)) {
                $fileName =  $filePrefix . $ctStoreId . '_customer_' . date("Ymds") . '.json';
            }
            $filePath = $fileDirectory .  $fileName;
            if (!$this->filesystemDriver->isDirectory($fileDirectory)) {
                $this->filesystemDriver->createDirectory($fileDirectory, 0777, true);
            }

            $customerCount = 0;

            // $json = json_encode($customerDataCollection)  ;

            // file_put_contents($filePath, $json, FILE_APPEND);

            foreach ($customerDataCollection as $line) {
                $customerCount++;
                $json = json_encode($line) . "\n";
                $this->filesystemDriver->filePutContents($filePath, $json, FILE_APPEND);
            }

            // echo "<br>";
            // echo "<pre>";
            // print_r($customerDataCollection);

            // $filePath = $fileDirectory .  $fileName;
            $htmlResponse.= "http://54.78.65.10/magento/pub/media/convertopia/feed/".$fileName;  
            $ftpConfig = $this->getFtpConfig();
            if ($isDelta) {
                $this->exportUpdateDeltaCustomerDate();
            }
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

            if ($customerCount > 0) {
                if ($open) {
                    $content = $this->filesystemDriver->fileGetContents($filePath);
                    $this->ftp->write($fileName, $content);
                    $this->ftp->close();
                    $htmlResponse.= "<p style='color: green'> {$customerCount} Customer  exported successfully into server 
                    {$ftpConfig['host']} with file name {$fileName} </p>"; 
                    if ($isDelta) {
                        $this->exportUpdateDeltaCustomerDate();
                    }
                } else {
                    $htmlResponse.= "<p style='color: red'> Couldn't connect to host. Please check FTP configurations </p>";
                }
            } else {
                $htmlResponse.= "<p style='color: green'> No more Customers are avaiable for  export </p>"; 
            }
            
        } catch (\Throwable $th) {
            //throw $th;
            if (!empty($open)) {
                $this->ftp->close();
            }
            $htmlResponse.= "<p style='color: red'> Oops, 
            something went wrong while generating product feed and error is {$th}</p>"; 
        }

        return $htmlResponse;
    }

    /**
     * Get getFtpConfig
     *
     * @param getFtpConfig
     * @return object
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
     * Get getCtStoreId
     *
     * @return getCtStoreId
     */
    private function getCtStoreId()
    {
        $cpStoreId = $this->scopeConfig->
        getValue('convertopia_general/convertopia_store_settings/store_id', ScopeInterface::SCOPE_STORE);
        return $cpStoreId;
    }

    /**
     * Get getCtFtpPath
     *
     * @param getCtFtpPath
     * @return object
     */
    private function getCtFtpPath()
    {
        $cpFtpPath = $this->scopeConfig->getValue('convertopia_general/ftp_settings/ftp_path', ScopeInterface::SCOPE_STORE);
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
        // ->addFieldToSelect('*')->addFieldToFilter('entity_id', 7);
    }

    /**
     * Retrieve customer collection
     *
     * @return array
     */
    public function getDeltaCustomerCollection()
    {

        $date = $this->scopeConfig->
        getValue('convertopia_general/delta_export_customer/delta_export_customer', ScopeInterface::SCOPE_STORE);

        if ($date != null) {
            return $this->collectionFactory->create()
                ->addFieldToSelect('*')->addFieldToFilter('updated_at', ['gteq' => $date]);
        } else {
            return $this->collectionFactory->create();
        }
    }

    /**
     * Retrieve customer collection
     *
     * @return array
     */
    public function exportUpdateDeltaCustomerDate()
    {
        date_default_timezone_set('UTC');
        $date = date("Y-m-d H:i:s", time());
        $this->configWriter->save('convertopia/delta_export_customer/delta_export_customer', $date, $scope
        = ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $types = ['config'];
        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        // echo "updated";
        // echo $date;
    }
    /**
     * Retrieve Order  collection
     *
     * @param int $customerId
     * @return array
     */
    public function getOrderCollection($customerId)
    {
        return $this->_orderCollectionFactory->create()
            ->addFieldToSelect('*')->addFieldToFilter('customer_id', $customerId);
    }

    /**
     * Retrieve Customer Profile
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return object
     */
    public function getCustomerProfileData($customer)
    {
        $customerData = [];
        $customerData['salutation'] = $customer->getData('prefix');
        $customerData['title'] = $customer->getData('title')  ?? "NONE" ;
        $customerData['company'] = $customer->getData('company');
        $customerData['job_title'] = $customer->getData('job_title');
        $customerData['first_name'] = $customer->getData('firstname') ?? "NONE";
        $customerData['last_name'] = $customer->getData('lastname') ?? "NONE";
        $customerData['name_suffix'] = $customer->getData('suffix')    ?? "NONE";
        $gender = $customer->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender'));
        if ($gender) {
            $customerData['gender'] = $gender ;
        } else {
            $customerData['gender'] = "NONE";
        }
        $customerData['birthday'] = $customer->getData('dob') ?? "NONE";
        $customerData['email'] = $customer->getEmail() ?? "NONE";
        $date = $customer->getData('dob');
        $next_birthday = $date;
        if ($date) {
            $next_birthday = $this->nextBirthday($date);
        }
        $customerData['next_birthday'] =  $next_birthday;
        $customerData['second_name'] = $customer->getData('middlename');
        return $customerData;
    }

    /**
     * Get nextBirthday
     *
     * @param int $date
     */
    public function nextBirthday($date)
    {
        $today_date = $this->timezone->date()->format('Y-m-d');
        $year = date('Y', strtotime($today_date));
        $month = date('m', strtotime($today_date));
        $day = date('d', strtotime($today_date));
        $birthday_month = date('m', strtotime($date));
        $birthday_day = date('d', strtotime($date));
        if ($birthday_month > $month) {
            $next_birthday = $year . "-" . $birthday_month . "-" . $birthday_day;
        } elseif ($birthday_day < $day) {
            $next_birthday = "++$year" . "-" . $birthday_month . "-" . $birthday_day;
        } else {
            if ($birthday_day > $day) {
                $next_birthday = $year . "-" . $birthday_month . "-" . $birthday_day;
            } elseif ($birthday_day <= $day) {
                $next_birthday = "++$year" . "-" . $birthday_month . "-" . $birthday_day;
            }
        }
        return  $next_birthday;
    }
    /**
     * Retrieve Customer Address Data
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    public function getCustomerAddressData($customer)
    {
        $addresses = [];

        foreach ($customer->getAddresses() as $address) {
            $customerAddressData = [];
            $customerAddressData['address_id'] = $address->getData('entity_id') ?? "NONE";
            $customerAddressData['title'] = "NONE";
            $customerAddressData['company'] = $address->getData('company');
            $customerAddressData['salutation'] = $address->getData('prefix');
            $customerAddressData['first_name'] = $address->getData('firstname') ?? "NONE";
            $customerAddressData['last_name'] = $address->getData('lastname') ?? "NONE";
            $customerAddressData['second_name'] =  $address->getData('middlename');
            $customerAddressData['suffix'] = $address->getData('suffix') ?? "NONE";
            $customerAddressData['address_1'] = $address->getStreet()[0] ?? "NONE";
            $customerAddressData['address_2'] =  "NONE";
            if (isset($address->getStreet()[1])) {
                $customerAddressData['address_2'] = $address->getStreet()[1];
            }

            $customerAddressData['suite_no'] = "";
            $customerAddressData['postal_box'] =  "NONE";
            $customerAddressData['city'] = $address->getData('city') ?? "NONE";
            $customerAddressData['postal_code'] = $address->getData('postcode') ?? "NONE";
            $customerAddressData['country'] = $address->getData('country_id') ?? "NONE";
            $customerAddressData['state'] = $address->getData('region') ?? "NONE";
            $customerAddressData['contact_phone'] = $address->getData('telephone') ?? "NONE";

            $addresses[] = $customerAddressData;
        }
        return $addresses;
    }
    /**
     * Retrieve Customer Phone Data
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    public function getCustomerPhoneData($customer)
    {
        $customerPhoneData = [];
        $customerPhoneData['home_phone'] = $customer->getData('home_phone') ?? "NONE";
        $customerPhoneData['business_phone'] = $customer->getData('business_phone');
        $customerPhoneData['mobile_phone'] = $customer->getData('mobile_phone') ?? "NONE";
        $customerPhoneData['fax_number'] = $customer->getData('fax_number');
        return $customerPhoneData;
    }

    /**
     * Retrieve Customer Order Data
     *
     * @param array $orderCollection
     * @return array
     */
    public function getCustomerOrderData($orderCollection)
    {
        $customerOrderData = [];
        foreach ($orderCollection as $order) {
            $ordderDetail = [];
            $ordderDetail['order_id'] = $order->getData('increment_id')?? "NONE";
            $ordderDetail['order_date'] = $order->getData('created_at')?? "NONE";
            $customerOrderData[] = $ordderDetail;
        }
        return $customerOrderData;
    }


        /**
     * Retrieve Customer Order Data
     *
     * @param array $orderCollection
     * @return array
     */
    public function getCustomerOrderDataCount($orderCollection)
    {
        return count($orderCollection);
    }
    /**
     * Retrieve Customer Order Data
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return array
     */
    public function getCustomerCustomAttributeData($customer)
    {
        $customerCustomAttributeData = [];
        $customAttribute = [];
        $customAttribute['customs_attribute_name'] = 'updated_at';
        $customAttribute['customs_attribute_value'] = $customer->getData('updated_at')?? "NONE";
        $customerCustomAttributeData[] = $customAttribute;
        $customAttribute['customs_attribute_name'] = 'created_in';
        $customAttribute['customs_attribute_value'] = $customer->getData('created_in')?? "NONE";
        $customerCustomAttributeData[] = $customAttribute;
        $customAttribute['customs_attribute_name'] = 'group_id';
        $customAttribute['customs_attribute_value'] = $customer->getData('group_id')?? "NONE";
        $customerCustomAttributeData[] = $customAttribute;
        return $customerCustomAttributeData;
    }
    /**
     * Retrieves customer log model
     *
     * @return \Magento\Customer\Model\Log
     */
    /**
     * Get getCustomerLog
     *
     * @param int $customerId
     */
    protected function getCustomerLog($customerId)
    {

        $customerLog = $this->customerLogger->get($customerId);
        return $customerLog;
    }
}
