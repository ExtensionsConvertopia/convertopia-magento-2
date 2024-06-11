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

namespace Innovadeltech\Convertopia\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\HTTP\ClientInterface;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;


use Laminas\Http\Request;
use Laminas\Http\Response;

class Index extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param PageFactory $resultPageFactory
     * @param Curl $curl
     * @param ScopeConfigInterface $scopeConfig
     * @param CurlFactory $curlFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        PageFactory $resultPageFactory,
        Curl $curl,
        ScopeConfigInterface $scopeConfig,
        CurlFactory $curlFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_productCollectionFactory = $collectionFactory;
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->curlFactory = $curlFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $needle = $this->getRequest()->getParam('q');
        $responseData = $this->getcurlApi($needle);
        $cSearch = $needle;
        if (!empty($responseData)) {
            if (!array_key_exists("message", $responseData)) {
                $cSearch = implode(',', $responseData);
            } else {
                $cSearch = $needle;
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath(
            'catalogsearch/result/index',
            ['q' => $cSearch, 'searchTerm' => $needle]
        );
        return $resultRedirect;
    }

    /**
     * Make API request using CURL
     *
     * @param string $needle
     * @return array
     */
    public function getcurlApi($needle)
    {
        $searchTerm['searchTerm'] = $needle;
        $http = $this->curlFactory->create();
        $cfg = ['timeout' => 30];
        $apiData = $this->getApiKey();
        $http->setConfig($cfg);
        $http->write(
            Request::POST,
            $apiData['feed_url'],
            '1.1',
            ['Content-Type: application/json', 'Authorization: ' . $apiData['authorization']],
            json_encode($searchTerm)
        );
        $resData = $http->read();
        $responseData = json_decode(Response::extractBody($resData), true);
        return $responseData;
    }

    /**
     * Get API key and configuration data
     *
     * @return array
     */
    private function getApiKey()
    {
        $feedUrl = $this->scopeConfig->getValue('convertopia_general/general/service_url') . '/product-search/extended';
        $secretKey = $this->scopeConfig->getValue('convertopia_general/convertopia_store_settings/cp_secret_key');
        $apiKey = $this->scopeConfig->getValue('convertopia_general/convertopia_store_settings/cp_api_key');
        $webStoreId = $this->scopeConfig->getValue('convertopia_general/convertopia_store_settings/store_id');
        $apiData = [];
        $apiData['feed_url'] = $feedUrl;
        $apiData['api_key'] = $apiKey;
        $apiData['secret_key'] = $secretKey;
        $apiData['web-store-id'] = $webStoreId;
        $apiData['authorization'] = 'Basic ' . base64_encode($apiKey . ':' . $secretKey . ':' . $webStoreId);
        return $apiData;
    }
}
