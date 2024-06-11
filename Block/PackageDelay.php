<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Innovadeltech\Convertopia\Block;

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
     * @var \Magento\Checkout\Model\SessionFactory $_checkoutSession
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
     * @var  CustomerRepositoryInterface $customerRepository
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
     * @var \Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
     */
    protected $quoteIdToMaskedQuoteId;
    /**
     * @var \Magento\Framework\Webapi\Rest\Response
     */
    private $response;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Checkout\Model\SessionFactory $_checkoutSession
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
     * @param \Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId
     * @param RestResponse $response
     * @param array $data = []
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Session $customer,
        \Magento\Checkout\Model\SessionFactory $_checkoutSession,
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
        \Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId,
        RestResponse $response,

        array $data = []
    ) {
        parent::__construct($context, $data);
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
        $this->quoteIdToMaskedQuoteId = $quoteIdToMaskedQuoteId;
    }
    /**
     * This return fullActionName value
     *
     * @return string
     */
    public function getRequestShow()
    {

        return $this->_request->getFullActionName();
    }
    /**
     * This return customer_id value else guest
     *
     * @return string
     */
    public function getLoggedinCustomerName()
    {

        if ($this->customerSession->getData('customer_id')) {

            return $this->customerSession->getData('customer_id');
        } else {
            return "guest";
        }
    }
    /**
     * This return getLoadProduct value
     *
     * @param int $id Product Id
     * @return object
     */
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
    /**
     * This return getCategoryNameById value
     *
     * @param int $id category id
     * @param null|int $storeId
     * @return string
     */
    protected function getCategoryNameById($id, $storeId = null)
    {
        $categoryInstance = $this->categoryRepository->get($id, $storeId);

        return $categoryInstance->getName();
    }

    /**
     * This returns getWebStoreId value
     *
     * @return int
     */
    public function getWebStoreId()
    {
        $webStoreId = $this->_scopeConfig->getValue(
            'convertopia_general/convertopia_store_settings/store_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        return $webStoreId;
    }
    /**
     * This return cartView value
     *
     * @return json Array
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

        return (json_encode($main_cart_arr));
    }
    /**
     * This return getCustomer value
     *
     * @param int $customerId
     * @return object
     */
    public function getCustomer($customerId)
    {
        $customer = $this->_customers->load($customerId);
    }
    /**
     * This return getShippingDetails value
     *
     * @return json array
     */
    public function getShippingDetails()
    {
        $shippingAddress = $this->cart->getQuote()->getShippingAddress();
        // $main_shipping_arr = [];
        $shipping_arr = [];
        $shipping_arr['first_name'] = $shippingAddress->getFirstname('firstname');
        $shipping_arr['last_name'] = $shippingAddress->getLastname('lastname');
        $shipping_arr['address1'] = $shippingAddress->getData('street');
        $shipping_arr['city'] = $shippingAddress->getData('city');
        $shipping_arr['state'] = $shippingAddress->getData('region');
        $shipping_arr['zip'] = $shippingAddress->getData('postcode');
        $shipping_arr['country'] = $shippingAddress->getData('country_id');
        // $shippingMethod = $shippingAddress->getShippingMethod();

        return json_encode($shipping_arr);
        // return $main_shipping_arr;
    }
    /**
     * This return getHashedEmail value
     *
     * @param string $email
     * @return string
     */
    public function getHashedEmail($email)
    {
        try {
            $customer = $this->customerRepository->get($email);
            return $customer->getEmail();
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }
    /**
     * This return getOrderTotal value
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
     * This return getCurrentCurrencyCode value
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }
    /**
     * This return getSessionId value
     *
     * @return string
     */
    public function getSessionId()
    {
        try {
            $sessionId = $this->_checkoutSession->create()->getQuote();
            if ($this->customerSession->getData('customer_id')) {
                return $this->customerSession->getCustomer()->getEmail();
            } else {
                $maskedId = null;
                try {
                    $maskedId = $this->quoteIdToMaskedQuoteId->execute((int)$sessionId->getId());
                    return $maskedId;
                } catch (NoSuchEntityException $exception) {
                    return null;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
    /**
     * This return getPageView value
     *
     * @return string
     */
    public function getPageView()
    {
        $current_view = $this->_request->getFullActionName();
        $page_view = [
            'cms_index_index' => 'HOME',
            'catalog_category_view' => 'PLP',
            'catalog_product_view' => 'PDP',
            'checkout_cart_index' => 'CART',
            'customer_account_login' => 'ACCOUNT_LOGIN',
            'customer_account_create' => 'ACCOUNT_SIGNUP',
            'customer_account_index' => 'ACCOUNT_DASHBORD',
            'checkout_index_index' => 'CHECKOUT_SHIPPING',
            'checkout_onepage_success' => 'ORDER_CONFIRMATION',
        ];
        if (isset($page_view[$current_view])) {
            return $page_view[$current_view];
        }
        return $current_view;
    }
}
