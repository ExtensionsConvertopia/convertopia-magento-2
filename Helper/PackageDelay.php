<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Innovadeltech\Convertopia\Helper;

use Magento\Framework\Webapi\Rest\Response as RestResponse;

use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;

class PackageDelay extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;
    /**
     * @var \Magento\Customer\Model\Session $customer
     */
    protected $customer;
    /**
     * @var \Magento\Checkout\Model\Session $_checkoutSession
     */
    protected $_checkoutSession;
    /**
     * @var \Magento\Catalog\Model\ProductFactory $_productloader
     */
    protected $_productloader;
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    private $categoryRepository;
    /**
     * @var \Magento\Checkout\Model\Cart $cart
     */
    private $cart;
    /**
     * @var CustomerRepositoryInterface $customerRepository
     */
    private $customerRepository;
    /**
     * @var \Magento\Sales\Model\Order $order
     */
    protected $_order;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $_storeManager;
    /**
     * @var \Magento\Directory\Model\Currency $currency
     */
    protected $_currency;
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface $sessionManager
     */
    protected $sessionManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    /**
     * @var \Magento\Framework\Webapi\Rest\Response
     */
    private $response;
    
    /**
     * This is the contructor For class PackageDelay
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Checkout\Model\Session $_checkoutSession
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param RestResponse $response
     * @param \Magento\Customer\Model\SessionFactory $session
     * @param array $data = []
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Session $customer,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        RestResponse $response,
        \Magento\Customer\Model\SessionFactory $session,
        array $data = []
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->_checkoutSession = $_checkoutSession;
        $this->cart = $cart;
        $this->_productloader = $_productloader;
        $this->_storeManager = $storeManager;
        $this->orderRepository = $orderRepository;
        $this->addressCollection = $addressCollection;
        $this->cart = $cart;
        $this->customerRepository = $customerRepository;
        $this->_order = $order;
        $this->_currency = $currency;
        $this->sessionManager = $sessionManager;
        $this->response = $response;
        $this->session = $session;
        parent::__construct($context, $data);
    }
    /**
     * This returns getWebStoreId value
     *
     * @return int
     */
    public function getWebStoreId()
    {
        $webStoreId = $this->scopeConfig->getValue(
            'convertopia_general/convertopia_store_settings/store_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        return $webStoreId;
    }
    /**
     * This returns getRequestShow value
     *
     * @return object
     */
    public function getRequestShow()
    {

        return $this->_request->getFullActionName();
    }
    /**
     * This returns getLoggedinCustomerName value
     *
     * @return string
     */
    public function getLoggedinCustomerName()
    {
        // $customerEmail = $this->session->getQuote()->getCustomerEmail();
        if ($this->customerSession->getData('customer_id')) {

            return $this->customerSession->getData('customer_id');
        } else {
            return "guest";
        }
    }
    /**
     * This returns getHashedEmailString value
     *
     * @return string
     */
    public function getHashedEmailString()
    {
        // $customerEmail = $this->session->getQuote()->getCustomerEmail();
        if ($this->customerSession->getData('customer_id')) {
            $email = $this->customerSession->getCustomer()->getEmail();
            return $email;
        } else {
            return "";
        }
    }

    /**
     * This returns getLoadProduct value
     *
     * @param int $id
     * @return object
     */
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    /**
     * This returns getCategoryNameById value
     *
     * @param int $id
     * @param null|int $storeId
     * @return string
     */
    protected function getCategoryNameById($id, $storeId = null)
    {
        $categoryInstance = $this->categoryRepository->get($id, $storeId);

        return $categoryInstance->getName();
    }
    /**
     * This returns cartView value
     *
     * @return json array
     */
    public function cartView()
    {
        $items = $this->cart->getQuote()->getAllVisibleItems();
        $base_url = $this->_storeManager->getStore()->getBaseUrl();
        $main_cart_arr = [];
        foreach ($items as $item) {
            $cart_arr = [];
            $cart_arr['id'] = $item->getProductId();
            $cart_arr['name'] = $item->getName();
            $cart_arr['Price'] = $item->getPrice();
            $product = $this->getLoadProduct($item->getProductId());
            $sub_cart_arr = [];
            foreach ($product->getCategoryIds() as $id) {
                $productCategory = $this->getCategoryNameById($id);
                $sub_cart_arr[] = $productCategory;
            }

            $cart_arr['category'] =  $sub_cart_arr;
            $cart_arr['url'] = $base_url . $product->getUrlKey();
            $main_cart_arr[] = $cart_arr;
        }

        // $this->response->setBody($main_cart_arr)->setHeader('Content-Type', 'application/json');

        return (json_encode($main_cart_arr));
    }
    /**
     * This returns lineItems value
     *
     * @return array
     */
    public function lineItems()
    {
        $items = $this->cart->getQuote()->getAllVisibleItems();
        $quote = $this->cart->getQuote();
        $base_url = $this->_storeManager->getStore()->getBaseUrl();
        $main_cart_arr = [];
        foreach ($items as $item) {
            $cart_arr = [];
            $cart_arr['id'] = $item->getProductId();
            $cart_arr['name'] = $item->getName();
            $cart_arr['Price'] = $item->getBaseRowTotal();
            $product = $this->getLoadProduct($item->getProductId());
            $sub_cart_arr = [];
            foreach ($product->getCategoryIds() as $id) {
                $productCategory = $this->getCategoryNameById($id);
                $sub_cart_arr[] = $productCategory;
            }

            $cart_arr['category'] =  $sub_cart_arr;
            $cart_arr['url'] = $base_url . $product->getUrlKey();
            $main_cart_arr[] = $cart_arr;
        }

        // $this->response->setBody($main_cart_arr)->setHeader('Content-Type', 'application/json');

        return (($main_cart_arr));
    }
    /**
     * This returns getLineItem value
     *
     * @param object $product
     * @return array
     */
    public function getLineItem($product)
    {

        $base_url = $this->_storeManager->getStore()->getBaseUrl();
        $main_cart_arr = [];
        $cart_arr = [];
        $cart_arr['id'] = $product->getEntityId();
        $cart_arr['name'] = $product->getName();
        $cart_arr['Price'] = $product->getBaseRowTotal();
        $product = $this->getLoadProduct($product->getEntityId());
        $sub_cart_arr = [];
        foreach ($product->getCategoryIds() as $id) {
            $productCategory = $this->getCategoryNameById($id);
            $sub_cart_arr[] = $productCategory;
        }

        $cart_arr['category'] =  $sub_cart_arr;
        $cart_arr['url'] = $base_url . $product->getUrlKey();
        $main_cart_arr[] = $cart_arr;

        // $this->response->setBody($main_cart_arr)->setHeader('Content-Type', 'application/json');

        return $main_cart_arr;
    }
    /**
     * This returns getCustomer value
     *
     * @param int $customerId
     * @return object
     */
    public function getCustomer($customerId)
    {
        $customer = $this->_customers->load($customerId);
    }
    /**
     * This returns getShippingDetails value
     *
     * @return array
     */
    public function getShippingDetails()
    {
        $shippingAddress = $this->cart->getQuote()->getShippingAddress();
        // $main_shipping_arr = [];
        $shipping_arr = [];
        $shipping_arr['first_name'] = $shippingAddress->getData('firstname');
        $shipping_arr['last_name'] = $shippingAddress->getData('lastname');
        $shipping_arr['address1'] = $shippingAddress->getData('street');
        $shipping_arr['city'] = $shippingAddress->getData('city');
        $shipping_arr['state'] = $shippingAddress->getData('region');
        $shipping_arr['zip'] = $shippingAddress->getData('postcode');
        $shipping_arr['country'] = $shippingAddress->getData('country_id');

        return json_encode($shipping_arr);
        // return $main_shipping_arr;
    }
    /**
     * This returns getShippingAddress value
     *
     * @param var $order
     * @return array
     */
    public function getShippingAddress($order)
    {
        $shippingAddress = $order->getShippingAddress();
        // $main_shipping_arr = [];
        $shipping_arr = [];
        $shipping_arr['first_name'] = $shippingAddress->getData('firstname');
        $shipping_arr['last_name'] = $shippingAddress->getData('lastname');
        $shipping_arr['address1'] = $shippingAddress->getData('street');
        $shipping_arr['city'] = $shippingAddress->getData('city');
        $shipping_arr['state'] = $shippingAddress->getData('region');
        $shipping_arr['zip'] = $shippingAddress->getData('postcode');
        $shipping_arr['country'] = $shippingAddress->getData('country_id');
        // $shippingMethod = $shippingAddress->getShippingMethod();

        return $shipping_arr;
        // return $main_shipping_arr;
    }
    /**
     * This returns getBillingAddress value
     *
     * @param var $order
     * @return array
     */
    public function getBillingAddress($order)
    {
        $billingAddress = $order->getBillingAddress();
        // $main_shipping_arr = [];
        $billing_arr = [];

        $billing_arr['first_name'] = $billingAddress->getData('firstname');
        $billing_arr['last_name'] = $billingAddress->getData('lastname');
        $billing_arr['address1'] = $billingAddress->getData('street');
        $billing_arr['city'] = $billingAddress->getData('city');
        $billing_arr['state'] = $billingAddress->getData('region');
        $billing_arr['zip'] = $billingAddress->getData('postcode');
        $billing_arr['country'] = $billingAddress->getData('country_id');
        // $shippingMethod = $shippingAddress->getShippingMethod();

        return $billing_arr;
        // return $main_shipping_arr;
    }
    /**
     * This returns getLineItems value
     *
     * @param var $order
     * @return array
     */
    public function getLineItems($order)
    {

        $main_cart_arr = [];
        foreach ($order->getAllVisibleItems() as $item) {
            $cart_arr = [];
            $cart_arr['id'] = $item->getProductId();
            $cart_arr['name'] = $item->getName();
            $cart_arr['Price'] = $item->getBaseRowTotal();
            $product = $this->getLoadProduct($item->getProductId());
            $sub_cart_arr = [];
            foreach ($product->getCategoryIds() as $id) {
                $productCategory = $this->getCategoryNameById($id);
                $sub_cart_arr[] = $productCategory;
            }

            $cart_arr['category'] =  $sub_cart_arr;
            $base_url = $this->_storeManager->getStore()->getBaseUrl();

            $cart_arr['url'] = $base_url . $product->getUrlKey();
            $main_cart_arr[] = $cart_arr;
        }

        return $main_cart_arr;
    }

    /**
     * This returns getOrderTotal value
     *
     * @return int
     */
    public function getOrderTotal()
    {
        /**
         * $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         * $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
         */
        $grandTotal = $this->cart->getQuote()->getGrandTotal();
        return $grandTotal;
    }
    /**
     * This returns getCurrentCurrencyCode value
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }
    /**
     * This returns getSessionId value
     *
     * @return int
     */
    public function getSessionId()
    {
        $sessionId = $this->sessionManager->getSessionId();

        if ($this->customerSession->getData('customer_id')) {

            return $this->customerSession->getCustomer()->getEmail();
        } else {
            return $sessionId;
        }
    }
    /**
     * This returns getPageView value
     *
     * @return string
     */
    public function getPageView()
    {
        $current_view = $this->_request->getFullActionName();

        $page_view = [
            'cms_index_index' => 'HOME',
            'catalog_category_view' => 'CLP',
            'catalog_product_view' => 'PDP',
            'checkout_cart_index' => 'CART',
            'customer_account_login' => 'ACCOUNT_LOGIN',
            'customer_account_create' => 'ACCOUNT_SIGNUP',
            'customer_account_index' => 'ACCOUNT_DASHBORD',
            'checkout_index_index' => 'CHECKOUT_BEGIN',
            'checkout_onepage_success' => 'ORDER_CONFIRMATION',
        ];

        //

        if (isset($page_view[$current_view])) {
            return $page_view[$current_view];
        }
        return $current_view;
    }
}
