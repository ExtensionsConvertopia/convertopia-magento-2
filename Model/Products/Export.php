<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

/**
 * @category   Innovadel Technologies Ltd.
 * @package    Innovadeltech_Convertopia
 * @subpackage etc
 * @author     Innovadel Technologies <support@innovadeltech.com>
 * @copyright  Copyright (c) 2019 Innovadel Technologies (http://www.innovadeltech.com)
 * @version    ${release.version}
 * @link       http://www.innovadeltech.com/
 */

namespace Innovadeltech\Convertopia\Model\Products;

use \Magento\Framework\View\Element\Template;
use \Magento\Framework\App\Action\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Export
{
    /**
     * @var _escaper
     */
    protected $_escaper;
    /**
     * @var resultRawFactory
     */
    protected $resultRawFactory;
    /**
     * @var csvWriter
     */
    protected $csvWriter;
    /**
     * @var fileFactory
     */
    protected $fileFactory;
    /**
     * @var directoryList
     */
    protected $directoryList;
    /**
     * @var ftp
     */
    protected $ftp;
    /**
     * @var scopeConfig
     */
    protected $scopeConfig;
    /**
     * @var _categoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     */
    protected $filesystemDriver;

    /**
     * @var \Magento\Catalog\Helper\Output
     */
    protected $outputHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Get constructors
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\File\Csv $csvWriter
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Filesystem\Io\Ftp $ftp
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheFrontendPool
     * @param WriterInterface $configWriter
     * @param  \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
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
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        WriterInterface $configWriter,
        \Magento\Framework\Filesystem\Driver\File $filesystemDriver,
        \Magento\Catalog\Helper\Output $outputHelper,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager
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
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->configWriter = $configWriter;
        $this->filesystemDriver = $filesystemDriver;
        $this->outputHelper = $outputHelper;
        $this->productRepository = $productRepository;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->storeManager = $storeManager;
    }

    /**
     * Get getExport
     *
     * @param int $isDelta
     * @return array
     */
    public function getExport($isDelta = false)
    {
        $open = null;

        $htmlResponse="";
        try {

            if ($isDelta) {
                $collection = $this->getDeltaProductCollection();
                $filePrefix = 'delta_';
            } else {
                $collection = $this->getProductCollection();
                $filePrefix = '';
            }

            $store = $this->storeManager->getStore();

            $storeUrl = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_LINK
            );
            $data = [];

            $i = 0;
            $data[$i]['category_id'] = 'category_id';
            $data[$i]['categories'] = 'categories';
            $data[$i]['category_url'] = 'category_url';
            $data[$i]['image'] = 'image';
            $data[$i]['sku'] = 'sku';
            $data[$i]['url'] = 'url';
            $data[$i]['name'] = 'name';
            $data[$i]['short_description'] = 'short_description';
            $data[$i]['description'] = 'description';
            $data[$i]['price'] = 'price';
            $data[$i]['id'] = 'id';
            $cats = [];
            $catsId = [];
            $catsUrl = [];
            $parentIds = [];
            $topParentIds = [];
            $parentNames = [];
            $topParentNames = [];

            $i = 1;
            $productCount = 0;
            $c = 1;
            foreach ($collection as $product) {

                $pr = $this->productRepository->getById($product->getId());

                $categoryIds = max($pr->getCategoryIds());
                $categoryData = $this->getCategoryData($categoryIds);
                $extension = substr(strrchr($product->getProductUrl(), '.'), 0);
                if (empty($extension)) {
                    $extension = '';
                }

                $cates = $categoryData['name'];
                $catesUrl = $categoryData['url'];
                $catesId = $categoryData['id'];
                $data[$i]['category_id'] = $catesId;
                $data[$i]['categories'] = $cates;
                $data[$i]['category_url'] = $catesUrl;
                $data[$i]['image'] = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'catalog/product' .$product->getImage();
                $data[$i]['sku'] = $product->getSku();
                $data[$i]['url'] = $storeUrl . $product->getUrlKey() . $extension;
                $data[$i]['name'] = $product->getName();
                $data[$i]['short_description'] = $this->outputHelper->
                        productAttribute($product, $product->getShortDescription(), 'shortdescription');
                $rawDescription = str_replace(['\'', '"'], '', $product->getDescription() ?? '');
                $data[$i]['description'] = $this->_escaper->escapeHtml(strip_tags($rawDescription));
                $data[$i]['price'] = number_format(floatval($product->getPrice()), 2);
                $data[$i]['id'] = $product->getId();
                $i++;
                $c++;
                $productCount++;
            }

            if ($productCount > 0) {
                $fileDirectory = $this->directoryList->getPath('media') . "/convertopia/feed/";
                $ctStoreId = $this->getCtStoreId();
                $fileName = $filePrefix . 'convertopia_feed' . date("Ymds") . '.csv';
                if (!empty($ctStoreId)) {
                    $fileName = $filePrefix . $ctStoreId . '_convertopia_feed' . date("Ymds") . '.csv';
                }
                $filePath = $fileDirectory . $fileName;

                $htmlResponse.="http://54.78.65.10/magento/pub/media/convertopia/feed/" . $fileName;
                
                if (!$this->filesystemDriver->isDirectory($fileDirectory)) {
                    $this->filesystemDriver->createDirectory($fileDirectory, 0777, true);
                }
                $this->csvWriter
                    ->setEnclosure('"')
                    ->setDelimiter('|')
                    ->saveData($filePath, $data);

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

                if ($open) {
                    $content = $this->filesystemDriver->fileGetContents($filePath);
                    $this->ftp->write($fileName, $content);
                    $this->ftp->close();

                    $htmlResponse.="<p style='color: green'>
                    {$productCount} products exported successfully into server
                    {$ftpConfig['host']} with file name {$fileName} </p>";

                    if ($isDelta) {
                        $this->updateDeltaProductDate();
                    }
                } else {

                    $htmlResponse.="<p style='color: red'> Couldn't connect to host. Please check FTP configurations </p>";
                }
               
            } else {
                $htmlResponse.="<p style='color: green'> No Products are avaiable for export </p>";
            }
        } catch (\Throwable $th) {
            //throw $th;
            if (!empty($open)) {
                $this->ftp->close();
            }
            $htmlResponse.="<p style='color: red'> Oops,
            something went wrong while generating product feed and error is {$th}</p>";
             
        }

        return $htmlResponse;
    }

    /**
     * Get getFtpConfig
     *
     * @return array
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
     * @return array
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
     * @return array
     */
    private function getCtFtpPath()
    {
        $cpFtpPath = $this->scopeConfig->getValue('convertopia/ftp_settings/ftp_path', ScopeInterface::SCOPE_STORE);
        return $cpFtpPath;
    }

    /**
     * Retrieve Product collection
     *
     * @return array
     */
    public function getProductCollection()
    {

        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addWebsiteFilter();
        $collection->addStoreFilter();

        // set visibility filter
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInSiteIds());
        // ->addFieldToSelect('*')->addFieldToFilter('entity_id', 7);

        return $collection;
    }
    /**
     * Retrieve Product collection
     *
     * @return array
     */
    public function getDeltaProductCollection()
    {

        $collection = $this->_productCollectionFactory->create();
        $date = $this->scopeConfig
                ->getValue('convertopia/delta_export_products/delta_export_products', ScopeInterface::SCOPE_STORE);
        if ($date != null) {
            $collection = $this->_productCollectionFactory->create()
                ->addFieldToSelect('*')->addFieldToFilter('updated_at', ['gteq' => $date]);
        } else {
            $collection = $this->_productCollectionFactory->create();
        }

        $collection->addAttributeToSelect('*');
        $collection->addWebsiteFilter();
        $collection->addStoreFilter();
        // set visibility filter
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInSiteIds());
        return $collection;
    }

    /**
     * Retrieve customer collection
     *
     * @return array
     */
    public function updateDeltaProductDate()
    {
        date_default_timezone_set('UTC');
        $date = date("Y-m-d H:i:s", time());
        $this->configWriter->save('convertopia/delta_export_products/delta_export_products', $date, $scope
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
     * Get getCategoryData
     *
     * @param int $categoryId
     */
    public function getCategoryData($categoryId)
    {
        $category = $this->_categoryFactory->create()->load($categoryId);
        $categoryData['id'] = $categoryId;
        $categoryData['name'] = $category->getName();
        $categoryData['url'] = $category->getUrl();
        $parentId = $category->getParentCategory()->getId();
        if ($parentId > 2) {
            $categoryData['name'] = $category->getParentCategory()->getName() . '>' . $category->getName();
            $categoryData['url'] = $category->getParentCategory()->getUrl() . '>' . $category->getUrl();
        }
        $parentCategory = $this->_categoryFactory->create()->load($parentId);
        $topParentId = $parentCategory->getParentCategory()->getId();
        $topParentName = $parentCategory->getParentCategory()->getName();
        if ($topParentId > 2) {
            $categoryData['id'] = $parentCategory->getParentCategory()->getId() . '>'
                . $category->getParentCategory()->getId() . '>' . $categoryId;
            $categoryData['name'] = $parentCategory->getParentCategory()->getName() . '>'
                . $category->getParentCategory()->getName() . '>' . $category->getName();
            $categoryData['url'] = $parentCategory->getParentCategory()->getUrl()
                . '>' . $category->getParentCategory()->getUrl() . '>' . $category->getUrl();
        }
        return $categoryData;
    }
}
